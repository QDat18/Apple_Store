<?php
session_start();
$captcha_code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 5));
$_SESSION['captcha_code'] = $captcha_code;
echo $captcha_code;
?>