<?php
session_start();
//error配列初期化
$error = array('salary' => '',
               'salary2' => '',
               'overtime' => '',
               'overtime2' => '',
               'commuting' => '',
               'commuting2' => '',
               'house' => '',
               'house2' => '');
//セッション初期化
if (empty($_POST)) {
  $_SESSION['sosikyu']    = 0;
  $_SESSION['hoken']      = 0;
  $_SESSION['nenkin']     = 0;
  $_SESSION['syotokuzei'] = 0;
  $_SESSION['tedori']     = 0;
}
//$cancelにokが入っていなければバリデーションに引っかかったとみなして計算しない
$cancel = 'ok';
if (!(empty($_POST))) {
  //エラー項目の確認
  if (empty($_POST['salary'])) {
    $error['salary'] = 'blank';
    $cancel = 'error';
  }
  if (!ctype_digit($_POST['salary'])) {
    $error['salary2'] = 'number';
    $cancel = 'error';
  }
    if (empty($_POST['overtime'])) {
    $error['overtime'] = 'blank';
    $cancel = 'error';
  }
  if (!ctype_digit($_POST['overtime'])) {
    $error['overtime2'] = 'number';
    $cancel = 'error';
  }
    if (empty($_POST['commuting'])) {
    $error['commuting'] = 'blank';
    $cancel = 'error';
  }
  if (!ctype_digit($_POST['commuting'])) {
    $error['commuting2'] = 'number';
    $cancel = 'error';
  }
    if (empty($_POST['house'])) {
    $error['house'] = 'blank';
    $cancel = 'error';
  }
  if (!ctype_digit($_POST['house'])) {
    $error['house2'] = 'number';
    $cancel = 'error';
  }
  if ($_POST['salary'] == 0) {
    $error['salary'] = '';
    $cancel = 'ok';
  }
  if ($_POST['overtime'] == 0) {
    $error['overtime'] = '';
    $cancel = 'ok';
  }
  if ($_POST['commuting'] == 0) {
    $error['commuting'] = '';
    $cancel = 'ok';
  }
  if ($_POST['house'] == 0) {
    $error['house'] = '';
    $cancel = 'ok';
  }
  if ($cancel == 'ok'){
    require_once "calculation.php";
  }
}

?>
<!DOCTYPE html>
<html>
<head>
  <title>給与計算フォーム</title>
  <link rel="stylesheet" href="./stylesheet.css">
</head>
<body>
  <div class="header">
    <div class="header-list">
      <ul>
        <li><a href="http://192.168.64.2/app_payroll/change.php">>>ユーザー情報変更ページへ</a></li>
        <li><a href="http://192.168.64.2/app_payroll/logout.php">>>ログアウトする</a></li>
      </ul>
    </div>
  </div>
  <div class="input">
    <form action="payroll.php" method="post" accept-charset="utf-8">
      基本給を入力して下さい：<input type="text" name="salary" value="0">
      <?php if ($error['salary'] == 'blank'): ?>
        <p class="error">* 基本給を入力して下さい</p>
      <?php endif; ?>
      <?php if ($error['salary2'] == 'number'): ?>
      <p class="error">* 基本給は数字で入力して下さい</p>
      <?php endif; ?>
      <br>
      時間外手当を入力して下さい：<input type="text" name="overtime" value="0">
      <?php if ($error['overtime'] == 'blank'): ?>
        <p class="error">* 時間外手当を入力して下さい</p>
      <?php endif; ?>
      <?php if ($error['overtime2'] == 'number'): ?>
        <p class="error">* 時間外手当は数字で入力して下さい</p>
      <?php endif; ?>
      <br>
      通勤手当を入力して下さい：<input type="text" name="commuting" value="0">
      <?php if ($error['commuting'] == 'blank'): ?>
        <p class="error">* 通勤手当を入力して下さい</p>
      <?php endif; ?>
      <?php if ($error['commuting2'] == 'number'): ?>
        <p class="error">* 通勤手当は数字で入力して下さい</p>
      <?php endif; ?>
      <br>
      住宅手当を入力して下さい：<input type="text" name="house" value="0">
      <?php if ($error['house'] == 'blank'): ?>
        <p class="error">* 住宅手当を入力して下さい</p>
      <?php endif; ?>
      <?php if ($error['house2'] == 'number'): ?>
        <p class="error">* 住宅手当は数字で入力して下さい</p>
      <?php endif; ?>
      <br>
      <input type="submit" name="" value="送信する">
    </form>
  </div>
  <br>
  <div class="input">
    総支給は<span class="result"><?php echo $_SESSION['sosikyu'] ?></span>です
    <br>
    健康保険料は<span class="result"><?php echo $_SESSION['hoken'] ?></span>です
    <br>
    年金は<span class="result"><?php echo $_SESSION['nenkin'] ?></span>です
    <br>
    所得税は<span class="result"><?php echo $_SESSION['syotokuzei'] ?></span>です
    <br>
    手取りは<span class="result"><?php echo $_SESSION['tedori'] ?></span>です
  </div>
</body>
</html>