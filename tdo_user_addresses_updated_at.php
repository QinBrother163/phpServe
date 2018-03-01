<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9 0009
 * Time: 上午 9:37
 */
//header("Content-Type: text/html;charset=utf-8");
$mysql_server_name = '127.0.0.1';//读取的链接
$mysql_username = 'root';
$mysql_password = 'toodo1814';
$mysql_dataname = 'toodo_service';

//$mysql_server_name = '120.25.107.206';//读取的链接
//$mysql_username = 'toodo';
//$mysql_password = 'toodo1815';
//$mysql_dataname = 'tdsrv.unicom';

$mysql_server_name2 = '120.25.107.206';//写入的链接
$mysql_username2 = 'toodo';
$mysql_password2 = 'toodo1815';
//$mysql_dataname2 = 'mg_lt';
$mysql_dataname2 = 'tdbak.unicom';

$mysql_server_name3 = '120.25.107.206';//记录的链接
$mysql_username3 = 'toodo';
$mysql_password3 = 'toodo1815';
//$mysql_dataname3 = 'mg_lt';
$mysql_dataname3 = 'tdbak.unicom';

$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error Database link failure!!!");
mysqli_query($conn, "set character set 'utf8'");
mysqli_query($conn, "set names 'utf8'");
mysqli_select_db($conn, $mysql_dataname);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d H:i:s");
$createShowtime = date("Ym");
$read_table = "tdo_user_addresses";//读取的表名
$table_field = "updated_at";//读取的字段名称

//---------------->    写入的字段
$field1 = 'id';
$field2 = 'userId';
$field3 = 'retailId';
$field4 = 'name';
$field5 = 'phone';
$field6 = 'address';
$field7 = 'workday';
$field8 = 'created_at';
$field9 = 'updated_at';
//----------------<

$YMD = date("Y-m-d");
$H = date("H");
$IS = ":00:00";
$YMDHIS = $YMD . " " . $H . $IS;

$startTime = date("Y-m-d H:i:s", strtotime('-1 hour', strtotime($YMDHIS)));//开始日期选择
$stopTime = date("Y-m-d H:i:s", strtotime($YMDHIS));//结束日期选择
//$startTime = date("2018-01-29 00:00:00");
//$stopTime = date("2018-02-02 23:59:59");


$sql = "select * from $read_table WHERE $table_field between '$startTime' AND '$stopTime'";
$updatedSql = "select * from $read_table WHERE $table_field between '$startTime' AND '$stopTime'";

$result = mysqli_query($conn, $sql);
$updatedResult = mysqli_query($conn, $updatedSql);

$txtFileName = "/toodo/crontab/txt/tdoUserAddresses.txt";
//$txtFileName = "E:/LJW/LTDC/txt/tdoUserAddresses.txt";
$TxtRes = fopen($txtFileName, "w");

while ($row = mysqli_fetch_array($result)) {//----------------根据表字段数填写
    fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] . "#" . $row[4] . "#" . $row[5] . "#" . $row[6] . "#" . $row[7] . "#" . $row[8] . "\r" . "\n");
}
fclose($TxtRes);
//mysqli_close($conn);


$conn1 = mysqli_connect($mysql_server_name2, $mysql_username2, $mysql_password2) or die("error Database link failure!!!");
mysqli_query($conn1, "set character set 'utf8'");
mysqli_query($conn1, "set names 'utf8'");
mysqli_select_db($conn1, $mysql_dataname2);

$data = fopen($txtFileName, 'r');
date_default_timezone_set("Asia/Chongqing");
$create_table_name = "tdo_user_addresses";//创建的表名

function updatedAddresses($conn, $stopTime, $startTime, $updatedResult, $create_table_name, $conn1, $field1, $field2, $field3, $field4, $field5, $field6, $field7, $field8, $field9)
{


    $sql = "select * from tdo_user_addresses WHERE updated_at between '$startTime' AND '$stopTime'";
    $insertResult = mysqli_query($conn, $sql);

    $performState = false;
    $arrCreateData = array();
    $arrUpdatedData = array();

    while ($val = mysqli_fetch_array($insertResult)) {
        array_push($arrCreateData, $val[7]);
        array_push($arrUpdatedData, $val[8]);
    }

    for ($i = 0; $i < sizeof($arrUpdatedData); $i++) {
        for ($j = 0; $j < sizeof($arrCreateData); $j++) {
            if (strtotime($arrCreateData[$j]) != strtotime($arrUpdatedData[$i])) {
                $performState = true;
            }
        }
    }
    if ($performState == true) {
        while ($row = mysqli_fetch_array($updatedResult)) {

            $updatedAddresses = "update $create_table_name set $field1 = '$row[0]',$field2 = '$row[1]',$field3 = '$row[2]',$field4 = '$row[3]',$field5 = '$row[4]',$field6 = '$row[5]',$field7 = '$row[6]',$field8 = '$row[7]',$field9 = '$row[8]' WHERE $field1 = '$row[0]'";
            mysqli_query($conn1, $updatedAddresses);
        }
    }
}

