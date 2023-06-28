<?php

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];
    
    public function __construct($args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nuevo = $args['password_nuevo'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    // Validar login del usuario
    public function validarLogin(){
        if(!$this->email){
            self::$alertas['error'][] = 'Ingrese su Email';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'Introduzca un Password';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Correo Electronico no Valido';
        }

        return self::$alertas;
    }

    // Validacion para nuevas cuentas
    public function validarNuevaCuenta(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'Introduzca su Nombre';
        }

        if(!$this->email){
            self::$alertas['error'][] = 'Ingrese su Email';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'Introduzca un Password';
        }

        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'Introduzca un Password mayor de 6 caracteres';
        }

        if($this->password !== $this->password2 ){
            self::$alertas['error'][] = 'Los Password No Coinciden';
        }

        return self::$alertas;
    }

    public function validarEmail(){
        if(!$this->email){
            self::$alertas['error'][] = 'Introduzca su Correo Electronico';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Correo Electronico no Valido';
        }

        return self::$alertas;
    }

    public function validarPassword(){
        if(!$this->password){
            self::$alertas['error'][] = 'Introduzca un Password';
        }

        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'Introduzca un Password mayor de 6 caracteres';
        }

        return self::$alertas;
    }

    public function validar_perfil(){
        if(!$this->nombre){
            self::$alertas['error'][] = 'Introduzca su Nombre';
        }
        if(!$this->email){
            self::$alertas['error'][] = 'Introduzca su Email';
        }
        return self::$alertas;
    }

    public function nuevo_password() : array {
        if(!$this->password_actual){
            self::$alertas['error'][] = 'Introduzca su Password Actaul';
        }
        if(!$this->password_nuevo){
            self::$alertas['error'][] = 'Introduzca su Nuevo Password Actaul';
        }
        if(strlen($this->password_nuevo) < 6){
            self::$alertas['error'][] = 'Introduzca un Password mayor de 6 caracteres';
        }
        return self::$alertas;
    }

    public function comprobar_password() : bool{
        return password_verify($this->password_actual, $this->password);
    }

    public function hashPassword() : void{
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    // Generar token
    public function crearToken() : void{
        $this->token = uniqid();
    }
}