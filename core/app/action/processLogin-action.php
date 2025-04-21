<?php
    // define('LBROOT',getcwd()); // LegoBox Root ... the server root
    // include("core/controller/Database.php");
    if(!isset($_SESSION["user_id"])) {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $base = new Database();
        $con = $base->connect();
        
        // Validación en la tabla `users`
        $sql_users = "SELECT * FROM users WHERE (email = '".$user."' OR username = '".$user."') AND password = '".sha1(md5($pass))."'";
        $query_users = $con->query($sql_users);
        $found_user = false;
        $userid = null; 
        $username = null;
        $type = null;
        while($r = $query_users->fetch_array()){
            $found_user = true;
            $userid = $r['id'];
            $username = $r['username'];
            $type = $r['user_type'];
        }
        // Si no se encontró en `users`, buscar en la tabla `personal`
        if (!$found_user) {
            $sql_personal = "SELECT * FROM personal WHERE usuario = '".$user."' AND clave = '".$pass."'";
            $query_personal = $con->query($sql_personal);
            $found_personal = false;
            $personal_id = null;
            $personal_username = null;
            while($r = $query_personal->fetch_array()){
                $found_personal = true;
                $personal_id = $r['id'];
                $personal_username = $r['usuario'];
            }
            if ($found_personal) {
				// Sesión para el empleado
				$_SESSION['user_id'] = $personal_id;
				$_SESSION['typeUser'] = 'e'; // O el tipo que prefieras asignar
				$_SESSION['user_name'] = $personal_username; // Guarda el nombre de usuario en la sesión
				print "Cargando ... $personal_username";
				print "<script>window.location='index.php?view=home-personal';</script>";
				exit;
			}
			
        }
        if ($found_user) {
            // Sesión para el usuario del sistema
            $_SESSION['user_id'] = $userid;
            $_SESSION['typeUser'] = $type;
            print "Cargando ... $user";
            print "<script>window.location='index.php?view=home';</script>";
        } else {
            print "<script>
                alert('Verifica tus datos');
                window.location='index.php?view=login';
            </script>";
        }
    } else {
        print "<script>
            alert('Ya tienes una sesión activa');
            window.location='index.php?view=home';
        </script>";
    }
?>