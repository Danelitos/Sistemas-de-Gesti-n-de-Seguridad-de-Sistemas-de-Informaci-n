<?php
    
    function anadirLog($correo,$entrada,$fechaHora){ 
        require 'conexion.php';
        $conn->set_charset("utf8");  
        $sql = "INSERT INTO `log` (correoUsuario,entrada,fechaHora) VALUES (?,?,?)";
        $stmt = $conn->prepare($sql);
        $intentos=0;
        $estado="activo";
        $stmt->bind_param('sss', $correo,$entrada,$fechaHora);
        $stmt->execute();

        if ($entrada=="fallida"){ //añadir intento fallido al usuario
            $sql="UPDATE usuarios SET IntentosFallidos=IntentosFallidos+1 WHERE Email='$correo'";

            if (mysqli_query($conn, $sql)) {
                echo "<script> alert('Intento fallido añadido(MAX. 3)') </script>";
            } else {
                echo "<script> alert('Error al meter el log') </script>";
            }

            //verificar si se ha llegado a 3 intentos fallidos
            $sql = $conn->query("SELECT IntentosFallidos FROM usuarios WHERE Email='$correo'");
            if (mysqli_num_rows($sql) > 0) {
                $result = $sql->fetch_assoc();
                $intentos = $result['IntentosFallidos'];
            }

            if ($intentos>=3){ //si es mayor a 3, se desactiva la cuenta y se reinicia a 0

                $sql="UPDATE usuarios SET IntentosFallidos=0,Estado='inactivo' WHERE Email='$correo'";

                if (mysqli_query($conn, $sql)) {
                    echo "<script> alert('Cuenta en estado inactivo') </script>";
                } else {
                    echo "<script> alert('Error al desactivar cuenta') </script>";
                }
            }

        }
        
        else{ // reiniciar los intentos fallidos a 0 
            $sql="UPDATE usuarios SET IntentosFallidos=0 WHERE Email='$correo'";
            if (mysqli_query($conn, $sql)) {
                
            } else {
                echo "<script> alert('Error al meter el log') </script>";
            }
        }

        return true;
    }
    function timeOut(){
        if(isset($_SESSION['tiempo']) ) {

            //Tiempo en segundos para dar vida a la sesión.
            $inactivo = 119;//2min en este caso.
        
            //Calculamos tiempo de vida inactivo.
            $vida_session = time() - $_SESSION['tiempo'];
        
                //Compraración para redirigir página, si la vida de sesión sea mayor a el tiempo insertado en inactivo.
                if($vida_session > $inactivo)
                {
                    //Removemos sesión.
                    session_unset();
                    //Destruimos sesión.
                    session_destroy();              
                    //Redirigimos pagina.
                    header("Location: login.php");
        
                    exit();
                } 
        
        
        } else {
            //Activamos sesion tiempo.
            $_SESSION['tiempo'] = time();
        }
    }

    

?>