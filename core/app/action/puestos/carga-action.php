<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        
        $file = $_FILES['file'];
        $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileTmpPath = $file['tmp_name'];
        $fileName = $file['name'];

        if (!in_array($fileType, ['xls', 'xlsx'])) {
            die("❌ Error: El archivo debe ser de tipo .xls o .xlsx.");
        }

        if (!is_uploaded_file($fileTmpPath)) {
            die("❌ Error: No se pudo cargar el archivo.");
        }

        require_once 'vendor/autoload.php';
        $cargaData = new CargaData();

        // ✅ Guardar el archivo en la base de datos
        $fileData = file_get_contents($fileTmpPath);
        if ($fileData === false) {
            die("❌ Error: No se pudo leer el archivo.");
        }

        $cargaData->insertFileToDatabase($fileName, $fileType, $fileData);
        echo "✅ Archivo guardado en la base de datos.<br>";

        // ✅ Procesar el archivo
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmpPath);
        $sheet = $spreadsheet->getActiveSheet();

        $departments = [];
        $positions = [];

        foreach ($sheet->getRowIterator() as $row) {
            if ($row->getRowIndex() == 1) {
                continue; // Saltar encabezado
            }
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $valorCelda = $cell->getValue();
                $rowData[] = trim((string) $valorCelda);
            }

            if (count($rowData) === 2) {
                list($nombrePuesto, $nombreDepartamento) = $rowData;

                if (empty($nombrePuesto) || empty($nombreDepartamento)) {
                    continue;
                }

                $nombreDepartamento = strtoupper(trim($nombreDepartamento));
                $nombrePuesto = strtoupper(trim($nombrePuesto));

                if (!in_array($nombreDepartamento, $departments)) {
                    $departments[] = $nombreDepartamento;
                }

                $positions[] = [
                    'puesto' => $nombrePuesto,
                    'departamento' => $nombreDepartamento
                ];
            }
        }

        // ✅ Insertar departamentos
        $departmentIds = [];
        foreach ($departments as $nombreDepartamento) {
            $idDepartamento = $cargaData->insertDepartment($nombreDepartamento);
            if ($idDepartamento) {
                $departmentIds[$nombreDepartamento] = $idDepartamento;
            }
        }

        // ✅ Insertar puestos
        foreach ($positions as $position) {
            $nombrePuesto = $position['puesto'];
            $nombreDepartamento = $position['departamento'];

            if (isset($departmentIds[$nombreDepartamento])) {
                $idDepartamento = $departmentIds[$nombreDepartamento];
                $cargaData->insertPosition($nombrePuesto, $idDepartamento);
            } else {
                echo "❌ Error: No se encontró el ID del departamento $nombreDepartamento <br>";
            }
        }

        echo "✅ Departamentos y puestos insertados correctamente.";
    } else {
        die("❌ Error: No se seleccionó archivo.");
    }
} else {
    die("❌ Error: Método no permitido.");
}
?>
