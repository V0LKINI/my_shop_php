<?php 
session_start(); 

require_once('auth/connection.php');

$query = 'SELECT * FROM goods;';

$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection)); ;

if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $goods[] = $data_array;   
    }
}

//Если пользователь нажал "Подробнее" под товаром, то он переходит на эту же страницу, но в url передаётся id и подключается шаблон openedProduct.php с соотвествующим id товара, если в url отсутствует id, то попадаем в блок else, то есть выводится список всех товаров в магазине.

$id = $_GET['id'];
if (isset($id)) {
    $good = [];
    foreach ($goods as $product) {
        if ($product['id'] == $id) {
            $good = $product;
            break;
         } 
    } 
    require('openedProduct.php');
}
else{//всё что ниже выполняется если не передан id
?>


<?php require_once('templates/header.php'); ?>

<h1>
    Каталог товаров
</h1>
<div>
    <?php foreach ($goods as $good): ?>
    <div class="shopUnit">
        <img src="<?php echo $good['img']; ?>" />

        <div class="shopUnitName">
           <?php echo $good['name']; ?>
        </div>
        <div class="shopUnitShortDesc">
            <?php echo $good['description']; ?>
        </div>
        <div class="shopUnitPrice">
           <?php echo $good['price'] . '$'; ?>
        </div>
        <a href="shop.php?id=<?php echo $good['id']; ?>" class="shopUnitMore">
            Подробнее
        </a>
    </div>
    <?php endforeach; ?>
</div>

<?php require_once('templates/footer.php'); 
}//Конец блока else
?>