<?php

require __DIR__.'/router/router.php';
require __DIR__.'/routes.php';


try{
    Route::load();
}catch(RouteNotFoundExeception $e){ 
    header('HTTP/1.1 404 NOT FOUND');
    echo json_encode([
        'status' => '404',
        'msg'   => 'NOT FOUND'
    ]);
}catch(RouteMiddlewareExeception $e){ 
    header('HTTP/1.1 403 FORBIDEN');
    echo json_encode([
        'status' => '403',
        'msg'   => 'FORBIDEN'
    ]);
}


