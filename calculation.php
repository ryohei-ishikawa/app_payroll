<?php
require_once "db_connection.php";

//POSTで受け取ったものをローカル変数に格納
$salary     = $_POST['salary'] + $_POST['overtime'] + $_POST['commuting'] + $_POST['house'];
$_SESSION['sosikyu'] = $salary;
//年金保険料表テーブルから条件にあうレコードを取得する
$sql = "SELECT * FROM kennkouhokennn WHERE $salary >= ijo AND $salary < miman";
$stmt = $dbh->query($sql);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

//年金
$_SESSION['nenkin'] = $result['nennkinnseppann'];

//保険料
if ($_SESSION['age'] < 40) {
  $_SESSION['hoken'] = $result['40mimannhokennseppan'];
} else {
  $_SESSION['hoken'] = $result['40ijouhokennseppann'];
}

//社会保険料と年金を控除して通勤手当を控除した所得
$kojo = $salary - $_SESSION['hoken'] - $_SESSION['nenkin']; - $_POST['commuting'];

//所得税テーブルから条件にあうレコードを取得する
$sql2 = "SELECT * FROM syotokuzei WHERE $kojo >= ijo AND $kojo < miman";
$stmt2 = $dbh->query($sql2);
$result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
// var_dump($result2);

//所得税を扶養人数に応じてSessionに格納する関数
function fuyo($result2) {
  if ($_SESSION['fuyo'] == 0) {
    $_SESSION['syotokuzei'] = $result2['fuyo0'];
  } elseif ($_SESSION['fuyo'] == 1) {
    $_SESSION['syotokuzei'] = $result2['fuyo1'];
  } elseif ($_SESSION['fuyo'] == 2) {
    $_SESSION['syotokuzei'] = $result2['fuyo2'];
  } elseif ($_SESSION['fuyo'] == 3) {
    $_SESSION['syotokuzei'] = $result2['fuyo3'];
  } elseif ($_SESSION['fuyo'] == 4) {
    $_SESSION['syotokuzei'] = $result2['fuyo4'];
  } elseif ($_SESSION['fuyo'] == 5) {
    $_SESSION['syotokuzei'] = $result2['fuyo5'];
  } elseif ($_SESSION['fuyo'] == 6) {
    $_SESSION['syotokuzei'] = $result2['fuyo6'];
  } elseif ($_SESSION['fuyo'] == 7) {
    $_SESSION['syotokuzei'] = $result2['fuyo7'];
  } else {
    //扶養人数7人以上の処理
  }
}

//関数呼び出し
fuyo($result2);

//レコードをそのまま使えない例外の場合の処理
if ($kojo > 860000 && $kojo < 970000) {
  fuyo($result2);
  $_SESSION['syotokuzei'] = ($kojo - 860000) * 0.23483 + $_SESSION['syotokuzei'];
} elseif ($kojo > 970000 && $kojo < 1720000) {
  fuyo($result2);
  $_SESSION['syotokuzei'] = ($kojo - 1720000) * 0.33693 + $_SESSION['syotokuzei'];
} elseif ($kojo > 1720000 && $kojo < 3550000) {
  fuyo($result2);
  $_SESSION['syotokuzei'] = ($kojo - 3550000) * 0.4084 + $_SESSION['syotokuzei'];
} elseif ($kojo > 3550000) {
  fuyo($result2);
  $_SESSION['syotokuzei'] = ($kojo - 3550000) * 0.45945 + $_SESSION['syotokuzei'];
}

//手取り計算
$_SESSION['tedori'] = $kojo - $_SESSION['syotokuzei'];

?>