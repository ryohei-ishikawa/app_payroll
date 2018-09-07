<?php
// セッション開始
session_start();

// セッションの値を初期化
$_SESSION = array();

// セッションを破棄
session_destroy();

header('Location: http://192.168.64.2/app_payroll/login.php');
?>