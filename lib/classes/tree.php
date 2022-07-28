<?php

class tree
{
    use singleton;

    // получение дерева элементов библиотеки
    function treeLevel($id){
        $array = tree::do()->getTree($id);
        $result = "";

            foreach ($array as list($id, $name, $book)) {
                $hasChildren = tree::do()->hasChildren($id);
                $isBook = empty($book);
                $result.= tree::do()->treeGen($id, $name, $hasChildren, $isBook);
            }
            return $result;
    }
    //получение всех вложенные элементы
    function getTree($objId){
        $sql = db::do()->query("SELECT obj_id, obj_name, book_id FROM library_tree WHERE parent_id = '$objId'");
        return mysqli_fetch_all($sql);
    }

    //рекурсивный генератор HTML
    function treeGen($id, $name, $hasChildren, $isBook){
        if (!$hasChildren) $children = "";
        else $children = tree::do()->treeLevel($id);
        $hasChildren = $hasChildren? "class='show'" : "class='void'";
        $isBook = $isBook? "" : "книга";
        $tree = "<ol><li><span $hasChildren> $isBook $name<span><ol>$children</ol></li></ol>";
        return $tree;
    }

    //проверка есть ли дети
    function hasChildren($id){
        $sql = db::do()->query("SELECT obj_id FROM library_tree WHERE parent_id = '$id'");
        return !empty(mysqli_fetch_assoc($sql));
    }
}