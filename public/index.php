<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
 <head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
     <link rel="stylesheet" href="assets/css/style.css">
     <script async="true" type="text/javascript" src="assets/js/library.js"></script>
     <title>Главная страница</title>
 </head>
 <body>

 <?php
        //require __DIR__.'/../core/core.php'; его не существует
        require __DIR__.'/../core/autoload.php';
 ?>

    <h2>Библиотека</h2>
    <lib>
        <?php require_once __DIR__.'/../module/library/index.php';?>
    </lib>

 </body>
</html>