<?php
session_start();
require_once '../config/database.php';
require_once 'functions.php';

if (!isLoggedIn() && basename($_SERVER['PHP_SELF']) != 'login.php' && basename($_SERVER['PHP_SELF']) != 'register.php') {
    redirect('login.php');
}
?>