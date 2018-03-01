<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/8
 * Time: 16:35
 */


$mysql_server_name2 = '120.25.107.206';//写入的链接
$mysql_username2 = 'toodo';
$mysql_password2 = 'toodo1815';
//$mysql_dataname2 = 'mg_lt';
$mysql_dataname2 = 'tdbak.unicom';


$conn2 = mysqli_connect($mysql_server_name2, $mysql_username2, $mysql_password2) or die("error Database link failure!!!");
mysqli_query($conn2, "set character set 'utf8'");
mysqli_query($conn2, "set names 'utf8'");
mysqli_select_db($conn2, $mysql_dataname2);

date_default_timezone_set("Asia/Chongqing");
$insertShowtime = date("Y-m-d");
$createShowtime = date("d");
$locH = date("H");
$locTime = date("Y-m-d", strtotime('-1 day', strtotime($insertShowtime)));


$tdo_order_datas_logs = 'phplooprecords_tdo_user_addresses';

$sql = "select * from $tdo_order_datas_logs group by id";
$result = mysqli_query($conn2, $sql);
$dataNumber = mysqli_num_rows($result);
$true_1 = "111111111111111111111111";
$true_0 = "000000000000000000000000";

$repair = 1;
$noRepair = 2;
$createTableDate = 'monitoringRecords_tdo_user_addresses';

if (mysqli_num_rows(mysqli_query($conn2, "SHOW TABLES LIKE '" . $createTableDate . "'")) == 1) {

} else {

    $createTable = "create table $createTableDate(id int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,createDate date,timeRecords text, repairState INT(11))";
    mysqli_query($conn2, $createTable);

}
$arrList = array();
while ($row = mysqli_fetch_array($result)) {

    $list = explode('-', $row[1]);
    array_push($arrList, $list[2]);
}

$sql = "select * from $tdo_order_datas_logs WHERE createDate = '$locTime'";
$result = mysqli_query($conn2, $sql);

$num1 = 1;
$num3 = "0";

if ($dataNumber == 2) {
    for ($i = 0; $i < count($arrList); $i++) {
        $start = $arrList[0];
        $end = $arrList[1];

        if ((int)$end - (int)$start == 1 && (int)$createShowtime - (int)$start == 1) {//相邻的天数

            while ($row = mysqli_fetch_array($result)) {
                if ($row[2] == $true_1) {//判断数据异常，没有问题
                    $sql = "delete from $tdo_order_datas_logs WHERE id = '$row[0]'";
                    mysqli_query($conn2, $sql);
                } else {//判断数据异常，有问题
                    if (strlen($row[2]) == 24) {
                        $sql = "insert into $createTableDate(createDate,timeRecords,repairState) values ('$row[1]','$row[2]','$noRepair')";
                        mysqli_query($conn2, $sql);

                        $sql = "delete from $tdo_order_datas_logs WHERE id = '$row[0]'";
                        mysqli_query($conn2, $sql);
                    }

                    if (strlen($row[2]) != 24) {
                        $dataLen = strlen($row[2]);
                        $locDate = 24 - $dataLen - 1;

                        $arr3 = array();
                        for ($i = 0; $i < $locDate; $i++) {
                            array_push($arr3, $num3);
                        }
                        $str3 = implode("", $arr3);
                        $strS = $row[2] . $str3 . $num1;

                        $sql = "insert into $createTableDate(createDate,timeRecords,repairState) values ('$row[1]','$strS','$noRepair')";
                        mysqli_query($conn2, $sql);

                        $sql = "delete from $tdo_order_datas_logs WHERE id = '$row[0]'";
                        mysqli_query($conn2, $sql);
                    }
                }
            }
        }
    }
}


$tdo_order_datas_logs = 'phplooprecords_tdo_user_addresses';

$sql = "select * from $tdo_order_datas_logs group by id";
$result = mysqli_query($conn2, $sql);

$arrDateTime = array();
while ($row = mysqli_fetch_array($result)) {
    array_push($arrDateTime, $row[1]);
}

if (count($arrDateTime) == 2) {

    $arrTime0 = reset($arrDateTime);
    $arrTime1 = end($arrDateTime);
    $locTime0 = date("Y-m-d", strtotime('+1 day', strtotime($arrTime0)));

    if ($insertShowtime != $locTime0) {
        $sql = "select * from $tdo_order_datas_logs WHERE createDate = '$arrTime0'";
        $result = mysqli_query($conn2, $sql);

        while ($row = mysqli_fetch_array($result)) {
            if ($row[2] == $true_1) {

                $sql = "delete from $tdo_order_datas_logs WHERE id = '$row[0]'";
                mysqli_query($conn2, $sql);

            } else {
                if (strlen($row[2]) == 24) {
                    $sql = "insert into $createTableDate(createDate,timeRecords,repairState) values ('$row[1]','$row[2]','$noRepair')";
                    mysqli_query($conn2, $sql);

                    $sql = "delete from $tdo_order_datas_logs WHERE id = '$row[0]'";
                    mysqli_query($conn2, $sql);
                }
                if (strlen($row[2]) != 24) {
                    $dataLen = strlen($row[2]);
                    $locDate = 24 - $dataLen - 1;

                    $arr3 = array();
                    for ($i = 0; $i < $locDate; $i++) {
                        array_push($arr3, $num3);
                    }
                    $str3 = implode("", $arr3);
                    $strS = $row[2] . $str3 . $num1;

                    $sql = "insert into $createTableDate(createDate,timeRecords,repairState) values ('$row[1]','$strS','$noRepair')";
                    mysqli_query($conn2, $sql);

                    $mysqlTime = date("Y-m-d", strtotime('-1 day', strtotime($insertShowtime)));//数据库时间
                    $arrDataList = array();
                    for ($i = 1; $i < 999; $i++) {

                        $locDate = date("Y-m-d", strtotime('+' . $i . ' day', strtotime($arrTime0)));
                        array_push($arrDataList, $locDate);
                        if ($locDate == $mysqlTime) {
                            break;
                        }
                    }
                    for ($i = 0; $i < count($arrDataList); $i++) {

                        $sql = "insert into $createTableDate(createDate,timeRecords,repairState) values ('$arrDataList[$i]','$true_0','$noRepair')";
                        mysqli_query($conn2, $sql);
                    }

                    $sql = "delete from $tdo_order_datas_logs WHERE id = '$row[0]'";
                    mysqli_query($conn2, $sql);
                }
            }
        }
    }

}
mysqli_close($conn2);




