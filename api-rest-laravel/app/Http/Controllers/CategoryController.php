<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Category;

class CategoryController extends Controller {

    //utilizamos esta manera para llamar al middleware , ya que si lo hubiesemos colocado en web.php 
    //Pediria autenticacion a todos los metodos 

    public function __construct() {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    //Metodos creados de manera automatica por laravel en la URL (resouces)

    /* php artisan route:list metodo para ver las rutas creadas de manera automatica,
      crea todas las acciones posibles relacionadas a los crud y les asigna un nombre por defecto */

    //Muestra todas las categorias
    public function index() {
        $categories = Category::all();

        return response()->json([
                    'code' => 200,
                    'status' => 'success',
                    'categories' => $categories
        ]);
    }

    //Muestra una categoria que se busque utilizando la id como parametro de busqueda
    public function show($id) {
        $category = Category::find($id);
        if (is_object($category)) {
            $data = [
                'code' => 200,
                'status' => 'success',
                'category' => $category
            ];
        } else {
            $data = [
                'code' => 404,
                'status' => 'error',
                'message' => 'La categoria no existe'
            ];
        }
        return response()->json($data, $data['code']);
    }

    //Inserta una categoria 
    public function store(Request $request) {
        //Recoger los datos por post 
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //validar los datos 
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            //Guardar la categoria 
            if ($validate->fails()) {
                $data = [
                    'code' => 400,
                    'status' => 'error',
                    'message' => 'No se ha guardado la categoria'
                ];
            } else {
                $category = new Category();
                $category->name = $params_array['name'];
                $category->save();

                $data = [
                    'code' => 200,
                    'status' => 'success',
                    'category' => $category
                ];
            }
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoria'
            ];
        }
        //devolver el resultado 
        return response()->json($data, $data['code']);
    }

    public function update($id, Request $request) {
        //Recoger los datos por post 
        $json = $request->input('json', null);
        $params_array = json_decode($json, true);

        if (!empty($params_array)) {
            //Validar lo datos 
            $validate = \Validator::make($params_array, [
                'name' => 'required'
            ]);

            //Quitar lo que no se actualizara 
            unset($params_array['id']);
            unset($params_array['created_at']);

            //Actualizar el registro(Categoria)
            $category = Category::where('id', $id)->update($params_array);

            $data = [
                'code' => 200,
                'status' => 'success',
                'message' => $params_array
            ];
        } else {
            $data = [
                'code' => 400,
                'status' => 'error',
                'message' => 'No has enviado ninguna categoria'
            ];
        }

        //Devolver respuesta 
        return response()->json($data, $data['code']);
    }

}
