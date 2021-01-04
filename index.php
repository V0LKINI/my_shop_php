<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <script src="scripts/jquery.js"></script>
    <script src="scripts/site.js"></script>
    <title>Онлайн магазин</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link href="styles/site.css" rel="stylesheet">
</head>
<body>

<!-- Шапка сайта -->
<?php require('templates/header.php');?>

<?php
    $db = "brandshop";
    $connection = mysqli_connect('localhost', 'root', 'root', $db)
        or die("Ошибка " . mysqli_error($connection)); 

    $query = 'SELECT * FROM goods;';

    $query_result = mysqli_query($connection, $query) 
        or die("Ошибка " . mysqli_error($connection)); ;

    if ($query_result) {
        $goods_count = mysqli_num_rows($query_result);
        for ($i=0; $i < $goods_count; $i++) { 
            $data_array = mysqli_fetch_array($query_result);
            $goods[] = $data_array;
        }
    }
?>


<div id="content">
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
    }elseif ($page == 'register') {
        require('auth/register.php');
    }elseif ($page == 'login') {
        require('auth/login.php');
    }

 ?>

</div>

<!-- Нижний футер -->
<?php require('templates/footer.php');?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-q2kxQ16AaE6UbzuKqyBE9/u/KzioAlnx2maXQHiDX9d4/zp8Ok3f+M7DPm+Ib6IU" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js" integrity="sha384-pQQkAEnwaBkjpqZ8RU1fF1AKtTcHJwFl3pblpTlHXybJjHpMYo79HY3hIi4NKxyj" crossorigin="anonymous"></script>
</body>
</html>