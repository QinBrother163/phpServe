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

$mysql_server_name2 = '120.25.107.206';//写入的链接
$mysql_username2 = 'toodo';
$mysql_password2 = 'toodo1815';
$mysql_dataname2 = 'mg_gxgd';

$mysql_server_name3 = '120.25.107.206';//记录的链接
$mysql_username3 = 'toodo';
$mysql_password3 = 'toodo1815';
$mysql_dataname3 = 'mg_gxgd';

$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error Database link failure!!!");
mysqli_query($conn,"set character set 'utf8'");
mysqli_query($conn,"set names 'utf8'");
mysqli_select_db($conn,$mysql_dataname);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d H:i:s");
$createShowtime = date("Ym");
$read_table = "tdo_service_datas";//读取的表名
$table_field = "created_at";//读取的字段名称

//---------------->    写入的字段
$field1 = 'serialNo';
$field2 = 'userId';
$field3 = 'retailId';
$field4 = 'productId';
$field5 = 'goodsName';
$field6 = 'beginTime';
$field7 = 'endTime';
$field8 = 'created_at';
$field9 = 'updated_at';
$field10 = 'tradeNo';
$field11 = 'own';
$field12 = 'ownTime';
//----------------<
$startTime = date("Y-m-d H:i:s",strtotime('-1 hour'));//开始日期选择
$stopTime = date("Y-m-d H:i:s");//结束日期选择
//$startTime = date("2017-12-25 00:00:00");
//$stopTime = date("2017-12-28 23:59:59");


$sql = "select * from $read_table WHERE $table_field between '$startTime' AND '$stopTime'";

$result = mysqli_query($conn,$sql);

$txtFileName = "/toodo/crontab/txt/tdoServiceDatas.txt";
$TxtRes = fopen($txtFileName, "w");

while ($row = mysqli_fetch_array($result)) {//----------------根据表字段数填写
    if ($row[11] == "" || $row[9] == ""){
        $row[11] = null;
        $row[9] = null;
        fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] ."#" . $row[4] ."#" . $row[5] ."#" . $row[6] ."#" . $row[7] ."#" . $row[8] ."#" . $row[9] ."#" . $row[10] ."#" . $row[11] . "\r" . "\n");
    }else{
        fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] ."#" . $row[4] ."#" . $row[5] ."#" . $row[6] ."#" . $row[7] ."#" . $row[8] ."#" . $row[9] ."#" . $row[10] ."#" . $row[11] . "\r" . "\n");
    }
}
fclose($TxtRes);
mysqli_close($conn);


$conn = mysqli_connect($mysql_server_name2, $mysql_username2, $mysql_password2) or die("error Database link failure!!!");
mysqli_query($conn,"set character set 'utf8'");
mysqli_query($conn,"set names 'utf8'");
mysqli_select_db($conn,$mysql_dataname2);

$data = fopen($txtFileName, 'r');
date_default_timezone_set("Asia/Chongqing");
$create_table_name = "tdo_service_datas";//创建的表名

if(mysqli_num_rows(mysqli_query($conn,"SHOW TABLES LIKE '". $create_table_name."'"))==1) {

    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $create_table_name($field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9,$field10,$field11,$field12) values ('$lines')";
        mysqli_query($conn,$lists);

    }
    fclose($data);
    mysqli_close($conn);

} else {
    $createtable = "create table $create_table_name($field1 varchar(255) PRIMARY KEY,$field2 int(10),$field3 varchar(255),$field4 int(10),$field5 varchar(255),$field6 datetime,$field7 datetime,$field8 timestamp,$field9 timestamp,$field10 varchar(255),$field11 tinyint(1),$field12 timestamp) default charset=utf8";
    mysqli_query($conn,$createtable);

    while (!feof($data)) {
        $line = fgets($data);
        $lines = join("','", explode('#', $line));
        $lists = "insert into $create_table_name($field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9,$field10,$field11,$field12) values ('$lines')";
        mysqli_query($conn,$lists);
    }
    mysqli_close($conn);
    fclose($data);
}



$conn = mysqli_connect($mysql_server_name3, $mysql_username3, $mysql_password3) or die("error Database link failure!!!");
mysqli_query($conn,"set character set 'utf8'");//读库
mysqli_query($conn,"set names 'utf8'");
mysqli_select_db($conn,$mysql_dataname3);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d H:i:s");
$createShowtime = date("Ym");
$createTableDate = 'phpLoopRecords_tdo_service_datas';

function WriteData ($createTableDate,$insertShowtime,$conn){
    $insertData = "insert into $createTableDate(createDate,completionStatus) values ('$insertShowtime','1')";
    mysqli_query($conn,$insertData);
    mysqli_close($conn);
    echo 'GOOD';
}

if(mysqli_num_rows(mysqli_query($conn,"SHOW TABLES LIKE '". $createTableDate."'"))==1) {

    WriteData ($createTableDate,$insertShowtime,$conn);

}else{

    $createTable = "create table $createTableDate(id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,createDate datetime,completionStatus int(11))";
    mysqli_query($conn,$createTable);

    WriteData ($createTableDate,$insertShowtime,$conn);

}
