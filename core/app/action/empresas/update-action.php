<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["id"]) && !empty($_POST["id"])) {
        $empresa = EmpresaData::getByCantidad($_POST["id"]);

        if ($empresa) {
            $empresa->nombre = $_POST["id_nombre"];
            $empresa->comentarios = $_POST["id_comentarios"];
            $empresa->id_cantidad = $_POST["id_cantidad"];

            $resultado = $empresa->update(); // Asegúrate de que este método exista

            if ($resultado) {
                echo "success";
            } else {
                echo "error";
            }
        } else {
            echo "empresa_no_encontrada";
        }
    } else {
        echo "id_invalido";
    }
} else {
    echo "metodo_no_permitido";
}

