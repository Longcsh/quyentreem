<?php
session_start();
$_SESSION = [];
session_destroy();
 header('Location: ../../pages/dang_nhap.php');
exit;
