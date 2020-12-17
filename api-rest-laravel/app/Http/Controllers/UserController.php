<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\User;

class UserController extends Controller {

    public function pruebas(Request $request) {
        return "Accion de pruebas de USER-CONTROLLER";
    }

    public function register(Request $request) {

        //Recoger los datos del usuario por post 
        $json = $request->input('json', null);
        //decodificarlo para poder trabajar con el como objeto o array
        $params = json_decode($json); //objeto generico de php
        $params_array = json_decode($json, true); //array


        if (!empty($params) && !empty($params_array)) {
            //Limpiar datos
            $params_array = array_map('trim', $params_array); //quita los espacios finales
            //Validar datos
            /* se utilizan metodos predefinidos de validaciones de laravel

             * REQUIRED:QUE ES OBLIGATORIO
             * ALPHA: QUE SOLO PUEDE TENER ALFANUMERICOS
             * EMAIL:QUE TIENE QUE TENER UN FORMATO DE EMAIL
             */
            //importacion de la libreria Validator (tambien se puede hacer el import en el namespace)
            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users', //Comprobar si el usuario existe ya (duplicado)
                'password' => 'required'
            ]);

            if ($validate->fails()) {
                //La validacion ha fallado
                $data = array(
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'El usuario no se ha creado',
                    'errors' => $validate->errors()
                );
            } else {
                //La validacion pasado correctamente
                //Cifrar la contraseña
                $pwd = hash('sha256', $params->password);

                //Crear el usuario
                $user = new User();
                $user->name = $params_array['name']; //primera parte se pide el parametro nombre del usuario , despues del = se pide el parametro de nombre del array
                $user->surname = $params_array['surname'];
                $user->email = $params_array['email'];
                $user->password = $pwd;
                $user->role = 'ROLE_USER';

                //Guardar el usuario
                $user->save();

                $data = array(
                    'status' => 'success',
                    'code' => 200,
                    'message' => 'El usuario se ha creado correctamente',
                    'user' => $user
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'Los datos enviados no son correctos'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request) {

        $jwtAuth = new \JwtAuth();

        //Recibir datos por POST
        $json = $request->input('json', null);
        $params = json_decode($json);
        $params_array = json_decode($json, true);
        //Validar esos datos 

        $validate = \Validator::make($params_array, [
            'email' => 'required|email', //Comprobar si el usuario existe ya (duplicado)
            'password' => 'required'
        ]);

        if ($validate->fails()) {
            //La validacion ha fallado
            $singup = array(
                'status' => 'error',
                'code' => 404,
                'message' => 'El usuario no se ha podido logear',
                'errors' => $validate->errors()
            );
        } else {
            //Cifrar la password
            $pwd = hash('sha256', $params->password);

            //Devolver token o datos 
            $singup = $jwtAuth->singup($params->email, $pwd);
            if (!empty($params->gettoken)) {
                $singup = $jwtAuth->singup($params->email, $pwd, true);
            }
        }

        return response()->json($singup, 200);
    }

    public function update(Request $request) {
        //Comprobar si el usuario esta identificado 
        $token = $request->header('Authorization');
        $JwtAuth = new \JwtAuth();
        $checkToken = $JwtAuth->checkToken($token);

        //recoger los datos por post    
        $json = $request->input('json', null); //recojer el parametro(datos) json y en caso de que no lo reciba recivira un null 
        $params_array = json_decode($json, true); //lo convierte en un objeto de javascript , en ves de un objeto json como viene por default 

        if ($checkToken && !empty($params_array)) {

            //Sacar usuario identificado

            $user = $JwtAuth->checkToken($token, true);

            //validar datos 

            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha',
                'surname' => 'required|alpha',
                'email' => 'required|email|unique:users' . $user->sub
            ]);

            //Quitar los campos que no quiero actualizar

            unset($params_array['id']);
            unset($params_array['role']);
            unset($params_array['password']);
            unset($params_array['created_at']);
            unset($params_array['remember_token']);

            //Actualizar usuario en bd 

            $user_update = User::where('id', $user->sub)->update($params_array);
            //Devolver array con reesultado

            $data = array(
                'code' => 200,
                'status' => 'succes',
                'user' => $user,
                'changes' => $params_array
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no esta identificado'
            );
        }
        return response()->json($data, $data['code']);
    }

    public function upload(Request $request) {
        //Recoger datos de la peticion 
        $image = $request->file('file0');

        //Calidacion de imagen
        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image|mimes:jpg,jpeg,png,gif'
        ]);

        if (!$image || $validate->fails()) {
            //Si la imagen es false o la validacion falla 

            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Error al subir la imagen'
            );
        } else {
            //guardar imagen 

            $image_name = time() . $image->getClientOriginalName();
            \Storage::disk('users')->put($image_name, \File::get($image));

            $data = array(
                'code' => 200,
                'status' => 'succes',
                'image' => $image_name
            );
        }
        return response()->json($data, $data['code']);
    }

    //Obtener imagen del usuario
    public function getImage($filename) {
        // Buscar si existe el archivo con el metodo exist
        $isset = \Storage::disk('users')->exists($filename);
        if ($isset) {
            // Obtener el archivocon el metodo get
            $file = \Storage::disk('users')->get($filename);
            return new Response($file, 200);
        } else {
            $data = array(
                'code' => 404,
                'status' => 'error',
                'message' => 'La imagen no existe'
            );
            return response()->json($data, $data['code']);
        }
    }

    public function detail($id) {
        $user = User::find($id);

        if (is_object($user)) {
            $data = array(
                'code' => 200,
                'status' => 'success',
                'user' => $user
            );
        } else {
            $data = array(
                'code' => 400,
                'status' => 'error',
                'message' => 'El usuario no existe'
            );
        }
        return response()->json($data, $data['code']);
    }

}
