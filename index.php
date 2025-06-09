<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', 1);

if (empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off') {
    $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . $redirect);
    exit();
}

$dr = $_SERVER['DOCUMENT_ROOT'].'/';
if (isset($_GET['admin']))
    include $dr . 'admin/admin-home.php';
elseif (isset($_GET['admin-login']))
    include $dr . 'admin/admin-login.php';
elseif (isset($_GET['admin-gender']))
    include $dr . 'admin/admin-gender.php';
elseif (isset($_GET['admin-brand']))
    include $dr . 'admin/admin-brand.php';
elseif (isset($_GET['admin-flex']))
    include $dr . 'admin/admin-flex.php';
elseif (isset($_GET['admin-style']))
    include $dr . 'admin/admin-style.php';
elseif (isset($_GET['admin-shape']))
    include $dr . 'admin/admin-shape.php';
elseif (isset($_GET['admin-snow']))
    include $dr . 'admin/admin-snow.php';
elseif (isset($_GET['admin-order']))
    include $dr . 'admin/admin-order.php';
elseif (isset($_GET['admin-work']))
    include $dr . 'admin/admin-work.php';
elseif (isset($_GET['login']))
    include $dr . 'include/login.php';
elseif (isset($_GET['registrati']))
    include $dr . 'include/register.php';
elseif (isset($_GET['negozio']))
    include $dr . 'include/shop.php';
elseif (isset($_GET['cart']))
    include $dr . 'include/cart.php';
elseif (isset($_GET['profilo']))
    include $dr . 'include/user.php';
else
    include $dr . 'include/home.php';

