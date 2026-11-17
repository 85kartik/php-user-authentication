<?php
require __DIR__ . '/../app/bootstrap.php';
go(isset($_SESSION['user']) ? '/dashboard.php' : '/login.php');
