<?php

class db{
    
    use singleton;

    private static $connection;

    private static $dbhost;

    private static $dbuser;

    private static $dbpass;

    private static $dbname;

    # Установка подключения к БД
    protected function __construct()
    {
       
        self::$dbhost = 'localhost';

        self::$dbuser = 'afet';

        self::$dbpass = 'Password_1';

        self::$dbname = 'library';

        self::$connection = $this->connect();
  
    }

    public function connect(){

        $connection = mysqli_connect(self::$dbhost, self::$dbuser, self::$dbpass, self::$dbname);

        if ( mysqli_connect_error() ){

            //

        } else {
          
            mysqli_set_charset($connection, "utf8mb4");

        }

        return $connection;

    }

//    public function disConnect(){
//        $connection = mysqli_connect(self::$dbhost, self::$dbuser, self::$dbpass, self::$dbname);
//        mysqli_close($connection);
//    }


    # Возврат результатов запроса в виде ассоциативного перебора
    public function assoc($res){

        return mysqli_fetch_assoc($res);

    }


    # Возврат результатов запроса в виде ассоциативного массива
    public function assoc_all($res){

        return mysqli_fetch_all($res, MYSQLI_ASSOC);
   
    }


    # Возврат результатов запроса в виде индексного перебора
    public function array($res){

        return mysqli_fetch_array($res);
   
    }

    # Возврат результатов запроса в виде индексного массива
    public function array_all($res){

        return mysqli_fetch_all($res, MYSQLI_NUM);
   
    }




    # Выполнение запроса к базе данных
    public function query($query, $log = true){

        if (!mysqli_ping(self::$connection)){

            self::$connection = db::do()->connect();

        }

        $res = mysqli_query(self::$connection, $query);

        if (mysqli_error(self::$connection)){

            switch(mysqli_errno(self::$connection)){

                case '1062':

                    //notification::do()->add("Запись уже существует...", 'error');

                    break;

                default:

                    $error_text = 'Ошибка чтения / записи!';

                    //notification::do()->add($error_text, 'error');

                    break;

            }

            //logger::do()->write($query.PHP_EOL.mysqli_error(self::$connection).stripslashes(json_encode(debug_backtrace(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)));

            //$ikivach_request['request_callback'][] = ['notification'];

            //if ($log) db::do()->log($query, 0);

            return false;

        }

        //if ($log) db::do()->log($query, 1);

//        db::do()->disConnect();
        return $res;


    }


    public function group_operation($mode, $table, $ids){

        $modes = ['lock' => "protected='1'", 'unlock' => "protected='1'", 'activate' => "state='1'", 'deactivate' => "state='2'"];

        if (db::do()->query("UPDATE `$table` SET ".$modes[$mode]." WHERE id IN(".join(',', $ids).")")) {

            /*notification::do()->add('Выполнено', 'success');

            req::do()->add_callback('redirect');*/

        } 

    }


    public function log($query, $success = 1, $user_id = false){

        /*preg_match('/^([\w\-]+)/', trim($query), $type);

        db::do()->query("INSERT IGNORE INTO log_".strtolower($type[0])." (`query`, `user_id`, `ip`, `success`) VALUES ('".db::do()->clean($query)."', '".($user_id ?? $_SESSION['user_id'])."','".user::do()->prop('ip')."','$success')", false);*/

    }


    # Простой запрос на выборку к таблице
    public function simple_select($table, $fields = [], $where = [], $order = [], $limit = '', $log = false){

        $structure =  db::do()->structure($table);

        if ($fields == []) $fields = array_keys($structure['fields']);

        $query = "SELECT ".join(', ', $fields)." FROM `".$table."`";

        if ($where != []) $query .= " WHERE ".join(" AND ", $where);

        if ($order != []) $query .= " ORDER BY ".join(', ', $order);

        $query .= $limit != '' ? ' LIMIT '.$limit : '';

        $res = db::do()->query($query, $log);

        if ($limit == '1'){

            return db::do()->assoc($res);

        } else {

            return db::do()->assoc_all($res);

        }

    }



