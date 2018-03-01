<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9 0009
 * Time: 上午 9:37
 */
//header("Content-Type: text/html;charset=utf-8");
$mysql_server_name = '172.16.147.104';
$mysql_username = 'toodo';
$mysql_password = 'toodo1815';
$mysql_dataname = 'fsdp_activity_91';

$mysql_server_name2 = '120.25.107.206';
$mysql_username2 = 'toodo';
$mysql_password2 = 'toodo1815';
$mysql_dataname2 = 'mg_gd';

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

$table_time = "list_" . date("Ym");//数据库表的选择
//$startTime = date("");//开始日期选择例如 "2017-11-11 23:59:59"
//$stopTime = date("");//结束日期选择例如 "2017-11-11 23:59:59"
$startTime = $param['start'];//开始日期选择例如 "2017-11-11 23:59:59"
$stopTime = $param['stop'];//结束日期选择例如 "2017-11-11 23:59:59"
$txtFileName = "D:\/wweb\/txt\/fsdp_activity.txt";//中转站：数据文件

if (!is_writable($txtFileName)) {
    echo "Error: File does not exist";
    exit();
}

$sql = "select * from $table_time WHERE createDate between '$startTime' AND '$stopTime'";

$result = mysqli_query($conn,$sql);

$TxtRes = fopen($txtFileName, "w");

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
$table_time2 = "fsdp_activity_list_".date("Ym");//写入数据库的表

function writeData($data, $table_time2, $conn)
{
    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $table_time2(id,createDate,Flags,resultCode,orderID,needCnfm,custid,spID,devType,devNO,CARegionCode,serviceid,streamingNO) values ('$lines')";
        mysqli_query($conn,$lists);
    }
}
if(mysqli_num_rows(mysqli_query($conn,"SHOW TABLES LIKE '". $table_time2."'"))==1) {

    writeData($data, $table_time2, $conn);
    fclose($data);
    mysqli_close($conn);
    echo 'GOOD';

} else {
    $createtable = "create table $table_time2(id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,createDate datetime,Flags int(4),resultCode int(4),orderID VARCHAR(50),needCnfm int(4),custid varchar(30),spID VARCHAR(10),devType VARCHAR(3),devNO VARCHAR(20),CARegionCode VARCHAR(50),serviceid VARCHAR(40),streamingNO VARCHAR(20))";
    mysqli_query($conn,$createtable);
    writeData($data, $table_time2, $conn);
    fclose($data);
    mysqli_close($conn);
    echo 'GOOD';
}


