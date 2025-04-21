<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subir archivo Excel</title>
  <style>
    .card {
      width: 100%;
      max-width: 600px;
      margin: 20px auto;
      background: white;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      padding: 20px;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    .table tr {
      border-bottom: 1px solid #ddd;
    }

    .table td {
      padding: 10px;
      text-align: left;
    }

    .drop-zone {
      border: 2px dashed #007bff;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      color: #007bff;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .drop-zone.dragover {
      background-color: #e9f5ff;
    }

    #loader {
      display: none;
      margin: 20px auto;
      text-align: center;
    }

    .spinner {
      border: 6px solid #f3f3f3;
      border-top: 6px solid #007bff;
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 1s linear infinite;
      margin: 0 auto;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    #response {
      margin-top: 20px;
      font-size: 14px;
      color: #555;
    }
  </style>
</head>
<body>

<div class="card">
  <table class="table">
    <tr><td>1.- Crear un archivo .XLSX o convertir el archivo de Excel a .XLSX.</td></tr>
    <tr><td>2.- El archivo debe pesar menos de 2 MB.</td></tr>
    <tr><td>3.- Debe contener 2 encabezados con los siguientes nombres exactos: <br> <strong>nombre</strong>, <strong>departamento</strong>.</td></tr>
    <tr><td>4.- A continuación, podrá descargar un ejemplo del formato de carga masiva. Sustituya los campos con los datos que desea cargar, 
            asegúrese de no dejar campos en blanco y que no estén duplicados. <a href="assets/carga_puestos.xlsx" download>Descargar ejemplo</a></td></tr>
  </table>

  <form id="uploadForm" enctype="multipart/form-data">
    <div class="drop-zone" id="dropZone">
      Arrastra y suelta tu archivo aquí o haz clic para seleccionarlo.
      <input type="file" name="file" id="file" accept=".xls,.xlsx,.csv" style="display: none;" required>
    </div>
  </form>

  <div id="loader"><div class="spinner"></div></div>
  <div id="response"></div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
  const dropZone = document.getElementById('dropZone');
  const fileInput = document.getElementById('file');

  dropZone.addEventListener('click', () => fileInput.click());

  dropZone.addEventListener('dragover', (event) => {
    event.preventDefault();
    dropZone.classList.add('dragover');
  });

  dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('dragover');
  });

  dropZone.addEventListener('drop', (event) => {
    event.preventDefault();
    dropZone.classList.remove('dragover');

    const files = event.dataTransfer.files;
    if (files.length) {
      fileInput.files = files;
      handleFileUpload();
    }
  });

  // ✅ SOLUCIÓN AQUÍ: Disparar subida si se selecciona archivo manualmente
  fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) {
      handleFileUpload();
    }
  });

  $('#uploadForm').on('submit', function (event) {
    event.preventDefault();
    handleFileUpload();
  });

  function handleFileUpload() {
    const formData = new FormData($('#uploadForm')[0]);
    $('#loader').show();
    $('#response').html('');

    $.ajax({
      url: './?action=puestos/carga',
      type: 'POST',
      data: formData,
      contentType: false,
      processData: false,
      success: function (response) {
        $('#loader').hide();
        $('#response').html(response);
      },
      error: function (xhr, status, error) {
        $('#loader').hide();
        $('#response').html(
          `Error al subir el archivo. Código: ${xhr.status} <br> ${xhr.responseText}`
        );
      }
    });
  }
});
</script>

</body>
</html>
