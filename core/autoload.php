<?php
    
    define('API_ACCESS_KEY','AAAAnVJ7GwA:APA91bEfWkA2cNx-mAVmcwL2fJdexDa54rAb0VjbZoNB3GCWnLh4TezojG1vCJBb8rE-PRBpVpts-_lcv_a3DnCcIH5gtFrHEbfrDKnPTPKxXxDEit1EKk6clLIVCZplA7xaBSD7yYFJ');

    define('__CONFIG__', __DIR__.'/../config.ini');
      
    define('__ROOT__', realpath(__DIR__.'/../'));
    
    define('__SALT__', 'STOP_THIS_SHIT');

    spl_autoload_register(function ($class) {

        if (file_exists(__ROOT__.'/lib/classes/' . $class . '.php')){

            include __ROOT__.'/lib/classes/' . $class . '.php';

        } 

    });


    function func($name, ...$args){
            
            if (!function_exists($name)) {
                
                require_once  __DIR__.'/../lib/'.preg_replace('/_.*/', '', $name, 1).'.php';
            
            }
            
            return call_user_func($name, ...$args);
      
    }

    //util::do()->csrf_token();