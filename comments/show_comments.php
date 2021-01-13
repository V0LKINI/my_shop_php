<?php
//Массив в который будут сохраняться все данные о комментариях
$comments = [];
$good_id = $good['id'];
$query = "SELECT * FROM comments WHERE id_good='$good_id'";

$query_result = mysqli_query($connection, $query) 
    or die("Ошибка " . mysqli_error($connection)); ;

if ($query_result) {
    while ($data_array = mysqli_fetch_assoc($query_result)) {
        $comments[] = $data_array;   
    }
}

?>