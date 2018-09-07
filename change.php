<?php
session_start();
require_once("db_connection.php");
  //空のerror配列を生成する。
  $error = array('name' => '',
                 'email' => '',
                 'password' => '',
                 'password2' => '',
                 'password3' => '',
                 'age' => '',
                 'age2' => '',
                 'fuyo' => '');
//$_POSTにデータが送信されれば動く（最初は動かしたくない）

//ローカル変数にセッションを格納する
$id = $_SESSION['id'];
$name = $_SESSION['name'];
$email = $_SESSION['email'];
$fuyo = $_SESSION['fuyo'];
$age = $_SESSION['age'];

if (!empty($_POST)) {
  //使いやすいようにGETで来た情報を変数に格納する。
  $name     = $_POST['name'];
  $password = $_POST['password'];
  //emailが正しい形式か調べるフィルター。正しくなければFALSEが返される。
  $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
  $age      = $_POST['age'];
  $fuyo     = $_POST['fuyo'];

  //$cancelにokが入っていなければバリデーションに引っかかったとみなしてデータベース登録しない
  $cancel = 'ok';

  //エラー項目の確認
  if (empty($name)) {
    $error['name'] = 'blank';
    $cancel = 'error';
  }
  if (!is_string($email)) {
    $error['email'] = 'blank';
    $cancel = 'error';
  }
  if (!empty($password)) {
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
    $options = array('cost' => 10);
    # ハッシュ化方式にPASSWORD_DEFAULTを指定し、パスワードをハッシュ化する。
    # password_hash()関数は自動的に安全なソルトを生成してくれる。(ハッシュ値を取得するたびにソルトが自動生成されるので、同じパスワードでもハッシュ値が変わる)
    $hashpassword = password_hash($password, PASSWORD_DEFAULT, $options);
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
    $stmt = $dbh -> prepare("UPDATE user SET name = '$name', email = '$email', age = '$age', fuyo = '$fuyo' WHERE email = '$email';");
    //登録するそれぞれの情報の型を固定
    $stmt -> bindValue(':name', $name, PDO::PARAM_STR);
    $stmt -> bindValue(':email', $email, PDO::PARAM_STR);
    $stmt -> bindValue(':age', $age, PDO::PARAM_STR);
    $stmt -> bindValue(':fuyo', $fuyo, PDO::PARAM_STR);
    $stmt -> execute(); //データベースの登録を実行
    //パスワードが入力されていれば動かす
    if (!empty($hashpassword)) {
      $stmt = $dbh -> prepare("UPDATE user SET password ='$hashpassword' WHERE email = '$email';");
      $stmt -> bindValue(':password', $hashpassword, PDO::PARAM_STR);
      $stmt -> execute();
    }
    $pdo = NULL;        //データベース接続を解除
    //セッションにデータ入れ直す
    $_SESSION['name'] = $name;
    $_SESSION['password'] = $password;
    $_SESSION['email'] = $email;
    $_SESSION['age'] = $age;
    $_SESSION['fuyo'] = $fuyo;
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
        <li><a href="http://192.168.64.2/app_payroll/payroll.php">>>給与計算ページへ</a></li>
        <li><a href="http://192.168.64.2/app_payroll/logout.php">>>ログアウトする</a></li>
      </ul>
    </div>
  </div>
  <div class="input">
    <form action="change.php" method="post" accept-charset="utf-8">
    変更後の名前を入力して下さい      <input type="text" name="name" value="<?php echo $name ?>">
    <?php if ($error['name'] == 'blank'): ?>
      <p class="error">* 名前を入力して下さい</p>
    <?php endif; ?>
    <br>
    変更後のパスワードを入力して下さい <input type="text" name="password">
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
      <option value="0"<? if($fuyo == 0){print " selected";} ?>>0</option>
      <option value="1"<? if($fuyo == 1){print " selected";} ?>>1</option>
      <option value="2"<? if($fuyo == 2){print " selected";} ?>>2</option>
      <option value="3"<? if($fuyo == 3){print " selected";} ?>>3</option>
      <option value="4"<? if($fuyo == 4){print " selected";} ?>>4</option>
      <option value="5"<? if($fuyo == 5){print " selected";} ?>>5</option>
      <option value="6"<? if($fuyo == 6){print " selected";} ?>>6</option>
      <option value="7"<? if($fuyo == 7){print " selected";} ?>>7</option>
    </select>
    <br>
    <input type="submit" name="submit" value="登録">
  </form>
  </div>
</body>
</html>