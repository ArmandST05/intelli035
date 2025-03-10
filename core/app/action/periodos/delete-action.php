<?php
// Verificar que se ha enviado un ID a través de GET
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    // Obtener el ID del periodo a eliminar
    $idPeriodo = intval($_GET["id"]);
    
    
    // Llamar al método delete() pasando el ID
    PeriodoData::delete($idPeriodo);
    Core::alert("¡Periodo eliminado exitosamente!");
    // Redirigir a la lista de puestos después de la eliminación
    print "<script>window.location='index.php?view=periodos/index';</script>";
} else {
    echo "¡ID no válido para eliminar!<br>";
    print "<script>window.location='index.php?view=periodos/index';</script>";
}
?>
