<?php

// 接收要删除的数据 ID
if (empty($_GET['id'])) {
  exit('<h1>必须传入指定参数</h1>');
}

$id = $_GET['id']; 

// 1. 建立连接
$con = mysqli_connect('localhost', 'root', '123456', 'test');

if (!$con) {
  exit('<h1>连接数据库失败</h1>');
}

// 2. 开始查询
$query = mysqli_query($con, 'delete from users where id in (' . $id . ');');

if (!$query) {
  exit('<h1>查询数据失败</h1>');
}

$affected_rows = mysqli_affected_rows($con);

if ($affected_rows <= 0) {
  exit('<h1>删除失败</h1>');
}

header('Location: index.php');