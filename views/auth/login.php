<div class="contenedor login">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php' ?>  

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Inicar Sesión</p>

        <?php include_once __DIR__ . '/../templates/alertas.php' ?>  
        
        <form class="formulario" method="POST" action="/" novalidate>
            <div class="campo">
                <label for="email">Email:</label>
                <input type="email" id="email" placeholder="Tu Correo Electronico" name="email">
            </div>

            <div class="campo">
                <label for="password">Password:</label>
                <input type="password" id="password" placeholder="Tu Password" name="password">
            </div>

            <input type="submit" class="boton" value="Iniciar Sesión">

            <div class="acciones">
                <a href="/crear">¿Aún no tienes una cuenta? Registrarme</a>
                <a href="/olvide">Olvide mi Contraseña</a>
            </div>
        </form>
    </div>
</div>