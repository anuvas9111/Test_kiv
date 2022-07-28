<?php

# Общий синглтон класс. Вызов всех методов через classname::do()->method_name

trait singleton

{

    private static $instance;

    final public static function do(){
        
        static $instance = null;
         
        if (null === $instance){

            $instance = new static();

        }
 
        return $instance;
    }


    # Вывод свойства
    final public static function prop($name){

        return self::$$name;

    }
 
    final protected function __clone() {}
}