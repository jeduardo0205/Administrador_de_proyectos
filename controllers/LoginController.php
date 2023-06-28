<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router $router){
        $alertas = [];
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            
            $alertas = $usuario->validarLogin();

            if(empty($alertas)){
                // Verificar que el usuario exista
                $usuario = Usuario::where('email', $usuario->email);

                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'El Usuario no existe o la cuenta no esta confirmada');
                }else{
                    // El usuario existe
                    if(password_verify($_POST['password'], $usuario->password)){
                        // Iniciar sesion
                        session_start();
                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccionar
                        header('Location: /dashboard');

                    }else{
                        Usuario::setAlerta('error', 'Password Incorrecto');
                    }
                }
            }
        }
        $alertas = Usuario::getAlertas();

        // Render a la vista
        $router->render('auth/login',[
            'titulo' => 'Iniciar Sesión',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
       session_start();
       $_SESSION = [];
       header('Location: /');
    }

    public static function crear(Router $router){
        $alertas = [];
        $usuario = new Usuario;

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            $existeUsuario = Usuario::where('email', $usuario->email);

            if(empty($alertas)){
                if($existeUsuario){
                    Usuario::setAlerta('error', 'El Email ya se encuentra registrado');
                    $alertas = Usuario::getAlertas();
                } else{
                // Hashear password
                    $usuario->hashPassword();

                // Eliminar password2
                    unset($usuario->password2);

                // Generar Token
                    $usuario->crearToken();

                // Crear un nuevo usuario
                    $resultado = $usuario->guardar();
                
                // Enviar Email
                $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                $email->enviarConfirmacion();

                if($resultado){
                        header('Location: /mensaje');
                    }
                }
            }
        }


         // Render a la vista
         $router->render('auth/crear',[
            'titulo' => 'Registrarme',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }

    public static function olvide(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEmail();

            if(empty($alertas)){
                // Buscar el usuario
                $usuario = Usuario::where('email', $usuario->email);

                if($usuario && $usuario->confirmado){
                    // Generar un nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);

                    // Actualizar al usuario
                    $usuario->guardar();

                    // Enviar el email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Imprimir la alerta
                    Usuario::setAlerta('exito', 'Se a enviado un correo electronico a ' . $usuario->email . ' con las instrucciones para restablecer tu contraseña');          
                }else{
                    Usuario::setAlerta('error', 'El Usuario no existe o la cuenta no se ha confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide',[
            'titulo' => 'Recuperar Contraseña',
            'alertas' => $alertas

        ]);
    }

    public static function restablecer(Router $router){
        $token = s($_GET['token']);
        $mostrar = true;

        if(!$token) header('Location: /');

        // Identificar el usuario con el token
        $usuario = Usuario::where('token', $token);        
        
        if(empty($usuario)) {
            Usuario::setAlerta('error', 'Token no Valido');
            $mostrar = false;
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Añadir el nuevo password
            $usuario->sincronizar($_POST);

            // Validar el password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                // Insertar el nuevo password
                $usuario->hashPassword();

                // Eliminar el token
                $usuario->token = null;

                // Guardar el usuario en la BD
                $resultado = $usuario->guardar();

                // Redireccionar
                if($resultado){
                    header('Location: /');
                }
            }
        }

        $alertas = Usuario::getAlertas();
         // Render a la vista
         $router->render('auth/restablecer',[
            'titulo' => 'Restablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){
         // Render a la vista
         $router->render('auth/mensaje',[
            'titulo' => 'Cuenta creada'
        ]);
    }

    public static function confirmar(Router $router){
       $token = s($_GET['token']);
       if(!$token) header('Location: /');

    //    Encontrar al usuario por token
    $usuario = Usuario::where('token', $token);

    if(empty($usuario)){
        Usuario::setAlerta('error', 'Token No Valido');
    }else{
        // Confirmar la cuenta
        $usuario->confirmado = 1;
        $usuario->token = null;
        unset($usuario->password2);

        // Guardar en la BD
        $usuario->guardar();

        // Mostrar Alerta
        Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
    }
    
    $alertas = Usuario::getAlertas();

        // Render a la vista
          $router->render('auth/confirmar',[
            'titulo' => 'Confirmación de cuenta',
            'alertas' => $alertas
        ]);
    }
}