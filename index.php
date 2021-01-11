<?php

session_start();  
$title="Онлайн магазин";

//Подключаем шапку сайта
require_once('templates/header.php');

require_once('auth/connection.php');

$query = 'SELECT * FROM goods;';

$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection)); ;

if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $goods[] = $data_array;   
    }
}

$page = $_GET['page'];
if (!isset($page)) {
    require('templates/main.php');
} elseif ($page == 'shop') {
    require('templates/shop.php');
} elseif ($page == 'product') {
	$id = $_GET['id'];
	$good = [];
	foreach ($goods as $product) {
		if ($product['id'] == $id) {
		 	$good = $product;
		 	break;
		 } 
	}
    require('templates/openedProduct.php');
}

//Подключаем футер
require_once('templates/footer.php');
?>


