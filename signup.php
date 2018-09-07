<?php
require_once "db_connection.php";

//使いやすいようにGETで来た情報を変数に格納する。
$name     = $_POST['name'];
$email    = $_POST['email'];
$password = password_hash($_POST['password']);
$password = $_POST['password'];
  # ハッシュ処理の計算コストを指定
  $options = array('cost' => 10);
  # ハッシュ化方式にPASSWORD_DEFAULTを指定し、パスワードをハッシュ化する。
  # password_hash()関数は自動的に安全なソルトを生成してくれる。(ハッシュ値を取得するたびにソルトが自動生成されるので、同じパスワードでもハッシュ値が変わる)
  $hashpassword = password_hash($password, PASSWORD_DEFAULT, $options);
  var_dump($hashpassword);
$age      = $_POST['age'];
$fuyo     = $_POST['fuyo'];
$income   = $_POST['income'];

  //登録準備
  $stmt = $dbh -> prepare("INSERT INTO user (name,email,password,age,fuyo,income) VALUES(:name, :email, :password, :age, :fuyo, :income)");
  //登録するそれぞれの情報の型を固定
  $stmt -> bindValue(':name', $name, PDO::PARAM_STR);
  $stmt -> bindValue(':email', $email, PDO::PARAM_STR);
  $stmt -> bindValue(':password', $hashpassword, PDO::PARAM_STR);
  $stmt -> bindValue(':age', $age, PDO::PARAM_STR);
  $stmt -> bindValue(':fuyo', $fuyo, PDO::PARAM_STR);
  $stmt -> bindValue(':income', $income, PDO::PARAM_STR);
  $stmt -> execute(); //データベースの登録を実行
  $pdo = NULL;        //データベース接続を解除
  header('Location: http://192.168.64.2/app_payroll/login.php');
?>