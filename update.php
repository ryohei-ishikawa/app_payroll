<?php
require_once "db_connection.php";
session_start();

//ローカル変数にセッションを格納する
$id = $_SESSION['id'];
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$password = $_SESSION['password'];
$fuyo = $_SESSION['fuyo'];
$age = $_SESSION['age'];
$income = $_SESSION['income'];

//空のerror配列を生成する。
$error = array('name' => '',
               'email' => '',
               'password' => '',
               'password2' => '',
               'password3' => '',
               'age' => '',
               'age2' => '',
               'fuyo' => '',
               'income' => '');
//$cancelにokが入っていなければバリデーションに引っかかったとみなしてデータベース登録しない
$cancel = 'ok';

//$_POSTにデータが送信されれば動く（最初は動かしたくない）
if (!empty($_POST)) {
  //使いやすいようにGETで来た情報を変数に格納する。
  $name     = $_POST['name'];
  //emailが正しい形式か調べるフィルター。正しくなければFALSEが返される。
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  $password = $_POST['password'];
  $age      = $_POST['age'];
  $fuyo     = $_POST['fuyo'];
  $income   = $_POST['income'];

  //エラー項目の確認
  if (empty($name)) {
    $error['name'] = 'blank';
    $cancel = 'error';
  }
  if (!is_string($email)) {
    $error['email'] = 'blank';
    $cancel = 'error';
  }
  if (empty($password)) {
    $error['password'] = 'blank';
    $cancel = 'error';
  }
  //パスワードの長さを確認（4文字以上にする）
  if (strlen($password) <= 3) {
    $error['password2'] = 'length';
    $cancel = 'error';
  }
  //パスワードが数字か確認
  if (!ctype_alnum($password)) {
    $error['password3'] = 'number';
    $cancel = 'error';
  }
  if (empty($age)) {
    $error['age'] = 'blank';
    $cancel = 'error';
  }
  if (!ctype_digit($age)) {
    $error['age2'] = 'number';
    $cancel = 'error';
  }
  if (!ctype_digit($income)) {
    $error['income'] = 'number';
    $cancel = 'error';
  }

  //バリデーションに引っかかっていなければデータベース動かす
  if ($cancel == 'ok') {
  //name列データベース更新
  $sql = "UPDATE user SET name = '$name',email = '$email',password = '$password',fuyo = '$fuyo',age = '$age',income = '$income' WHERE id = $id";
  $stmt = $dbh->query($sql);
  //update成功画面に飛ばして処理終了
  header('Location: http://192.168.64.2/app_payroll/payroll.php');
  exit();
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>編集画面</title>
  <link rel="stylesheet" href="./stylesheet.css">
</head>
<body>
  <div class="input">
    <form action="update.php" method="post" accept-charset="utf-8">
    変更後の名前を入力して下さい      <input type="text" name="name" value="<?php echo $name ?>">
    <?php if ($error['name'] == 'blank'): ?>
      <p class="error">* 名前を入力して下さい</p>
    <?php endif; ?>
    <br>
    変更後のパスワードを入力して下さい <input type="text" name="password" value="<?php echo $password ?>">
    <?php if ($error['password'] == 'blank'): ?>
      <p class="error">* パスワードを入力して下さい</p>
    <?php endif; ?>
    <?php if ($error['password2'] == 'length'): ?>
      <p class="error">* パスワードは4桁以上で入力して下さい</p>
    <?php endif; ?>
    <?php if ($error['password3'] == 'number'): ?>
      <p class="error">* パスワードは数字で入力して下さい</p>
    <?php endif; ?>
    <br>
    変更後のメールアドレスを入力して下さい <input type="text" name="email" value="<?php echo $email ?>">
    <?php if ($error['email'] == 'blank'): ?>
      <p class="error">* メールアドレスを正しい形式で入力して下さい</p>
    <?php endif; ?>
    <br>
    変更後の年齢を入力して下さい      <input type="text" name="age" value="<?php echo $age ?>">
    <?php if ($error['age'] == 'blank'): ?>
      <p class="error">* 年齢を入力して下さい</p>
    <?php endif; ?>
    <?php if ($error['age2'] == 'number'): ?>
      <p class="error">* 年齢は数字で入力して下さい</p>
    <?php endif; ?>
    <br>
    変更後の扶養者は何人か入力して下さい
    <select name="fuyo" value="<?php echo $fuyo?>">
      <option value="0">0</option>
      <option value="1">1</option>
      <option value="2">2</option>
      <option value="3">3</option>
      <option value="4">4</option>
    </select>
    <br>
    変更後の前年度の所得を入力して下さい      <input type="text" name="income" value="<?php echo $income ?>">
    <?php if ($error['income'] == 'number'): ?>
      <p class="error">* 前年度の所得は数字で入力してください</p>
    <?php endif; ?>
    <br>
    <input type="submit" name="submit" value="登録">
  </form>
  </div>
</body>
</html>