<?php

/*                                                                                                                                                                              
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

/*Import de Middlewares: Estos sirven para ejecutar acciones antes de una consulta http*/

use App\Http\Middleware\ApiAuthMiddleware;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/welcome', function(){
    return view('welcome');
});

Route::get('/pruebas', function(){
    return ('<h2>Texto desde una ruta </h2>');
});

Route::get('/test-orm','PruebasController@testOrm');


//-RUTAS DEL API

/*Metodos HTTP comunes
 * GET :Conseguir datos o recursos 
 * POST: Guardar datos o recursos o hacer logicadesde un formulario
 * PUT: Actualizar datos o recursos 
 * DELETE: Eliminar datos o recursos 
 */


//Rutas de prueba
/*
Route::get('/usuario/pruebas','UserController@pruebas');
Route::get('/category/pruebas','CategoryController@pruebas');
Route::get('/post/pruebas','PostController@pruebas');
*/

//Rutas del controlador de usuarios 

Route::post('/api/register','UserController@register');
Route::post('/api/login','UserController@login');
Route::put('/api/user/update','UserController@update');
Route::post('/api/user/upload', 'UserController@upload')->middleware(ApiAuthMiddleware::class);
Route::get('/api/user/avatar/{filename}','UserController@getImage');
Route::get('/api/user/detail/{id}','UserController@detail');


//Rutas del controllador de categorias 

Route::resource('/api/category','CategoryController');


//Rutas del controllador de post 

Route::resource('/api/post','PostController');
Route::post('/api/post/upload', 'PostController@upload');
Route::get('/api/post/image/{filename}', 'PostController@getImage');
Route::get('/api/post/category/{id}', 'PostController@getPostByCategory');
Route::get('/api/post/user/{id}', 'PostController@getPostByUser');