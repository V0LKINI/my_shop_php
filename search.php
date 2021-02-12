<?php 
require_once('auth/connection.php');

if (isset($_POST['search_q']) and $_POST['search_q'] !='' ){
	$search_q=$_POST['search_q'];
	$search_q = trim($search_q);
	$search_q = strip_tags($search_q);

	$query = "SELECT * FROM goods WHERE name LIKE '%$search_q%';";
	$search_result = mysqli_query($connection, $query)
		or die("Ошибка " . mysqli_error($connection));

	if ($search_result) {
	    while ($data_array = mysqli_fetch_assoc($search_result)) {
	            $goods[] = $data_array;   
	        }
	} 
} else {
	header("Location: http://brandshop/shop.php");
}
?>

<?php require_once('templates/header.php'); ?>

<?php if ($goods){ ?>
<h1>Найденные товары:</h1>

<?php require('templates/shop_unit.php'); ?>

<?php } else{ ?>
<h1>Товаров не найдено=(</h1>

<?php }require_once('templates/footer.php'); ?>

