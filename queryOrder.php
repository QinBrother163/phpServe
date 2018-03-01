<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/2 0002
 * Time: 下午 2:27
 */

$mysql_server_name = '120.25.107.206';//修改的链接
$mysql_username = 'toodo';
$mysql_password = 'toodo1815';
//$mysql_dataname = 'mg_lt';
$mysql_dataname = 'tdbak.unicom';


$conn = mysqli_connect($mysql_server_name, $mysql_username, $mysql_password) or die("error Database link failure!!!");
mysqli_query($conn, "set character set 'utf8'");
mysqli_query($conn, "set names 'utf8'");
mysqli_select_db($conn, $mysql_dataname);

date_default_timezone_set("Asia/Chongqing");

$localTime = date("Y-m-d H:i:s");


$tdo_order_transport_table = 'tdo_order_transport';
$tdo_order_datas_logs_table = 'tdo_order_datas_logs';

//$transportNo = '400097270952';
//$webUrl = "http://www.kuaidi100.com/query?type=shentong&postid=" . $transportNo;
//$contents = file_get_contents($webUrl);
//
//$arr = json_decode($contents);
//$state = $arr->state;

$sql_tdo_order_transport_table = "select * from $tdo_order_transport_table";

$tdo_order_transport_data = mysqli_query($conn, $sql_tdo_order_transport_table);

while ($row = mysqli_fetch_array($tdo_order_transport_data)) {
//    echo $row[1];
    $transportNo = $row[1];
    $webUrl = "http://www.kuaidi100.com/query?type=shentong&postid=" . $transportNo;
    $contents = file_get_contents($webUrl);

    $arr = json_decode($contents);
    $state = $arr->state;

    if ($state == 3){
        $updateDeliveryStatus = "update $tdo_order_transport_table set deliveryStatus = '$state', updated_at = '$localTime' WHERE orderNo = '$row[3]'";

        mysqli_query($conn, $updateDeliveryStatus);

        $updateTradeStatus = "update $tdo_order_datas_logs_table set tradeStatus = '5', updated_at = '$localTime' WHERE tradeNo = '$row[3]'";

        mysqli_query($conn, $updateTradeStatus);

    }

}

mysqli_close($conn);
