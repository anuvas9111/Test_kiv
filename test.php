<!DOCTYPE HTML>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>library</title>
</head>
<?php
require_once '/../core/autoload.php';
$sql = db::do()->query("SELECT parent_id
                                       FROM library_tree 
                                       WHERE obj_id = '1'");
var_dump(mysqli_fetch_assoc($sql));
?>

</html>

