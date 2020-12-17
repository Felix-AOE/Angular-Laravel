<?php

namespace App\Helpers;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\DB;
use App\User;

class JwtAuth {

    public $key;

    public function __construct() {
        $this->key = 'esto_es_una_clave_super_secreta-9999929';
    }

    public function singup($email, $password, $getToken = null) {
        //Buscar si existe el usuario con sus credenciales 
        
        $user = User::where([ //funcion del ORM para extraer los datos con un WHERE
            'email' => $email,
            'password' => $password
        ])->first();

        //Comprobar si son correctas(objeto)

        $singup = false;
        if (is_object($user)) { //metodo de php para ver si es un objeto la variable user
            $singup = true;
        }
        //Generar el token con los datos del usuario identificado

        if ($singup) {

            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'description' => $user->description,
                'image' => $user->image,
                'iat' => time(),
                'exp' => time() + (7 * 24 * 60 * 60)
            );

            $jwt = JWT::encode($token, $this->key, 'HS256');
            $decoded = JWT::decode($jwt, $this->key, ['HS256']);

            //Devolver los datos decodificados o el token , en funcion de un parametro
            if (is_null($getToken)) {
                //Se envia en formato JWT
                $data = $jwt;
            } else {
                //Se envia en formato array
                $data = $decoded;
            }
        } else {

            $data = array(
                'status' => 'error',
                'message' => 'Login incorrecto'
            );
        }

        return $data;
    }
    
    public function checkToken($jwt, $getIdentity = false) {
        $auth = false;
        
        try{
            $jwt = str_replace('"', '', $jwt) ;
            $decoded = JWT::decode($jwt, $this->key,['HS256']);
        } catch (\UnexpectedValueException $e){
            $auth = false;
        } catch (\DomainException $e){
            $auth = false;
        }
        
        if (!empty($decoded) && is_object($decoded) && isset($decoded->sub)) {// verifica que el parametro que nos llega por el header Autorization no llegue vacio , que sera un objeto y que sera una variable definida distinta a null 
             $auth = true;
        }else{
             $auth = false;
        }
        
        if ($getIdentity) {
            return $decoded;
        }
        
        return $auth;
         
    }
    

}
