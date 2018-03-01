<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/23
 * Time: 14:52
 */
$link = mysql_connect("172.16.147.95", "root", "toodo1814");
mysql_select_db("test_db", $link);
//获取八月表的总条数
$result_1 = mysql_query("SELECT id FROM fsdp_activity_list_201709", $link);
$num_rows_1 = mysql_num_rows($result_1);//获取数据库总数1
//获取九月表的总条数
$result_2 = mysql_query("SELECT id FROM fsdp_activity_list_201710", $link);
$num_rows_2 = mysql_num_rows($result_2);//获取数据库总数2
//获取十月表的总条数
$result_3 = mysql_query("SELECT id FROM fsdp_activity_list_201711", $link);
$num_rows_3 = mysql_num_rows($result_3);//获取数据库总数3

$pageSize = 30;   //每页显示的数量
$rowCount = $num_rows_1+$num_rows_2+$num_rows_3;   //总条数
$pageCount = ceil($rowCount/$pageSize);  //表示共有多少页
$pageCount1=ceil($num_rows_1/$pageSize);
$pageCount2=ceil($num_rows_2/$pageSize);
$pageCount3=ceil($num_rows_3/$pageSize);
$pageNow = 1;//当前显示第几页
//如果有pageNow就使用，没有就默认第一页。
if (!empty($_GET['pageNow'])){
    $pageNow = $_GET['pageNow'];
}
//使用sql语句时，注意有些变量应取出赋值。(开始的下标)
$pre = ($pageNow-1)*$pageSize;
//表一前查询
if($pre+$pageSize<$num_rows_1){
    $pre = ($pageNow-1)*$pageSize;
    $sql2 = "select * from fsdp_activity_list_201709 limit $pre,$pageSize";
    $result = mysql_query($sql2,$link);
    getRes($result);
    //表一与表二的临界点
    }else if($pageNow-$pageCount1==0){
        $num=$num_rows_1-$pre;
        $sql2 = "select * from fsdp_activity_list_201709 limit $pre,$num";
        $result = mysql_query($sql2,$link);
        getRes($result);
        $pre=0;
        $num=$pageSize-$num;
        $sql2 = "select * from fsdp_activity_list_201710 limit $pre,$num";
        $result2 = mysql_query($sql2,$link);
        getRes($result2);
        //表二的查询
    }else if($pageNow-$pageCount1>0 && $pageNow<$pageCount1+$pageCount2-1){
        $num=$pageSize-($num_rows_1-($pageCount1-1)*$pageSize);
        if($pageNow-$pageCount1-1==0){
            $pre=$num;
        }else{
            $pre = ($pageNow-$pageCount1-1)*$pageSize+$num;
        }
        $sql2 = "select * from fsdp_activity_list_201710 limit $pre,$pageSize";
        $result = mysql_query($sql2,$link);
        getRes($result);
        //表二到表三的临界点
    } else if($pageNow-($pageCount1+$pageCount2-1)==0){
        $pre = ((($pageCount1+$pageCount2-1)-$pageCount1-1)*$pageSize)+($pageSize-($num_rows_1-($pageCount1-1)*$pageSize));
        $num=$num_rows_2-$pre;
        $sql2 = "select * from fsdp_activity_list_201710 limit $pre,$num";
        $result = mysql_query($sql2,$link);
        getRes($result);
        $pre=0;
        $num=$pageSize-$num;
        $sql2 = "select * from fsdp_activity_list_201711 limit $pre,$num";
        $result2 = mysql_query($sql2,$link);
        getRes($result2);
        //表三的查询
    }else if($pageNow-($pageCount1+$pageCount2-1)>0) {
            $num =$pageSize-($num_rows_2-(((($pageCount1+$pageCount2-1)-$pageCount1-1)*$pageSize)+($pageSize-($num_rows_1-($pageCount1-1)*$pageSize))));
        if ($pageNow - ($pageCount1 + $pageCount2) == 0) {
            $pre = $num;
        } else {
            $pre = ($pageNow-($pageCount1+$pageCount2))*$pageSize+$num;
        }
        $sql2 = "select * from fsdp_activity_list_201711 limit $pre,$pageSize";
        $result = mysql_query($sql2, $link);
        getRes($result);
    }


function getRes($result){
    if($result!=null){
        while($row = mysql_fetch_array($result))
        {
            echo $row['id']."<br>";
            echo $row['createDate']."<br>";
            echo $row['resultCode']."<br>";
            echo $row['orderID']."<br>";
            echo "<br>";
        }
    }
}


if($pageNow>1){
    $prePage = $pageNow-1;
    echo "<a href='aaa.php?pageNow=$prePage'>pre</a> ";
}
if($pageNow<$pageCount){
    $nextPage = $pageNow+1;
    echo "<a href='aaa.php?pageNow=$nextPage'>next</a> ";
    echo "当前页{$pageNow}/共{$pageCount}页";
}