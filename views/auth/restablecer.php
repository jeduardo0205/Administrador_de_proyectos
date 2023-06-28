<div class="contenedor restablecer">
 <?php include_once __DIR__ . '/../templates/nombre-sitio.php' ?>  
    
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu Password en UpTask</p>
        
        <?php include_once __DIR__ . '/../templates/alertas.php' ?> 
        
        <?php if($mostrar){ ?>

        <form class="formulario" method="POST">
          <div class="campo">
                <label for="password">Password:</label>
                <input type="password" id="password" placeholder="Tu Password" name="password">
            </div>

            <input type="submit" class="boton" value="Actualizar Password">
        </form>

        <?php  }  ?>

            <div class="acciones">
                <a href="/">¿Ya tienes una cuenta? Iniciar Sesión</a>
                <a href="/crear">¿Aún no tienes una cuenta? Registrarme</a>
            </div>
    </div>
</div>