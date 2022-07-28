<?php

class view
{
    use singleton;

    private static $imgBook ='/../../public/assets/img/book.png';
    private static $imgRename ='/../../public/assets/img/rename.png';
    private static $imgFolder ='/../../public/assets/img/folder.png';
    private static $imgDelete ='/../../public/assets/img/delete.png';
    private static $imgBack ='/../../public/assets/img/back.png';

    function loadBook(){
        echo <<<END

         <ul><form enctype="multipart/form-data" action="index.php" method="POST" name="add_new_book">
           <li> Добавление новой книги
           <li>
           <input type="text" name="book_name" placeholder="Название книги"></li>
           <li>
           <input type="text" name="book_author" placeholder="Автор"></li>
           <li>
           <input type="text" name="book_description" placeholder="Описание"></li>
           <label>Загрузите книгу</label>
           <input name="book" type="file" /><br />
           <label>Загрузите обложку</label>
           <input name="cover" type="file" /><br />
           <input type="submit" value="Отправить" />
        </form>
        </ul>  
        END;
    }

    function newFolder(){
        echo <<<END
        <ul><form action="index.php" method="get">

              <input type="text" name="new" placeholder="Имя папки">          
              <button type="submit" >Создать папку</button>   
        </form>
        </ul>
        END;
    }

    function folderBut($name, $id){
        $imgFolder = self::$imgFolder;
        $imgRename = self::$imgRename;
        $imgDelete = self::$imgDelete;
        echo <<<END
            <li><form action="index.php" method="get"">
                 <button  type="submit" name="reference" value="$id"><img src ="$imgFolder" class="img_button">  $name</button>

                 <button type="submit" name="delete" value="$id" ><img src ="$imgDelete" class="img_button_delete"></button>
             </form></li>
        END;
    }

    function bookBut($name, $bookId){
        $imgBook = self::$imgBook;
        $imgDelete = self::$imgDelete;

        echo <<<END
            <li><form action="index.php" method="get"">
                 <button  type="submit" name="reference_book" value="$bookId"><img src ="$imgBook" class="img_button">  $name</button>
                 <button type="submit" name="delete_book" value="$bookId"><img src ="$imgDelete" class="img_button_delete"></button>
            </form></li>
        END;
}

    function backBut(){
        $imgBack = self::$imgBack;

        echo <<<END
             <form action="index.php" method="get">
             <li><button  type="submit" name="back"><img src ="$imgBack" class="img_button">Назад</button></li>
             </form>
        END;
    }
}