    # Получение структуры таблицы
    public function structure($table, $rels = true){

         /*$structure = sess::read('structure_'.$table);

         if (is_null($structure)){

            # Индексы

            $res = db::do()->query("SHOW INDEX FROM `".$table."` FROM `".config::do()->setting('dbname')."`", false);

            $structure['indexes']= [];

            while ($row = db::do()->assoc($res)){

                $structure['indexes'][$row['Key_name']][] = $row['Column_name'];

            }

            # Внешние ключи

            if ($rels){

                $q = "SELECT kcu.referenced_table_schema, kcu.constraint_name, kcu.table_name, kcu.column_name, kcu.referenced_table_name, kcu.referenced_column_name, rc.update_rule, rc.delete_rule FROM INFORMATION_SCHEMA.key_column_usage kcu JOIN INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS rc on kcu.constraint_name = rc.constraint_name WHERE kcu.referenced_table_schema = '".config::do()->setting('dbname')."' AND kcu.table_name = '".$table."' AND kcu.referenced_table_name IS NOT NULL ORDER BY kcu.table_name, kcu.column_name";

                $res = db::do()->query($q, false);

                $structure['table'] = $table;

                $structure['foreign'] = [];

                while ($row = db::do()->assoc($res)){

                    $rel_type = 'many-to-';

                    if (in_array($row['COLUMN_NAME'], $structure['indexes']['PRIMARY'])) $rel_type = 'one-to-';

                    $sub_structure = db::do()->structure($row['REFERENCED_TABLE_NAME'], false);

                    if (in_array($row['REFERENCED_COLUMN_NAME'], $sub_structure['indexes']['PRIMARY'])){

                        $rel_type .= 'one';

                    } else {

                        $rel_type .= 'many';

                    }

                    $structure['foreign'][] = ['type' => $rel_type, 'column' => $row['COLUMN_NAME'], 'ref_table' => $row['REFERENCED_TABLE_NAME'], 'ref_column' => $row['REFERENCED_COLUMN_NAME'], 'delete' => $row['DELETE_RULE'], 'update' => $row['UPDATE_RULE']];

                }

            }

            # Поля

            $res = mysqli_query(self::$connection, "SELECT COLUMN_NAME, COLUMN_COMMENT, ORDINAL_POSITION, COLUMN_DEFAULT, COLUMN_TYPE, IS_NULLABLE, EXTRA FROM information_schema.`COLUMNS` WHERE TABLE_SCHEMA = '".config::do()->setting('dbname')."' AND TABLE_NAME = '".$table."' ORDER BY ORDINAL_POSITION");

            while ($row = db::do()->assoc($res)){

                $length = preg_replace('/[^0-9,]/', '', $row['COLUMN_TYPE']);

                $label = $row['COLUMN_COMMENT'];

                $structure['fields'][$row['COLUMN_NAME']] = ['label' => $label, 'type' => preg_replace('/[\(\)0-9,]/', '', $row['COLUMN_TYPE']), 'length' => (int)$length, 'null' => ($row['IS_NULLABLE'] == 'YES' ? 1 : 0), 'default' => $row['COLUMN_DEFAULT'], 'auto' => ($row['EXTRA'] == 'auto_increment') ? 1 : 0];

            }    

            if (config::do()->setting('mode') == 'prod') sess::write('structure_'.$table, $structure);

         }
         
         
        
         return $structure;
         */

    }


    # Количество затронутых в последнем запросе строк
    public function affected(){

        return mysqli_affected_rows(self::$connection);
    }


    # id последней записанной строки
    public function last_id(){

        return mysqli_insert_id(self::$connection);

    }


    # Экранирование спец. символов
    public function clean($text){

        return mysqli_real_escape_string(self::$connection, $text);

    }

    public function rows($res){

        return mysqli_num_rows($res);

    }

    public function save($table, $input, $mode = 'insert'){

        $structure = self::do()->structure($table, false);

        if ($mode == 'insert'){

            $query = "INSERT INTO `$table` ";

        } else {

            $query = "UPDATE `$table` SET ";

        }

        $fields = [];

        foreach($structure['fields'] as $name => $field){

            if(in_array($name, ['created_at', 'updated_at']) || $field['auto'] == 1) continue;

            if($field['null'] == 0 && (!isset($input[$name]) || $input[$name] == null)) $input[$name] = $field['default'];

            if(isset($input[$name])){

                switch($field['type']){

                    case "tinyint":

                    case "int":

                        $input[$name] = (int)$input[$name];

                        break;

                    default:

                        $input[$name] = self::do()->clean((string)$input[$name]);

                        break;

                }

                $length = strlen((string)$input[$name]);

                if ($length > $field['length'] && $field['length'] != 0){
                    
                    //notification::do()->add('Максимальная длина значения '.$field['label'].' - '.$field['length'].' символов, передано - '.$length.'!', 'input_error');

                    return false;

                } else {

                    $fields[$name] = $input[$name];

                }    

            }

        }

        if(count($fields) > 0){

            if ($mode == 'insert'){

                $query .= "(`".join('`, `', array_keys($fields))."`) VALUES ('".join("', '", $fields)."')";

            } else {

                $vals = [];

                foreach($fields as $index => $value){

                    $vals[] = "`$index` = '$value'";

                }

                $query .= join(', ', $vals);

                $query .= " WHERE id = ".$input['id'];
            }    

        } else {

            //notification::do()->add('Нечего сохранять!', 'info');

            return false;

        }
       
        if (db::do()->query($query)){

           return $mode == 'insert' ? db::do()->last_id() : $input['id']; 

        } else {
          
           return false;

        }

    }


}
