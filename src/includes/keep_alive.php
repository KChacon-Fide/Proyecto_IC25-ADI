<?php
session_start();
$_SESSION['LAST_ACTIVITY'] = time();
http_response_code(200);
?>
