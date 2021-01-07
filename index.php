<?php session_start(); ?> 


<!-- Шапка сайта -->
<?php require('templates/header.php');?>

<?php

    require('auth/connection.php');
    $query = 'SELECT * FROM goods;';

    $query_result = mysqli_query($connection, $query) 
        or die("Ошибка " . mysqli_error($connection)); ;

    if ($query_result) {
        while ($data_array = mysqli_fetch_assoc($query_result)) {
            $goods[] = $data_array;   
        }
    }

?>



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


