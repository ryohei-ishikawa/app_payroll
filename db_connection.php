<?php

$dsn = 'mysql:dbname=app_payroll;host=192.168.64.2;port=3306;charset=utf8';
$user = 'root';
$password = '';
$dbh = null;

try {
  global $dbh;
  $dbh = new PDO($dsn, $user, $password);
} catch (PODEException $e) {
  echo 'Connection failed: ' . $e->getMessege();
}

?>