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
$mysql_dataname2 = 'mg_lt';

$mysql_server_name3 = '120.25.107.206';//记录的链接
$mysql_username3 = 'toodo';
$mysql_password3 = 'toodo1815';
$mysql_dataname3 = 'mg_lt';

$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error Database link failure!!!");
mysqli_query($conn,"set character set 'utf8'");
mysqli_query($conn,"set names 'utf8'");
mysqli_select_db($conn,$mysql_dataname);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d H:i:s");
$createShowtime = date("Ym");
$read_table = "tdo_user_addresses";//读取的表名
$table_field = "created_at";//读取的字段名称
$table_field1 = "updated_at";//读取的字段名称

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
$startTime = date("Y-m-d H:i:s",strtotime('-1 hour'));//开始日期选择
$stopTime = date("Y-m-d H:i:s");//结束日期选择
//$startTime = date("2018-01-29 00:00:00");
//$stopTime = date("2018-02-02 23:59:59");


$sql = "select * from $read_table WHERE $table_field between '$startTime' AND '$stopTime'";
$updatedSql = "select * from $read_table WHERE $table_field1 between '$startTime' AND '$stopTime'";

$result = mysqli_query($conn,$sql);
$updatedResult = mysqli_query($conn,$updatedSql);

$txtFileName = "/toodo/crontab/txt/tdoUserAddresses.txt";
$TxtRes = fopen($txtFileName, "w");

while ($row = mysqli_fetch_array($result)) {//----------------根据表字段数填写
    fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] ."#" . $row[4] ."#" . $row[5] ."#" . $row[6] ."#" . $row[7] ."#" . $row[8] . "\r" . "\n");
}
fclose($TxtRes);
//mysqli_close($conn);


$conn1 = mysqli_connect($mysql_server_name2, $mysql_username2, $mysql_password2) or die("error Database link failure!!!");
mysqli_query($conn1,"set character set 'utf8'");
mysqli_query($conn1,"set names 'utf8'");
mysqli_select_db($conn1,$mysql_dataname2);

$data = fopen($txtFileName, 'r');
date_default_timezone_set("Asia/Chongqing");
$create_table_name = "tdo_user_addresses";//创建的表名

function updatedAddresses($updatedResult,$create_table_name,$conn1,$field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9){
    while ($row =  mysqli_fetch_array($updatedResult)) {
        $updatedAddresses = "update $create_table_name set $field1 = '$row[0]',$field2 = '$row[1]',$field3 = '$row[2]',$field4 = '$row[3]',$field5 = '$row[4]',$field6 = '$row[5]',$field7 = '$row[6]',$field8 = '$row[7]',$field9 = '$row[8]' WHERE $field1 = '$row[0]'";

        mysqli_query($conn1,$updatedAddresses);
    }
}
if(mysqli_num_rows(mysqli_query($conn1,"SHOW TABLES LIKE '". $create_table_name."'"))==1) {

    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $create_table_name($field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9) values ('$lines')";
        mysqli_query($conn1,$lists);

    }
    fclose($data);
    updatedAddresses($updatedResult,$create_table_name,$conn1,$field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9);
    mysqli_close($conn1);

} else {
    $createtable = "create table $create_table_name($field1 int(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,$field2 int(10),$field3 int(10),$field4 varchar(255),$field5 varchar(255),$field6 varchar(255),$field7 tinyint(1),$field8 datetime,$field9 datetime) default charset=utf8";
    mysqli_query($conn1,$createtable);

    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $create_table_name($field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9) values ('$lines')";
        mysqli_query($conn1,$lists);
    }
    fclose($data);
    updatedAddresses($updatedResult,$create_table_name,$conn1,$field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9);
    mysqli_close($conn1);
}



$conn = mysqli_connect($mysql_server_name3, $mysql_username3, $mysql_password3) or die("error Database link failure!!!");
mysqli_query($conn,"set character set 'utf8'");//读库
mysqli_query($conn,"set names 'utf8'");
mysqli_select_db($conn,$mysql_dataname3);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d H:i:s");
$createShowtime = date("Ym");
$createTableDate = 'phpLoopRecords_tdo_user_addresses';

function WriteData ($createTableDate,$insertShowtime,$conn){
    $insertData = "insert into $createTableDate(createDate,completionStatus) values ('$insertShowtime','1')";
    mysqli_query($conn,$insertData);
    mysqli_close($conn);
    echo '----  GOOD';
}

if(mysqli_num_rows(mysqli_query($conn,"SHOW TABLES LIKE '". $createTableDate."'"))==1) {

    WriteData ($createTableDate,$insertShowtime,$conn);

}else{

    $createTable = "create table $createTableDate(id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,createDate datetime,completionStatus int(11))";
    mysqli_query($conn,$createTable);

    WriteData ($createTableDate,$insertShowtime,$conn);

}


