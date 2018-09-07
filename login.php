<?php
session_start();
$error = array('email' => '',
               'password' => '',
               'password2' => '',
               'password3' => '',
               'error' => '');
//$_POSTに値が入っていたら動かす
if (!(empty($_POST))) {
  //使いやすいようにローカル変数に格納（emailは正しい形式か同時に調べる）
  $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  $password1 = $_POST['password'];
  //$cancelにokが入っていなければバリデーションに引っかかったとみなしてデータベース登録しない
  $cancel = 'ok';
  if (!is_string($email)) {
    $error['email'] = 'blank';
    $cancel = 'error';
  }
  if (empty($password1)) {
    $error['password'] = 'blank';
    $cancel = 'error';
  }
  //パスワードが英数字か確認
  if (!ctype_alnum($password1)) {
    $error['password2'] = 'number';
    $cancel = 'error';
  }
    if (!(empty($_POST)) && $cancel == 'ok') {
        //データベースと接続
        //入力されたメールアドレスとパスワードを用いてテーブルのデータを取得する
        $sql = "SELECT * FROM user WHERE email = '$email'" ;
        //アロー演算子を使ってPDOインスタンス内にあるqueryメソッドにアクセスして変数に格納
        require_once "db_connection.php";
        $stmt = $dbh->query($sql);
        //結果をローカル変数に格納
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!($result)) {
          $error['error'] = 'error';
          $cancel = 'error';
        }
        if (!(password_verify($password1, $result['password']))) {
          $error['password3'] = 'miss';
          $cancel = 'error';
        }
        if ($cancel == 'ok') {
          //Sessionにデータを入れる
          $_SESSION['id'] = $result['id'];
          $_SESSION['name'] = $result['name'];
          $_SESSION['email'] = $result['email'];
          $_SESSION['age'] = $result['age'];
          $_SESSION['fuyo'] = $result['fuyo'];
          header('Location: http://192.168.64.2/app_payroll/payroll.php');
          exit();
        }
    }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>ログイン画面</title>
  <link rel="stylesheet" href="./stylesheet.css">
</head>
<body>
  <div class="header">
    <div class="header-list">
      <ul>
        <li><a href="http://192.168.64.2/app_payroll/input.php">>>まだユーザー登録してない方はこちらへ</a></li>
      </ul>
    </div>
  </div>
  <div class="input">
  <form action="login.php" name="loginForm" method="post" accept-charset="utf-8">
    <?php if ($error['error'] == 'error'): ?>
      <p class="error">* メールアドレスが正しくありません</p>
    <?php endif; ?>
    メールアドレスを入力して下さい <input type="text" name="email">
    <?php if ($error['email'] == 'blank'): ?>
      <p class="error">* メールアドレスを入力して下さい</p>
    <?php endif; ?>
    <br>
    パスワードを入力して下さい <input type="text" name="password">
    <?php if ($error['password'] == 'blank'): ?>
      <p class="error">* パスワードを入力して下さい</p>
    <?php endif; ?>
    <?php if ($error['password2'] == 'number'): ?>
      <p class="error">* パスワードは英数字で入力して下さい</p>
    <?php endif; ?>
    <?php if ($error['password3'] == 'miss'): ?>
      <p class="error">* パスワードが正しくありません</p>
    <?php endif; ?>
    <br>
    <input type="submit" name="submit" value="ログイン">
  </form>
  </div>
</body>
</html>