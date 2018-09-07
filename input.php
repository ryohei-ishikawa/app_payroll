<?php
require_once "db_connection.php";

//空のerror配列を生成する。
$error = array('name' => '',
               'email' => '',
               'email2' => '',
               'password' => '',
               'password2' => '',
               'password3' => '',
               'age' => '',
               'age2' => '',
               'fuyo' => '');
//$cancelにokが入っていなければバリデーションに引っかかったとみなしてデータベース登録しない
$cancel = 'ok';

//$_POSTにデータが送信されれば動く（最初は動かしたくない）
if (!empty($_POST)) {
  //使いやすいようにGETで来た情報を変数に格納する。
  $name     = $_POST['name'];
  //emailが正しい形式か調べるフィルター。正しくなければFALSEが返される。
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  $password = $_POST['password'];
  # ハッシュ処理の計算コストを指定
  $options = array('cost' => 10);
  # ハッシュ化方式にPASSWORD_DEFAULTを指定し、パスワードをハッシュ化する。
  # password_hash()関数は自動的に安全なソルトを生成してくれる。(ハッシュ値を取得するたびにソルトが自動生成されるので、同じパスワードでもハッシュ値が変わる)
  $hashpassword = password_hash($password, PASSWORD_DEFAULT, $options);
  $age      = $_POST['age'];
  $fuyo     = $_POST['fuyo'];

  //エラー項目の確認
  if (empty($name)) {
    $error['name'] = 'blank';
    $cancel = 'error';
  }
  if (!is_string($email)) {
    $error['email'] = 'blank';
    $cancel = 'error';
  }
  //メールアドレスが重複しているか調べる（データベースで検索してfalseであれば重複していない）
  $sql = "SELECT * FROM user WHERE email = '$email' ";
  $stmt = $dbh->query($sql);
  $user = $stmt->fetch();
  //すでに存在していればtrueで返ってくるのでifが動く
  if ($user) {
    $error['email2'] = 'duplicatio';
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

  //バリデーションに引っかかっていなければデータベース動かす
  if ($cancel == 'ok') {
    //登録準備
    $stmt = $dbh -> prepare("INSERT INTO user (name,email,password,age,fuyo) VALUES ('$name', '$email', '$hashpassword', '$age', '$fuyo');");
    //登録するそれぞれの情報の型を固定
    $stmt -> bindValue(':name', trim($name), PDO::PARAM_STR);
    $stmt -> bindValue(':email', trim($email), PDO::PARAM_STR);
    $stmt -> bindValue(':password', trim($hashpassword), PDO::PARAM_STR);
    $stmt -> bindValue(':age', trim($age), PDO::PARAM_STR);
    $stmt -> bindValue(':fuyo', trim($fuyo), PDO::PARAM_STR);
    $stmt -> execute(); //データベースの登録を実行
    $pdo = NULL;        //データベース接続を解除
    header('Location: http://192.168.64.2/app_payroll/login.php');
  }
}

?>

<!DOCTYPE html>
<html>
<head>
  <title>新規登録</title>
  <link rel="stylesheet" href="./stylesheet.css">
</head>
<body>
    <div class="header">
    <div class="header-list">
      <ul>
        <li><a href="http://192.168.64.2/app_payroll/login.php">>>ログインはこちらへ</a></li>
      </ul>
    </div>
  </div>
  <div class="input">
    <form action="input.php" method="post" accept-charset="utf-8">
    名前を入力して下さい      <input type="text" name="name">
    <?php if ($error['name'] == 'blank'): ?>
      <p class="error">* 名前を入力して下さい</p>
    <?php endif; ?>
    <br>
    メールアドレスを入力して下さい <input type="text" name="email">
    <?php if ($error['email'] == 'blank'): ?>
      <p class="error">* メールアドレスを正しい形式で入力して下さい</p>
    <?php endif; ?>
    <?php if ($error['email2'] == 'duplicatio'): ?>
      <p class="error">* このメールアドレスは既に登録されています</p>
    <?php endif; ?>
    <br>
    パスワードを入力して下さい <input type="text" name="password">
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
    年齢を入力して下さい      <input type="text" name="age">
    <?php if ($error['age'] == 'blank'): ?>
      <p class="error">* 年齢を入力して下さい</p>
    <?php endif; ?>
    <?php if ($error['age2'] == 'number'): ?>
      <p class="error">* 年齢は数字で入力して下さい</p>
    <?php endif; ?>
    <br>
    扶養者は何人か入力して下さい
    <select name="fuyo">
      <option value="0">0</option>
      <option value="1">1</option>
      <option value="2">2</option>
      <option value="3">3</option>
      <option value="4">4</option>
      <option value="5">5</option>
      <option value="6">6</option>
      <option value="7">7</option>
    </select>
    <br>
    <input type="submit" name="submit" value="登録">
  </form>
  </div>
</body>
</html>