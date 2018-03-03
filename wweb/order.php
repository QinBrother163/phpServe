<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/9 0009
 * Time: 上午 9:37
 */
//header("Content-Type: text/html;charset=utf-8");
$mysql_server_name = '172.16.147.103';//读取的链接
$mysql_username = 'toodo';
$mysql_password = 'toodo1815';
$mysql_dataname = 'order_list_1_91';

$mysql_server_name2 = '120.25.107.206';//写入的链接
$mysql_username2 = 'toodo';
$mysql_password2 = 'toodo1815';
$mysql_dataname2 = 'mg_gd';

$param = array();

if ($argc > 1) {
    parse_str($argv [1], $param);
    foreach ($param as $k => $v) {
        $param[$k] = $v;
    }
}

$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error Database link failure!!!");
mysqli_query($conn, "set character set 'utf8'");//读库
mysqli_query($conn, "set names 'utf8'");
mysqli_select_db($conn, $mysql_dataname);

date_default_timezone_set("Asia/Chongqing");

$table_time1 = "order_" . date("Ym");//读取的表名
//$startTime = date("Y-m-d H:i:s",strtotime('-1 hour'));//开始日期选择
//$stopTime = date("Y-m-d H:i:s");//结束日期选择
$startTime = $param['start'];//开始日期选择例如 "2017-11-11 23:59:59"
$stopTime = $param['stop'];//结束日期选择例如 "2017-11-11 23:59:59"

$sql = "select * from $table_time1 WHERE date between '$startTime' AND '$stopTime'";

$result = mysqli_query($conn, $sql);

$txtFileName = "D:\/wweb\/txt\/order.txt";
$TxtRes = fopen($txtFileName, "w");

if (!is_writable($txtFileName)) {
    echo "Error: File does not exist";
    exit();
}

while ($row = mysqli_fetch_array($result)) {
    fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] . "#" . $row[4] . "#" . $row[5] . "#" . $row[6] . "#" . $row[7] . "#" . $row[8] . "#" . $row[9] . "#" . $row[10] . "#" . $row[11] . "#" . $row[12] . "#" . $row[13] . "\n");
}
fclose($TxtRes);
mysqli_close($conn);

$conn = mysqli_connect($mysql_server_name2, $mysql_username2, $mysql_password2) or die("error Database link failure!!!");
mysqli_query($conn, "set character set 'utf8'");//读库
mysqli_query($conn, "set names 'utf8'");
mysqli_select_db($conn, $mysql_dataname2);

$data = fopen($txtFileName, 'r');
date_default_timezone_set("Asia/Chongqing");
$table_time = "order_" . date("Ym");//创建的表名

function writeData($data, $table_time, $conn)
{
    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $table_time(order_id,user_id,app_type,app_id,currency_type,fee,date,state,event,title,real_name,phone,address,update_time) values ('$lines')";
        mysqli_query($conn, $lists);
    }
}

if (mysqli_num_rows(mysqli_query($conn, "SHOW TABLES LIKE '" . $table_time . "'")) == 1) {

    writeData($data, $table_time, $conn);
    fclose($data);
    mysqli_close($conn);
    echo 'GOOD';

} else {

    $createtable = "create table $table_time(order_id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,user_id int(11),app_type smallint(6),app_id int(11),currency_type smallint(6),fee int(11),date datetime,state smallint(6),event smallint(6),title VARCHAR(128),real_name varchar(16),phone varchar(16),address varchar(256),update_time datetime)";
    mysqli_query($conn, $createtable);
    writeData($data, $table_time, $conn);
    fclose($data);
    mysqli_close($conn);
    echo 'GOOD';
}

