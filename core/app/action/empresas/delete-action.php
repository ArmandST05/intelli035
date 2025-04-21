<?php
if (isset($_GET["id"]) && !empty($_GET["id"])) {
    $idEmpresa = intval($_GET["id"]);
    EmpresaData::delete($idEmpresa);
    Core::alert("¡Empresa eliminada exitosamente!");
    print "<script>window.location='index.php?view=empresas/index';</script>";
} else {
    Core::alert("¡ID no válido para eliminar!");
    print "<script>window.location='index.php?view=empresas/index';</script>";
}