if (mysqli_num_rows(mysqli_query($conn1, "SHOW TABLES LIKE '" . $create_table_name . "'")) == 1) {

    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $create_table_name($field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9) values ('$lines')";
        mysqli_query($conn1, $lists);

    }
    fclose($data);
    updatedAddresses($conn, $stopTime, $startTime, $updatedResult, $create_table_name, $conn1, $field1, $field2, $field3, $field4, $field5, $field6, $field7, $field8, $field9);
    mysqli_close($conn1);

} else {
    $createtable = "create table $create_table_name($field1 int(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,$field2 int(10),$field3 int(10),$field4 varchar(255),$field5 varchar(255),$field6 varchar(255),$field7 tinyint(1),$field8 datetime,$field9 datetime) default charset=utf8";
    mysqli_query($conn1, $createtable);

    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $create_table_name($field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9) values ('$lines')";
        mysqli_query($conn1, $lists);
    }
    fclose($data);
    updatedAddresses($conn, $stopTime, $startTime, $updatedResult, $create_table_name, $conn1, $field1, $field2, $field3, $field4, $field5, $field6, $field7, $field8, $field9);
    mysqli_close($conn1);
}


$conn3 = mysqli_connect($mysql_server_name3, $mysql_username3, $mysql_password3) or die("error Database link failure!!!");
mysqli_query($conn3, "set character set 'utf8'");//读库
mysqli_query($conn3, "set names 'utf8'");
mysqli_select_db($conn3, $mysql_dataname3);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d H:i:s");
$createShowtime = date("Ymd");
$createTableDate = 'phpLoopRecords_tdo_user_addresses';
$locH = date("H");
function WriteData($createTableDate, $conn)
{
//当前日期
    $nowDate = date("Y-m-d");
//当前小时
    $nowHour = date("H");
//昨天日期
    $locTime = date("Y-m-d", strtotime('-1 day', strtotime($nowDate)));
// 操作日期
    $data = "";
    if ($nowHour == 0) {
        $data = $locTime;
        $sqlYMD = "select * from $createTableDate WHERE createDate = '$locTime'";
    } else {
        $data = $nowDate;
        $sqlYMD = "select * from $createTableDate WHERE createDate = '$nowDate'";
    }
    $result = mysqli_query($conn, $sqlYMD);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);

    if (!mysqli_num_rows($result)) {
        //创建
        $insertData = "insert into $createTableDate(createDate,completionStatus) values ('$data','')";
        mysqli_query($conn, $insertData);

        if ($data == $locTime) {
            fellZero(24, 0, "", $createTableDate, $data, $conn);
        } else {
            fellZero($nowHour, 0, "", $createTableDate, $data, $conn);
        }
    } else {
        if ($data == $locTime) {
            $nowHour = 24;
        }
        $indexLen = strlen($row['completionStatus']);
        fellZero($nowHour, $indexLen, $row['completionStatus'], $createTableDate, $data, $conn);
    }
}

function fellZero($hour, $size, $contion, $createTableDate, $createDate, $conn)
{
    if ($hour != $size) {
        $zeroNum = $hour - $size - 1;
        $arr3 = array();
        for ($i = 0; $i < $zeroNum; $i++) {
            array_push($arr3, '0');
        }
        $str3 = implode("", $arr3);
        $strS = $contion . $str3 . '1';

        $insertData = "update $createTableDate set completionStatus = '$strS' WHERE createDate = '$createDate'";
        mysqli_query($conn, $insertData);
    }
}

if (mysqli_num_rows(mysqli_query($conn3, "SHOW TABLES LIKE '" . $createTableDate . "'")) == 1) {

    WriteData($createTableDate, $conn3);

} else {

    $createTable = "create table $createTableDate(id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,createDate date,completionStatus text)";
    mysqli_query($conn3, $createTable);

    WriteData($createTableDate, $conn3);

}


