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


//---------------->    写入的字段
$field1 = 'tradeNo';
$field2 = 'retailId';
$field3 = 'orderNo';
$field4 = 'userId';
$field5 = 'storeId';
$field6 = 'storeName';
$field7 = 'totalAmount';
$field8 = 'subject';
$field9 = 'body';
$field10 = 'goodsDetail';
$field11 = 'extendParams';
$field12 = 'extUserInfo';
$field13 = 'payTimeout';
$field14 = 'payAmount';
$field15 = 'receiptAmount';
$field16 = 'serialNo';
$field17 = 'tradeStatus';
$field18 = 'payTime';
$field19 = 'sendPayTime';
$field20 = 'created_at';
$field21 = 'updated_at';
//----------------<

$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error Database link failure!!!");
mysqli_query($conn, "set character set 'utf8'");
mysqli_query($conn, "set names 'utf8'");
mysqli_select_db($conn, $mysql_dataname);
//
date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d H:i:s");
$createShowtime = date("Ym");
$read_table = "tdo_order_datas_logs";//读取的表名
$table_field = "updated_at";//读取的字段名称


$YMD = date("Y-m-d");
$H = date("H");
$IS = ":00:00";
$YMDHIS = $YMD . " " . $H . $IS;


$startTime = date("Y-m-d H:i:s", strtotime('-1 hour', strtotime($YMDHIS)));//开始日期选择
$stopTime = date("Y-m-d H:i:s", strtotime($YMDHIS));//结束日期选择
//$startTime = date("2018-02-01 00:00:00");
//$stopTime = date("2018-02-01 15:11:39");


$sql = "select * from $read_table WHERE $table_field between '$startTime' AND '$stopTime'";

$updatedSql = "select * from $read_table WHERE $table_field between '$startTime' AND '$stopTime'";


$result = mysqli_query($conn, $sql);
$updatedResult1 = mysqli_query($conn, $updatedSql);


$txtFileName = "/toodo/crontab/txt/tdoOrderDatasLogs.txt";
//$txtFileName = "E:/LJW/LTDC/txt/tdoOrderDatasLogs.txt";
$TxtRes = fopen($txtFileName, "w");

while ($row = mysqli_fetch_array($result)) {//----------------根据表字段数填写
    fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] . "#" . $row[4] . "#" . $row[5] . "#" . $row[6] . "#" . $row[7] . "#" . $row[8] . "#" . $row[9] . "#" . $row[10] . "#" . $row[11] . "#" . $row[12] . "#" . $row[13] . "#" . $row[14] . "#" . $row[15] . "#" . $row[16] . "#" . $row[17] . "#" . $row[18] . "#" . $row[19] . "#" . $row[20] . "\r" . "\n");
}
fclose($TxtRes);


$conn1 = mysqli_connect($mysql_server_name2, $mysql_username2, $mysql_password2) or die("error Database link failure!!!");
mysqli_query($conn1, "set character set 'utf8'");
mysqli_query($conn1, "set names 'utf8'");
mysqli_select_db($conn1, $mysql_dataname2);

$data = fopen($txtFileName, 'r');
$create_table_name = "tdo_order_datas_logs";//创建的表名


function updatedOrder($conn, $stopTime, $startTime, $updatedResult1, $create_table_name, $conn1, $field1, $field2, $field3, $field4, $field5, $field6, $field7, $field8, $field9, $field10, $field11, $field12, $field13, $field14, $field15, $field16, $field17, $field18, $field19, $field20, $field21)
{

    $sql = "select * from tdo_order_datas_logs WHERE updated_at between '$startTime' AND '$stopTime'";
    $insertResult = mysqli_query($conn, $sql);

    $performState = false;
    $arrCreateData = array();
    $arrUpdatedData = array();

    while ($val = mysqli_fetch_array($insertResult)) {
        array_push($arrCreateData, $val[19]);
        array_push($arrUpdatedData, $val[20]);
    }

    for ($i = 0; $i < sizeof($arrUpdatedData); $i++) {
        for ($j = 0; $j < sizeof($arrCreateData); $j++) {
            if (strtotime($arrCreateData[$j]) != strtotime($arrUpdatedData[$i])) {
                $performState = true;
            }
        }
    }
    if ($performState == true) {
        while ($row = mysqli_fetch_array($updatedResult1)) {

            $updatedOrder = "update $create_table_name set $field1 = '$row[0]', $field2 = '$row[1]', $field3 = '$row[2]', $field4 = '$row[3]', $field5 = '$row[4]', $field6 = '$row[5]', $field7 = '$row[6]', $field8 = '$row[7]', $field9 = '$row[8]', $field10 = '$row[9]', $field11 = '$row[10]', $field12 = '$row[11]', $field13 = '$row[12]', $field14 = '$row[13]', $field15 = '$row[14]', $field16 = '$row[15]', $field17 = '$row[16]', $field18 = '$row[17]', $field19 = '$row[18]', $field20 = '$row[19]', $field21 = '$row[20]' WHERE $field1 = '$row[0]'";
            mysqli_query($conn1, $updatedOrder);
        }
    }
}

if (mysqli_num_rows(mysqli_query($conn1, "SHOW TABLES LIKE '" . $create_table_name . "'")) == 1) {

    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $create_table_name($field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9,$field10,$field11,$field12,$field13,$field14,$field15,$field16,$field17,$field18,$field19,$field20,$field21) values ('$lines')";
        mysqli_query($conn1, $lists);
    }
    fclose($data);
    updatedOrder($conn, $stopTime, $startTime, $updatedResult1, $create_table_name, $conn1, $field1, $field2, $field3, $field4, $field5, $field6, $field7, $field8, $field9, $field10, $field11, $field12, $field13, $field14, $field15, $field16, $field17, $field18, $field19, $field20, $field21);

    mysqli_close($conn1);

} else {
    $createtable = "create table $create_table_name($field1 varchar(255) PRIMARY KEY,$field2 varchar(255),$field3 varchar(255),$field4 int(10),$field5 int(11),$field6 varchar(255),$field7 int(11),$field8 varchar(255),$field9 varchar(255),$field10 text,$field11 text,$field12 text,$field13 datetime,$field14 int(11),$field15 int(11),$field16 VARCHAR(255),$field17 int(11),$field18 datetime,$field19 datetime,$field20 timestamp,$field21 timestamp) default charset=utf8";
    mysqli_query($conn1, $createtable);

    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $create_table_name($field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9,$field10,$field11,$field12,$field13,$field14,$field15,$field16,$field17,$field18,$field19,$field20,$field21) values ('$lines')";
        mysqli_query($conn1, $lists);
    }
    fclose($data);
    updatedOrder($conn, $stopTime, $startTime, $updatedResult1, $create_table_name, $conn1, $field1, $field2, $field3, $field4, $field5, $field6, $field7, $field8, $field9, $field10, $field11, $field12, $field13, $field14, $field15, $field16, $field17, $field18, $field19, $field20, $field21);
    mysqli_close($conn1);

}

$conn3 = mysqli_connect($mysql_server_name3, $mysql_username3, $mysql_password3) or die("error Database link failure!!!");
mysqli_query($conn3, "set character set 'utf8'");//读库
mysqli_query($conn3, "set names 'utf8'");
mysqli_select_db($conn3, $mysql_dataname3);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d");
$createShowtime = date("Ymd");
$locH = date("H");
$createTableDate = 'phpLoopRecords_tdo_order_datas_logs';
echo("***");

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
mysqli_close($conn3);
