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

            $firstRow = true; // Variable para omitir la primera fila

            foreach ($sheet->getRowIterator() as $row) {
                if ($firstRow) {
                    $firstRow = false; // Omitir la primera fila (encabezados)
                    continue;
                }

                $rowData = [];
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false);

                foreach ($cellIterator as $cell) {
                    $rowData[] = strval($cell->getFormattedValue() ?? ''); // Convertir null a cadena vacía
                }

                // Verificar si la fila está completamente vacía
                if (empty(array_filter($rowData, fn($value) => trim($value) !== ''))) {
                    continue; // Omitir filas completamente vacías
                }

                // Verificar que la fila tenga exactamente 5 valores
                if (count($rowData) === 5) {
                    list($nombre, $puesto, $departamento, $correo, $telefono) = array_map(fn($val) => trim($val ?? ''), $rowData);

                    // Si el campo de correo está vacío, asignarle un valor predeterminado
                    if ($correo === '') {
                        $correo = "sin correo";
                    }

                    // Generar usuario aleatorio
                    $iniciales = strtoupper(substr($nombre, 0, 1));
                    $palabras = explode(' ', $nombre);
                    if (count($palabras) > 1) {
                        $iniciales .= strtoupper(substr($palabras[1], 0, 1));
                    }
                    $numeroAzar = rand(100000, 999999);
                    $usuario = 'u' . $iniciales . $numeroAzar;

                    // Generar contraseña aleatoria
                    $longitudClave = rand(6, 8);
                    $clave = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"), 0, $longitudClave);

                    // Obtener IDs de puesto y departamento
                    $positions = $cargaData->getPositions();
                    $departments = $cargaData->getDepartments();
                    $puesto = strtoupper(trim($puesto));
                    $departamento = strtoupper(trim($departamento));
                    $id_puesto = $positions[$puesto] ?? null;
                    $id_departamento = $departments[$departamento] ?? null;

                    // Insertar en la base de datos
                    $fecha_alta = date('Y-m-d H:i:s');
                    $insertSuccess = $cargaData->insertIntoDatabase($nombre, $id_puesto, $id_departamento, $correo, $telefono, $usuario, $clave, $fecha_alta);

                    if (!$insertSuccess) {
                        echo "❌ Error al insertar algunos datos en la base de datos.<br>";
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

?>
