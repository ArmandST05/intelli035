<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include_once 'core/autoload.php'; // Ajusta según tu estructura

    $empresa_id = isset($_POST['empresa_id']) ? (int)$_POST['empresa_id'] : 0;

    if ($empresa_id == 0) {
        die("Error: No se recibió el ID de la empresa.");
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] != UPLOAD_ERR_OK) {
        die("Error: No se subió ningún archivo o hubo un problema con la carga.");
    }

    $file = $_FILES['file'];
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_extensions)) {
        die("Error: Formato de archivo no permitido.");
    }

    // Ruta donde se guardará la imagen
    $upload_dir = "core/app/view/empresas/logos/";
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Crea la carpeta si no existe
    }

    // Nombre único para evitar sobrescribir archivos
    $new_filename = "logo_" . $empresa_id . "." . $ext;
    $file_path = $upload_dir . $new_filename;

    // Mover archivo al servidor
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        die("Error: No se pudo guardar el archivo.");
    }

    // Guardar la ruta en la base de datos
    $empresa = EmpresaData::getById($empresa_id);
    if (!$empresa) {
        die("Error: La empresa no existe.");
    }

    $empresa->updateLogo($file_path);

    echo "Logo subido exitosamente.";
}
?>
