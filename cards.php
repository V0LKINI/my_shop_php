<?php 
require_once('auth/connection.php');

//Сортировка товаров по дате, просмотрам и комментариям
$sort_by_q = $_POST['sort'];

//Получаем начальную позицию и количество добавляемых элементов
$begin = $_POST["begin"];
$count_per_page = $_POST["count"];

//Получение товаров
$query = "SELECT * FROM goods ORDER BY $sort_by_q LIMIT $begin, $count_per_page;";
$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection));    
if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $goods[] = $data_array;    
    }
}
 ?>

 <?php require('templates/shop_unit.php'); ?>

