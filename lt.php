<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9 0009
 * Time: 上午 9:37
 */
//header("Content-Type: text/html;charset=utf-8");
$mysql_server_name = '120.25.107.206';//读取的链接
$mysql_username = 'toodo';
$mysql_password = 'toodo1815';
$mysql_dataname = 'mg_gd';

$mysql_server_name2 = '120.25.107.206';//写入的链接
$mysql_username2 = 'toodo';
$mysql_password2 = 'toodo1815';
$mysql_dataname2 = 'mg_lt';

$param = array();

if ($argc > 1) {
    parse_str ( $argv [1], $param );
    foreach ( $param as $k => $v ) {
        $param[$k] = $v;
    }
}

$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error Database link failure!!!");
mysqli_query($conn,"set character set 'utf8'");//读库
mysqli_query($conn,"set names 'utf8'");
mysqli_select_db($conn,$mysql_dataname);

date_default_timezone_set("Asia/Chongqing");


$table_time1 = "orderdata";//读取的表名--------需要修改
//$startTime = date("");//开始日期选择例如 "2017-11-11 23:59:59"--------需要修改
//$stopTime = date("");//结束日期选择例如 "2017-11-11 23:59:59"--------需要修改
$startTime = $param['start'];//开始日期选择例如 "2017-11-11 23:59:59"
$stopTime = $param['stop'];//结束日期选择例如 "2017-11-11 23:59:59"


$sql = "select * from $table_time1 WHERE CreateDate between '$startTime' AND '$stopTime'";

$result = mysqli_query($conn,$sql);

$txtFileName = "D:\/web\/txt\/ltOrderData.txt";
$TxtRes = fopen($txtFileName, "w");

if (!is_writable($txtFileName)) {
    echo "Error: File does not exist";
    exit();
}

while ($row = mysqli_fetch_array($result)) {
    fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] . "#" . $row[4] . "#" . $row[5] . "#" . $row[6] . "#" . $row[7] . "#" . $row[8] . "#" . $row[9] . "#" . $row[10] . "#" . $row[11] . "#" . $row[12] . "\n");
}
fclose($TxtRes);
mysqli_close($conn);

$conn = mysqli_connect($mysql_server_name2, $mysql_username2, $mysql_password2) or die("error Database link failure!!!");
mysqli_query($conn,"set character set 'utf8'");//读库
mysqli_query($conn,"set names 'utf8'");
mysqli_select_db($conn,$mysql_dataname2);


$data = fopen($txtFileName, 'r');
date_default_timezone_set("Asia/Chongqing");
$table_time = "orderdata";//创建的表名--------需要修改

function writeData($data, $table_time, $conn)
{
    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $table_time(Id,OrderNo,Amount,SerialNumber,PassportId,GameId,PayStatus,Signature,CreateDate,DeviceId,UserId,ShopId,ExpiryDate) values ('$lines')";
        mysqli_query($conn,$lists);
    }
}
if(mysqli_num_rows(mysqli_query($conn,"SHOW TABLES LIKE '". $table_time."'"))==1) {
    writeData($data, $table_time, $conn);
    fclose($data);
    mysqli_close($conn);
    echo 'GOOD';
} else {
    $createtable = "create table $table_time(Id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,OrderNo varchar(255),Amount decimal(18,4),SerialNumber varchar(255),PassportId VARCHAR(255),GameId int(11),PayStatus int(11),Signature VARCHAR(255),CreateDate datetime,DeviceId VARCHAR(255),UserId int(11),ShopId int(11),ExpiryDate int(11))";
    mysqli_query($conn,$createtable);
    writeData($data, $table_time, $conn);
    fclose($data);
    mysqli_close($conn);
    echo 'GOOD';
}

