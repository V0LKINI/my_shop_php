<?php
$headers = "FROM: mrsteep228@gmail.com\r\nReply-to: mrsteep228@gmail.com\r\nContent-type: text/html; charset=utf-8\r\n";

 if(mail('k.volkov.n@gmail.com', 'Тема письма', 'Отправка почты через локальный сервер openserver', $headers) ) {echo'Письмо успешно отправлено';
 }else{echo 'Ошибка';}
 ?>