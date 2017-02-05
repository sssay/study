<?php
//页面编码
header("content-type:text/html;charset=utf-8");
//数据库配置
define('DB_HOST', 'localhost');//服务器域名
define('DB_USER','root');//用户名
define('DB_PWD','');//密码
define('DB_NAME', 'company');//数据库名
define('DB_CHARSET','utf8');//链接编码


/*//链接数据库
$link=mysqli_connect(DB_HOST,DB_USER,DB_PWD) or die(mysql_error());

//设置链接编码
mysqli_set_charset(DB_CHARSET) or die(mysql_error());

//选择数据库
mysqli_select_db(DB_NAME) or die(mysql_error());*/



//链接数据库
$link = new mysqli(DB_HOST,DB_USER,DB_PWD,DB_NAME) or die($mysqli->errno);

//设置链接编码
mysqli_set_charset($link , DB_CHARSET);




$sql="select * from navication where id>8 and id<12 or id>150 and id<200 order by id asc";

$ret=mysqli_query($link,$sql);

while($res=mysqli_fetch_row($ret)){
	$list[]=$res;
}
// $arr=array_pop($list);
var_dump($list);


