<?php
/**
 * EXAMPLE ROUTES
 */

class User{
    public function home(){
        echo "List of users ";
    }

    public function get(){
        echo "Get user ".$_GET['id']."<br>";
        echo "Route: ".Route::getByName('user.get', ['{id}' => 2]);
    }

    public function create(){
        echo "Create user";
    }

    public function update(){
        echo "Update user";
    }

    public function delete(){
        echo "Delete user ".$_GET['id'];
    }

    public function adminPanel(){
        echo "Admin Panel";
    }

}



Route::get('/', [User::class, 'home']);
Route::get('/home', [User::class, 'home'])->name('user.home');


Route::get('/user/{id}', [User::class, 'get'])->name('user.get');
Route::post('/user', [User::class, 'create'])->name('user.create');
Route::put('/user', [User::class, 'update'])->name('user.update');
Route::delete('/user/{id}', [User::class, 'delete'])->name('user.update');

$isAdmin = function(){
    return false;
};

$isLogin = function(){
    return true;
};


Route::get('/admin', [User::class, 'adminPanel'])->name('admin.panel')
                                                 ->middleware($isLogin)
                                                 ->middleware($isAdmin);



        


