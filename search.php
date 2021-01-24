<?php 
require_once('auth/connection.php');

if (isset($_POST['search_q'])){
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
}
?>

<?php require_once('templates/header.php'); ?>

<?php if ($goods){ ?>
<h1>Найденные товары:</h1>

<div>
    <?php foreach ($goods as $good): ?>
    <div class="shopUnit">
        <img src="<?php echo $good['img']; ?>" />

        <div class="shopUnitName">
           <?php echo $good['name']; ?>
        </div>
        <div class="shopUnitShortDesc">
            <?php echo substr($good['description'],0,60)."..."; ?>
        </div>
        <div class="shopUnitPrice">
           <?php echo $good['price'] . '$'; ?>
        </div>
        <a href="shop.php?id=<?php echo $good['id']; ?>&page=1" class="shopUnitMore">
            Подробнее
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php } else{ ?>
<h1>Товаров не найдено=(</h1>

<?php }require_once('templates/footer.php'); ?>

