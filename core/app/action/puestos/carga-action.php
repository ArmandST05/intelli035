<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['file'])) {
        
        $file = $_FILES['file'];
        $fileType = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileTmpPath = $file['tmp_name'];

        if (!in_array($fileType, ['xls', 'xlsx'])) {
            die("Error: El archivo debe ser de tipo .xls o .xlsx.");
        }

        if (!is_uploaded_file($fileTmpPath)) {
            die("Error: No se pudo cargar el archivo.");
        }

        require_once 'vendor/autoload.php';
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($fileTmpPath);
        $sheet = $spreadsheet->getActiveSheet();

        $cargaData = new CargaData();
        

        $departments = [];
        $positions = [];

        foreach ($sheet->getRowIterator() as $row) {
            $rowData = [];
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $valorCelda = $cell->getValue();
                $rowData[] = trim((string) $valorCelda);
            }
            

            if (count($rowData) === 2) {
                list($nombrePuesto, $nombreDepartamento) = $rowData;
            
                // Ignora si alguno de los campos está vacío
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

        $departmentIds = [];
        foreach ($departments as $nombreDepartamento) {
            $idDepartamento = $cargaData->insertDepartment($nombreDepartamento);
            if ($idDepartamento) {
                $departmentIds[$nombreDepartamento] = $idDepartamento;
            }
        }

        foreach ($positions as $position) {
            $nombrePuesto = $position['puesto'];
            $nombreDepartamento = $position['departamento'];

            if (isset($departmentIds[$nombreDepartamento])) {
                $idDepartamento = $departmentIds[$nombreDepartamento];
                $cargaData->insertPosition($nombrePuesto, $idDepartamento);
            } else {
                echo "Error: No se encontró el ID del departamento $nombreDepartamento <br>";
            }
        }

        echo "✅ Departamentos y puestos insertados correctamente.";
    } else {
        die("❌ Error: No se seleccionó archivo o empresa.");
    }
} else {
    die("❌ Error: Método no permitido.");
}
