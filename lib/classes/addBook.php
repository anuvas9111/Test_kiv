<?php

class addBook{

    use singleton;

    private static $bookDirectory = __DIR__.'/../../storage/library/';
    private static $allowExtBook = ['fb2','fb3','txt','epub','pdf'];
    private static $allowExtCover =['img','png','bmp'];
    private static $coverDirectory = __DIR__.'/../../storage/library/cover/';
    private static $stdCoverDirectory = __DIR__.'/../../storage/library/cover/no_cover.png'; //тут лежит пустая обложка

    //получить адрес хранения пустой обложки
    public function getStdCoverDirectory(){
        return self::$stdCoverDirectory;
    }

    // добавление новой книги
    public function new(){
        $value['valid'] = addBook::do()->validateData();
        $value['save'] =  addBook::do()->save();
        return $value;

    }

    //сохранение данных в бд
    public function save(){
        $newName = addBook::do()->codeName();
        $ext = pathinfo($_FILES['book']['name'], PATHINFO_EXTENSION);
        $bookAdr = self::$bookDirectory . $newName . '.' .$ext;
        $bookName = $_POST['book_name'];
        $bookAuthor = $_POST['book_author'];
        $bookDescription = $_POST['book_description'];
        $parentId = $_COOKIE['obj_id'];
        if(move_uploaded_file($_FILES['book']['tmp_name'], $bookAdr)){
            if (!empty($_FILES['cover'])){
                $extCover = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                $coverAdr = self::$coverDirectory . $newName . '.' . $extCover;
                move_uploaded_file($_FILES['cover']['tmp_name'], $coverAdr);
            }
            else $coverAdr = self::$stdCoverDirectory; //если не добавлена обложка, присваевается адрей пустой
            db::do()->query("INSERT INTO books (name, autor, description, cover_addr, book_addr)
                                   VALUES ('$bookName', '$bookAuthor', '$bookDescription', '$coverAdr', '$bookAdr')");
            db::do()->query("INSERT INTO library_tree (obj_name, parent_id, book_id)
                               VALUES ('$bookName', '$parentId', LAST_INSERT_ID())");
            return true;
        }
        return false;
    }

    //проверка расширение
    public function validateExt($ext){
        $bookExt =  (in_array($ext['book'], self::$allowExtBook));
        if (!empty($ext['cover'])){
            $coverExt = (in_array($ext['cover'], self::$allowExtCover));
            return $bookExt and $coverExt;
        }
        else return $bookExt;
    }


    //Генерация имени для сохранения
    public function codeName(){
        $ext = pathinfo($_FILES['book']['name'], PATHINFO_EXTENSION);
        do {
            $name = uniqid();
            $file = self::$bookDirectory . $name . $ext;
        } while (file_exists($file));
        return $name;
    }

    //проверяем валидность введенных данных
    public function validateData(){
        if (empty($_FILES['book']) or empty($_POST['book_name'] and !empty($_POST['book_author']))) {
            return false;
        }
        else {
            $foo = pathinfo($_FILES['book']['name'], PATHINFO_EXTENSION);
            $ext = ['book' => $foo];
            if (!empty($_FILES['cover'])) {
                $foo = pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION);
                $ext += ['cover' => $foo];
            }
            $extValidate = addBook::do()->validateExt($ext);
            //подредактировать валидные имена
            $bookNameValidate = preg_match("/^(([a-zA-Z' -]{1,50})|([А-я' -]{1,30}))$/u", $_POST['book_name']);
            $bookAuthorValidate = preg_match("/^(([a-zA-Z' -]{1,50})|([А-я' -]{1,30}))$/u", $_POST['book_author']);

            return $extValidate and $bookAuthorValidate and  $bookNameValidate;
        }
    }
}