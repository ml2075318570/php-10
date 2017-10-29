<?php

// 接收要修改的数据 ID
if (empty($_GET['id'])) {
  exit('<h1>必须传入指定参数</h1>');
}

$id = $_GET['id'];

// 1. 建立连接
$con = mysqli_connect('localhost', 'root', '123456', 'test');

mysqli_set_charset($con,'utf8');

if (!$con) {
  exit('<h1>连接数据库失败</h1>');
}

// 2. 开始查询
// 因为 ID 是唯一的 那么找到第一个满足条件的就不用再继续了 limit 1
$query = mysqli_query($con, "select * from users where id = {$id} limit 1;");

if (!$query) {
  exit('<h1>查询数据失败</h1>');
}

$GLOBALS['user'] = mysqli_fetch_assoc($query);

var_dump($GLOBALS['user']);

$GLOBALS['aaa'] = $GLOBALS['user']['id'];

if (!$GLOBALS['user']) {
  exit('<h1>找不到你要编辑的数据</h1>');
}

//======================================
function update_user() {

  if (empty($_POST['name'])) {
    $GLOBALS['error_message'] = '请输入姓名';
    return;
  }
  if (!(isset($_POST['gender']) && $_POST['gender'] !== '-1')) {
    $GLOBALS['error_message'] = '请选择性别';
    return;
  }
  if (empty($_POST['birthday'])) {
    $GLOBALS['error_message'] = '请输入出生日期';
    return;
  }

  $name = $_POST['name'];
  $gender = $_POST['gender'];
  $birthday = $_POST['birthday'];
  // 接收文件并验证
  if (empty($_FILES['avatar'])) {
    $GLOBALS['error_message'] = '请上传头像';
    return;
  }

  $ext = pathinfo($_FILES['avatar']['name'],PATHINFO_EXTENSION);

  $target = '../uploads/avatar-' . uniqid() . '.' . $ext;

  if (!move_uploaded_file($_FILES['avatar']['tmp_name'],$target)) {
    $GLOBALS['error_message'] = '上传头像失败';
    return;
  }
  
  $avatar = substr($target,2);

  // 1. 建立连接
  $con = mysqli_connect('localhost','root','123456','test');

  if (!$con) {
    $GLOBALS['error_message'] = '连接数据库失败';
    return;
  }
  //  var_dump($GLOBALS['user']['id']);
  // '{$name}', {$gender}, '{$birthday}', '{$avatar}'
  $query = mysqli_query($con,"update users set {name} = '$name', gender = $gender, birthday = '$birthday', avatar = '$avatar' where id = $aaa;");
  
  if (!$query) {
    $GLOBALS['error_message'] = '查询过程失败';
    return;
  }

  $affected_rows = mysqli_affected_rows($con);

  if ($affected_rows !== 1) {
    $GLOBALS['error_message'] = '修改过程失败';
    return;
  }
  header('Location: index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  update_user();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>XXX管理系统</title>
  <link rel="stylesheet" href="assets/css/bootstrap.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <nav class="navbar navbar-expand navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">XXX管理系统</a>
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="index.html">用户管理</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="#">商品管理</a>
      </li>
    </ul>
  </nav>
  <main class="container">
    <h1 class="heading">编辑“<?php echo $GLOBALS['user']['name']; ?>”</h1>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data" autocomplete="off">
      <div class="form-group">
        <label for="avatar">头像</label>
        <input type="file" class="form-control" id="avatar">
      </div>
      <div class="form-group">
        <label for="name">姓名</label>
        <input type="text" class="form-control" id="name" value="<?php echo $GLOBALS['user']['name']; ?>">
      </div>
      <div class="form-group">
        <label for="gender">性别</label>
        <select class="form-control" id="gender">
          <option value="-1">请选择性别</option>
          <option value="1"<?php echo $GLOBALS['user']['gender'] === '1' ? ' selected': ''; ?>>男</option>
          <option value="0"<?php echo $GLOBALS['user']['gender'] === '0' ? ' selected': ''; ?>>女</option>
        </select>
      </div>
      <div class="form-group">
        <label for="birthday">生日</label>
        <input type="date" class="form-control" id="birthday" value="<?php echo $GLOBALS['user']['birthday']; ?>">
      </div>
      <button class="btn btn-primary">保存</button>
    </form>
  </main>
</body>
</html>
