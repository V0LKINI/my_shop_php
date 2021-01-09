<?php session_start(); ?> 


<!-- Шапка сайта -->
<?php require('templates/header.php');?>

 <?php
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

 ?>



<!-- Нижний футер -->
<?php require('templates/footer.php');?>


