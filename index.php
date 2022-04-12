<?php

require __DIR__.'/router/router.php';
require __DIR__.'/routes.php';


try{
    
    Route::setBaseUrl('localhost');
    Route::load();

}catch(RouteHttpException $e){
    
    switch($e->getCode()){
        case 404: 
             header('HTTP/1.1 404 NOT FOUND');
        break;
        case 403:
            header('HTTP/1.1 403 FORBIDEN');
        break;
    }

}


