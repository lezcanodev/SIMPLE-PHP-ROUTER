# SIMPLE PHP ROUTER
Pasos para usarlo:
- Descargar ".htaccess" el cual indicara las directivas al servidor
- Descargar "router/router.php" es el archivo que se encargara de resolver las rutas
- Descargar "index.php" en el esta el codigo para cargar las rutas
- Por ultimo pueden descargar el archivo routes.php en modo de ejemplo

## Como usar
- ##### **Definir una ruta base**
Para definir la ruta base del proyecto solo llamamos al metodo estatico **setBaseUrl**, ejemplo:

		Route::setBaseUrl('localhost');
- ##### **Definir rutas**
Para definir las rutas solo hay que llamar al metodo estatico de la clase **Route** (los metodos estaticos tienen los mismos nombres que los HTTP Verbs).

  Un metodo estatico recibe dos parametros el primero indicara la ruta, el segundo es un array el cual recibe dos elementos el primer elemento es la clase y el segundo el metodo que tiene que llamar, ejemplos:

		//{id} al estar entre llaves indica que es un parametro
		
		Route::get('/user/{id}', [User::class, 'get']);
		Route::post('/user', [User::class, 'create']);
		Route::put('/user', [User::class, 'update']);
		Route::delete('/user/{id}', [User::class, 'delete']);
- ##### **Cargar las rutas**
Para cargar las rutas solo llamamos al metodo estatico **load()**, en caso de no encontrar la ruta o que no tenga permiso para acceder a tal ruta lanzara una excepcion tipo **RouteHttpException**, ejemplo:

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


- ##### **Utilizar middlewares**
Para utilizar middlewares solo hay que llamar al metodo **middleware($callback)** el cual recibe una callback como parametro, luego de declarar la ruta, ejemplo:
		$isAdmin = function(){
  		return false;
		};
		
		Route::get('/admin', [User::class, 'adminPanel'])->middleware($isAdmin);

  tambien se pueden usar multiples middlewares, ejemplo:
  	$isLogin = function(){
    		return true;
		};
		
		$isAdmin = function(){
  		return false;
		};
		
		Route::get('/admin', [User::class, 'adminPanel'])->middleware($isLogin)->middleware($isAdmin);
- ##### **Nombrar rutas**
Para nombrar las rutas solo llamamos al metodo **name**, luego de nombrar la ruta se pueden utilizar los middlewares

		Route::get('/user/{id}', [User::class, 'get'])->name("user.get");

- ##### **Obtener una ruta por su nombre**
Solo llamamos al metodo estatico getByName, el cual recibe dos parametros el primero el nombre de la ruta y el segundo un arrays con los parametros de la ruta si es que los tiene, ejemplo:

		Route::get('/user/{id}', [User::class, 'get'])->name("user.get");
		Route::getByName("user.get", [
		 "{id}" => 3
		]);
