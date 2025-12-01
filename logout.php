<?php
/**
 * SIPANG POLRI - Logout Handler
 */

require_once 'config/database_config.php';

session_start();

$auth = new Auth();
$auth->logout();

// Redirect to login page
header('Location: login.php');
exit();