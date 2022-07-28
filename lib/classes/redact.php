<?php

class redact{

    use singleton;

    public function valueRequestGet(){
        // Если GET запрос пустой возвращаем все обьекты в корневой папке
        if (empty($_GET)) $valueReq = 1;
        else {
            switch (array_key_first($_GET)) {
                case 'reference':
                    $valueReq = $_GET['reference'];
                    break;

                case 'new':
                    $nameFolder = $_GET['new'];
                    $parentId = $_COOKIE['obj_id'];
                    $valueReq = $_COOKIE['obj_id'];
                    redact::do()->newFolder($nameFolder, $parentId);
                    break;

                case 'rename':
                    $newName = $_GET['rename'];
                    $objId = $_GET['obj_id'];
                    $valueReq = $_COOKIE['obj_id'];
                    redact::do()->renameFolder($newName, $objId);
                    break;

                case 'delete':
                    $objId = $_GET['delete'];
                    $valueReq = $_COOKIE['obj_id'];
                    redact::do()->deleteFolder($objId);
                    break;

                case 'delete_book':
                    $bookId = $_GET['delete_book'];
                    $valueReq = $_COOKIE['obj_id'];
                    redact::do()->deleteBook($bookId);
                    break;

                case 'back':
                    if ($_COOKIE['parent_id'] < 1) $valueReq = 1;
                    else $valueReq = intval($_COOKIE['parent_id']);
                    break;

                default: $valueReq =1;

            }
        }
        $array = redact::do()->referenceFolder($valueReq);
        return $array;
    }

    //получение всех вложенных папок и файлов
    public function referenceFolder($valueReq){
        $sql  = db::do()->query("SELECT obj_name, obj_id, book_id, parent_id 
                                       FROM library_tree 
                                       WHERE parent_id = '$valueReq' ORDER BY book_id, obj_name");
        $array = mysqli_fetch_all($sql);
        //запись текущей папки в куки
        $sqlQ = db::do()->query("SELECT obj_id, obj_name, parent_id
                                       FROM library_tree
                                       WHERE obj_id = '$valueReq'");
        $arrayQ = mysqli_fetch_assoc($sqlQ);
        setcookie("obj_id", $arrayQ['obj_id'], time()+3600);
        setcookie("parent_id", $arrayQ['parent_id'], time()+3600);
        return $array;
    }

    //переименовать папку
    public function renameFolder($newName, $objId){
        db::do()->query("UPDATE library_tree 
                               SET obj_name = '$newName' 
                               WHERE  id = '$objId'");
    }


    //удалить папку
    public function deleteFolder($objId){
 //        $newParentId = $_GET['new_parent_id'];
        db::do()->query("DELETE FROM library_tree 
                               WHERE obj_id = '$objId'");
//        db::do()->query("UPDATE library_tree SET parent_id = '$newParentId' WHERE  parent_id = '$objId'");
    }

    //удалить книгу
    public function deleteBook($bookId){
        $sqlAddress = mysqli_fetch_assoc(db::do()->query("SELECT cover_addr, book_addr
                                       FROM books
                                       WHERE id = '$bookId'"));
        $bookAddress = $sqlAddress['book_addr'];
        $coverAddress = $sqlAddress['cover_addr'];
        unlink($bookAddress);
        if ($coverAddress != addBook::do()->getStdCoverDirectory()) unlink($coverAddress);
        db::do()->query("DELETE FROM library_tree
                               WHERE book_id = '$bookId'");
        db::do()->query("DELETE FROM books
                               WHERE id = '$bookId'");
        return true;
    }

    //Создать папку
    public function newFolder($nameFolder, $parentId){
        db::do()->query("INSERT INTO library_tree (obj_name, parent_id) 
                               VALUES ('$nameFolder', '$parentId')");
    }

    //Адрес обложки
    public function getCoverAddr($bookId){
        return mysqli_fetch_assoc(db::do()->query("SELECT cover_addr FROM books where id = '$bookId'"))['cover_addr'];
    }
}

