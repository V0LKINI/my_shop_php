<?php

$comments = []; //Массив в который будут сохраняться все данные о комментариях
$good_id = $good['id']; //Переменная с ID товара

//В базе данных выбирается 5 коментариев, которые сохраняются в массиве comments[].
$query = "SELECT * FROM comments WHERE id_good='$good_id'";

$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection)); ;

if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $comments[] = $data_array;
    }
}

?>