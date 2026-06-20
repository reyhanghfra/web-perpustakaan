<?php
session_start();

if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
    header('Location: ./pages/dashboard/');
} else {
    header('Location: ./login.php');
}
exit;