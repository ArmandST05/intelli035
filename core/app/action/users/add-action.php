<?php
if (count($_POST) > 0) {
    // Crear un nuevo usuario
    $user = new UserData();
    $user->name = $_POST["name"];
    $user->lastname = $_POST["lastname"];
    $user->email = $_POST["email"];
    $user->date_of_birth = $_POST["dob"];
    $user->username = $_POST["username"];
    $user->password = sha1(md5($_POST["password"])); // Mantener encriptación
    $user->user_type = $_POST["user_type"];
    $user->fecha_inicio = $_POST["fechaInicio"];
    $user->fecha_fin = $_POST["fechaFin"];

    
    /*
    // Procesar la imagen
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
        $imageData = file_get_contents($_FILES["image"]["tmp_name"]);
        $base64Image = base64_encode($imageData);
        $user->image = $base64Image;
    } else {
        $user->image = null; // Si no se sube imagen, se deja como null
    }
*/
    // Guardar usuario
    $user->add();

    // Redirigir a la lista de usuarios
    print "<script>window.location='index.php?view=users/index';</script>";
}
?>
