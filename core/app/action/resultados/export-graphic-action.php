<?php
require 'vendor/autoload.php'; // Cargar PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['image'])) {
    $imageData = $_POST['image'];

    // Decodificar la imagen Base64
    $imageData = str_replace('data:image/png;base64,', '', $imageData);
    $imageData = base64_decode($imageData);

    // Guardar la imagen temporalmente
    $imagePath = 'grafico.png';
    file_put_contents($imagePath, $imageData);

    // Redirigir para descargar el archivo
    echo json_encode(["success" => true]);
    exit;
}

// Si se solicita la descarga del archivo Excel
if (isset($_GET['download'])) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Resultados');

    // Insertar una imagen en la hoja de cálculo
    $drawing = new Drawing();
    $drawing->setName('Grafico');
    $drawing->setDescription('Resultados de encuestas');
    $drawing->setPath('grafico.png'); // Imagen guardada
    $drawing->setHeight(300); // Tamaño de la imagen en el Excel
    $drawing->setCoordinates('B2'); // Posición en la hoja
    $drawing->setWorksheet($sheet);

    // Generar el archivo Excel
    $filename = 'Resultados_Grafico.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}
