<?php

$comments = []; //Массив в который будут сохраняться все данные о комментариях
$good_id = $good['id']; //Переменная с ID товара
$kol = 5;  //количество записей для вывода на 1 странице

$res = mysqli_query($connection, "SELECT COUNT(*) FROM comments WHERE id_good='$good_id'");
$row = mysqli_fetch_row($res);

$total = $row[0]; // всего записей 
$count_comment_page = 1;
if ($count_comment_page < ceil($total / $kol)) {
	$count_comment_page = ceil($total / $kol); // всего страниц 
}


// текущая страница
if (isset($_GET['comment_page']) and $_GET['comment_page']!='specifications' and $_GET['comment_page']<=$count_comment_page and is_numeric($_GET['comment_page'])){
	$comment_page = $_GET['comment_page'];
}else $comment_page = 1; 


$art = ($comment_page * $kol) - $kol; // определяем, с какой записи нам выводить
$count_show_comment_pages = 10; //количество отображаемых страниц


if ($count_comment_page > 1) { // Всё это только если количество страниц больше 1
	/* Дальше идёт вычисление первой выводимой страницы и последней (чтобы текущая страница была где-то посредине, если это возможно, и чтобы общая сумма выводимых страниц была равна count_show_comment_pages, либо меньше, если количество страниц недостаточно) */
	$left = $comment_page - 1;
	$right = $count_comment_page - $comment_page;
	if ($left < floor($count_show_comment_pages / 2)) $start = 1;
	else $start = $comment_page - floor($count_show_comment_pages / 2);
	$end = $start + $count_show_comment_pages - 1;
	if ($end > $count_comment_page) {
	  $start -= ($end - $count_comment_page);
	  $end = $count_comment_page;
	  if ($start < 1) $start = 1;
	}
}

//В базе данных выбирается 5 коментариев, которые сохраняются в массиве comments[].
$query = "SELECT * FROM comments WHERE id_good='$good_id' LIMIT $art,$kol";

$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection)); ;

if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $comments[] = $data_array;
    }
}

?>