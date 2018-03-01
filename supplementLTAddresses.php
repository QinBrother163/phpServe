<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/10
 * Time: 11:33
 */
$mysql_server_name = '120.25.107.206';//读取的链接
$mysql_username = 'toodo';
$mysql_password = 'toodo1815';
$mysql_dataname = 'tdsrv.unicom';

$mysql_server_name2 = '120.25.107.206';//写入的链接
$mysql_username2 = 'toodo';
$mysql_password2 = 'toodo1815';
//$mysql_dataname2 = 'mg_lt';
$mysql_dataname2 = 'tdbak.unicom';


//----------------------------------------------->    写入的字段
$field1 = 'id';
$field2 = 'userId';
$field3 = 'retailId';
$field4 = 'name';
$field5 = 'phone';
$field6 = 'address';
$field7 = 'workday';
$field8 = 'created_at';
$field9 = 'updated_at';
//-----------------------------------------------<

$conn2 = mysqli_connect($mysql_server_name2, $mysql_username2, $mysql_password2) or die("error Database link failure!!!");
mysqli_query($conn2, "set character set 'utf8'");
mysqli_query($conn2, "set names 'utf8'");
mysqli_select_db($conn2, $mysql_dataname2);


$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error Database link failure!!!");
mysqli_query($conn, "set character set 'utf8'");
mysqli_query($conn, "set names 'utf8'");
mysqli_select_db($conn, $mysql_dataname);


date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d");

$recordTable = 'monitoringRecords_tdo_user_addresses';
$read_table = "tdo_user_addresses";
$table_field = "updated_at";//读取的字段名称

$sql = "select * from $recordTable WHERE repairState = '2' group by id";
$result2 = mysqli_query($conn2, $sql);
echo "---   ";

$IS = ":00:00";

$txtFileName = "/toodo/crontab/txt/tdoUserAddresses.txt";
//$txtFileName = "E:/LJW/LTDC/txt/tdoUserAddresses2018.txt";
$TxtRes = fopen($txtFileName, "w");

while ($val = mysqli_fetch_array($result2)) {
//    echo "---   ".$val[1];
    $arr = str_split($val[2], 1);

    for ($i = 0; $i < count($arr); $i++) {
        if ($arr[$i] == 0) {

            $YMDHIS = $val[1] . " " . $i . $IS;
            $startTime = date("Y-m-d H:i:s", strtotime($YMDHIS));
            $stopTime = date("Y-m-d H:i:s", strtotime('+1 hour', strtotime($YMDHIS)));

            $sql = "select * from $read_table WHERE $table_field between '$startTime' AND '$stopTime'";
            $result = mysqli_query($conn, $sql);

            while ($row = mysqli_fetch_array($result)) {
                fwrite($TxtRes, $row[0] . "#" . $row[1] . "#" . $row[2] . "#" . $row[3] . "#" . $row[4] . "#" . $row[5] . "#" . $row[6] . "#" . $row[7] . "#" . $row[8] . "\r" . "\n");
            }
        }
    }
    $updatedRepairState = "update $recordTable set repairState = '1' WHERE id = '$val[0]'";
    mysqli_query($conn2, $updatedRepairState);

}
fclose($TxtRes);

$data = fopen($txtFileName, 'r');
$create_table_name = "tdo_user_addresses";//创建的表名

while (!feof($data)) {
    $line = fgets($data);
    $lines = join("','", explode('#', $line));
    $lists = "insert into $create_table_name($field1,$field2,$field3,$field4,$field5,$field6,$field7,$field8,$field9) values ('$lines')";
    mysqli_query($conn2, $lists);
}
fclose($data);

mysqli_close($conn);
mysqli_close($conn2);


