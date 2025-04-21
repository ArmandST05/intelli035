<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES['file']) && isset($_POST['empresa_id']) && !empty($_POST['empresa_id'])) {
        $empresa_id = (int) $_POST['empresa_id'];

        $file = $_FILES['file'];
        $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];

        if (!in_array($fileType, ['xls', 'xlsx', 'csv'])) {
            die("❌ Error: El archivo debe ser de tipo .xls, .xlsx o .csv.");
        }

        if (!is_uploaded_file($fileTmpPath)) {
            die("❌ Error: No se pudo cargar el archivo.");
        }

        $cargaData = new CargaData();
        $cargaData->setEmpresaId($empresa_id);

        $fileData = file_get_contents($fileTmpPath);
        if ($fileData === false) {
            die("❌ Error: No se pudo leer el archivo.");
        }

        $insertResult = $cargaData->insertFileToDatabase($fileName, $fileType, $fileData);
        echo "✅ Archivo guardado en la base de datos.<br>";

        if ($fileType == 'xls' || $fileType == 'xlsx') {
            require_once 'vendor/autoload.php';

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmpPath);
            $sheet = $spreadsheet->getActiveSheet();

            $firstRow = true;

            foreach ($sheet->getRowIterator() as $row) {
                if ($firstRow) {
                    $firstRow = false;
                    continue;
                }

                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $rowData[] = strval($cell->getFormattedValue() ?? '');
                }

                if (empty(array_filter($rowData, fn($value) => trim($value) !== ''))) {
                    continue;
                }

                if (count($rowData) === 5) {
                    list($nombre, $puestoNombre, $departamentoNombre, $correo, $telefono) = array_map('trim', $rowData);

                    if ($correo === '') {
                        $correo = "sin correo";
                    }

                    $iniciales = strtoupper(substr($nombre, 0, 1));
                    $palabras = explode(' ', $nombre);
                    if (count($palabras) > 1) {
                        $iniciales .= strtoupper(substr($palabras[1], 0, 1));
                    }
                    $numeroAzar = rand(100000, 999999);
                    $usuario = 'u' . $iniciales . $numeroAzar;

                    $longitudClave = rand(6, 8);
                    $clave = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $longitudClave);

                    // Convertimos a mayúsculas los nombres para evitar duplicados por diferencias de formato
                    $departamentoNombre = strtoupper($departamentoNombre);
                    $puestoNombre = strtoupper($puestoNombre);

                    // Usar métodos que hacen SELECT para evitar duplicados
                    $id_departamento = $cargaData->insertOrGetDepartamento($departamentoNombre);
                    $id_puesto = $cargaData->insertOrGetPuesto($puestoNombre, $id_departamento);

                    // Insertar personal
                    $fecha_alta = date('Y-m-d H:i:s');
                    $insertSuccess = $cargaData->insertIntoPersonal(
                        $nombre,
                        $id_puesto,
                        $id_departamento,
                        $correo,
                        $telefono,
                        $usuario,
                        $clave,
                        $fecha_alta
                    );

                    if (!$insertSuccess) {
                        echo "❌ Error al insertar algunos datos del personal.<br>";
                    }
                } else {
                    echo "⚠️ Advertencia: Filas incompletas o con datos vacíos fueron omitidas.<br>";
                }
            }

            echo "✅ Archivo procesado y datos insertados correctamente.";
        } else {
            echo "⚠️ Advertencia: Procesamiento de archivos CSV aún no implementado.";
        }
    } else {
        die("❌ Error: No se seleccionó archivo o empresa.");
    }
} else {
    die("❌ Error: Método no permitido.");
}
