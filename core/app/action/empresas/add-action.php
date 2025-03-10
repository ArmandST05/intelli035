<?php 
if (count($_POST) > 0) {
    $newEmpresa = new EmpresaData();

    $newEmpresa -> nombre = trim($_POST["id_nombre"]);
    $newEmpresa -> comentarios = $_POST["id_comentarios"];
    $newEmpresa -> id_cantidad= $_POST["id_cantidad"];


        $result = $newEmpresa->add(); // Guarda en la base de datos
        Core::alert("¡Empresa agregada exitosamente!");
        print "<script>window.location='index.php?view=empresas/index';</script>";
}        





?>