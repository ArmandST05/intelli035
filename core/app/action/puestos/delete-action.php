<?php
// Verificar que se ha enviado un ID a través de GET
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    // Obtener el ID del puesto a eliminar
    $idPuesto = intval($_GET["id"]);

    // Llamar al método delete() pasando el ID del puesto
    PuestoData::delete($idPuesto);

    // Mostrar un mensaje de éxito
    Core::alert("¡Puesto eliminado exitosamente!");

    // Redirigir a la lista de puestos después de la eliminación
    print "<script>window.location='index.php?view=puestos/index';</script>";
} else {
    // En caso de que no se haya enviado un ID válido
    Core::alert("¡ID no válido para eliminar!");
    print "<script>window.location='index.php?view=puestos/index';</script>";
}
?>
