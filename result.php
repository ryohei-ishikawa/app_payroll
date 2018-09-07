<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>計算結果</title>
  <link rel="stylesheet" href="./stylesheet.css">
</head>
<body>
  <div class="input">
    総支給は<?php echo $_SESSION['sosikyu'] ?>です
    <br>
    健康保険料は<?php echo $_SESSION['hoken'] ?>です
    <br>
    年金は <?php echo $_SESSION['nenkin'] ?>です
    <br>
    所得税は <?php echo $_SESSION['syotokuzei'] ?>です
    <br>
    手取りは <?php echo $_SESSION['tedori'] ?>です
  </div>
</body>
</html>