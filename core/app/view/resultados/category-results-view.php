<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Encuestas por categoria</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
       
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            font-weight: bold;
            background-color: #f2f2f2;
        }
        .form-group {
            margin: 15px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .col-md-5 {
            width: 45%;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            text-align: center;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Resultados de encuestas por categoría</h1>

    <form id="resultados-form">
    <div class="row">
        <!-- Select para empleados -->
        <div class="col-md-4">
            <label for="personal_id">Seleccionar Empleado:</label>
            <div class="form-group">
                <select class="form-control" id="personal_id" name="personal_id" required>
                    <option value="">Selecciona un empleado</option>
                    <option value="todos">Todos los empleados</option>
                    <?php
                    $empleados = ReporteData::getCompletedEmployees();
                    if (!empty($empleados)) {
                        foreach ($empleados as $empleado) {
                            echo "<option value='{$empleado['personal_id']}'>{$empleado['personal_name']}</option>";
                        }
                    } else {
                        echo "<option value=''>No hay empleados disponibles</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Select para encuestas -->
        <div class="col-md-4">
            <label for="survey_id">Seleccionar Encuesta:</label>
            <div class="form-group">
                <select class="form-control" id="survey_id" name="survey_id" required>
                    <option value="">Selecciona una encuesta</option>
                    <?php 
                    $encuestas = EncuestaData::getAll();
                    if (!empty($encuestas)) {
                        foreach ($encuestas as $encuesta) {
                            if($encuesta->id == 1){
                                continue;
                            }
                            echo "<option value='{$encuesta->id}'>{$encuesta->title}</option>";
                        }
                    } else {
                        echo "<option value=''>No hay encuestas disponibles</option>";
                    }
                    ?>
                </select>
            </div>
        </div>

        <!-- Botón Exportar -->
        <div class="col-md-4">
            <input type="button" id="exportarExcel" value="Exportar a Excel">
        </div>
    </div>
</form>
                
            <!-- Agregar la librería Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Contenedor del gráfico -->


<canvas id="graficoDominios" width="400" height="200"></canvas>

<script>
        $(document).ready(function () {
            $("#personal_id, #survey_id").change(function () {
                cargarResultados();
            });
        });
        
        function cargarResultados() {
    var encuesta_id = $("#survey_id").val();
    var personal_id = $("#personal_id").val();


    // Selección de la URL según el ID de la encuesta y si es un cálculo general o por empleado
    var url = '';
    if (encuesta_id == 2) {
        url = (personal_id === 'todos') 
            ? './?action=resultados/get-general-category-results2' 
            : './?action=resultados/get-category-results-survey2';
    } else if (encuesta_id == 3) {
        url = (personal_id === 'todos') 
            ? './?action=resultados/get-general-category-results3' 
            : './?action=resultados/get-category-results-survey3';
    } else {
        url = "";
    }

    // Realizar la petición AJAX
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            survey_id: encuesta_id,
            personal_id: personal_id === 'todos' ? null : personal_id // Enviar null si es "todos"
        },
        success: function (response) {

            
                // Intentar parsear la respuesta JSON
                var data = JSON.parse(response);

                // Procesar los resultados por categoría
                var categorias = data.categorias;

                if (categorias && Object.keys(categorias).length > 0) {
                    var categoriasLabels = [];
                    var categoriasValores = [];
                    var categoriasNiveles = [];

                    // Extraer etiquetas, valores y niveles de cada categoría
                    for (var categoriaId in categorias) {
                        if (categorias.hasOwnProperty(categoriaId)) {
                            var categoria = categorias[categoriaId];
                            categoriasLabels.push(categoria.categoria_nombre);
                            categoriasValores.push(categoria.total_valor);
                            categoriasNiveles.push(categoria.nivel);
                        }
                    }

                    // Generar el gráfico con los datos procesados
                    generarGrafico(categoriasLabels, categoriasValores, categoriasNiveles);
                } else {
                    console.warn("No se encontraron categorías válidas.");
                    generarGrafico(["Sin resultados"], [0], ["Nulo"]);
                }
           
        },
        error: function (xhr, status, error) {
           // console.error("Error en la petición AJAX:");
            //console.error("Estado:", status);
           // console.error("Error:", error);
            //sconsole.error("Detalles de la respuesta:", xhr.responseText);
            alert("Ocurrió un error al cargar los resultados. Por favor, intenta de nuevo más tarde.");
        }
    });
}


function generarGrafico(labels, data, niveles) {
    var ctx = document.getElementById('graficoDominios').getContext('2d');

    // Destruir gráfico existente si ya fue creado
    if (window.dominioChart) {
        window.dominioChart.destroy();
    }

    // Mapear los niveles a colores correspondientes
    var categoriasColores = niveles.map(function(nivel) {
        switch (nivel) {
            case "Muy Alto":
                return "rgba(255, 0, 0, 0.8)"; // Rojo
            case "Alto":
                return "rgba(255, 159, 64, 0.8)"; // Naranja
            case "Medio":
                return "rgb(251, 255, 0)"; // Amarillo
            case "Bajo":
                return "rgba(75, 192, 192, 0.8)"; // Verde claro
            case "Nulo":
                return "rgba(0, 225, 255, 0.8)"; // Gris
            default:
                return "rgba(0, 0, 0, 0.8)"; // Negro (si no se encuentra el nivel)
        }
    });

    // Crear el nuevo gráfico
    window.dominioChart = new Chart(ctx, {
        type: 'bar', // Tipo de gráfico (barras en este caso)
        data: {
            labels: labels, // Etiquetas de las categorías
            datasets: [{
                label: 'Valor Total', // Título de la gráfica
                data: data, // Los valores para cada categoría
                backgroundColor: categoriasColores, // Colores calculados para cada barra
                borderColor: categoriasColores.map(color => color.replace('0.8', '1')), // Borde con opacidad completa
                borderWidth: 1
            }]
        },
        options: {
            responsive: true, // Hacer que el gráfico sea responsivo
            scales: {
                y: {
                    beginAtZero: true // Iniciar el eje Y desde cero
                }
            },
            plugins: {
                legend: {
                    display: false // Desactivar la leyenda
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return `Valor Total: ${tooltipItem.raw}`; // Mostrar valor total en el tooltip
                        }
                    }
                }
            }
        }
    });
}
$(document).ready(function () {
    $("#exportarExcel").click(function () {
        if (window.dominioChart) {
            var imageBase64 = window.dominioChart.toBase64Image(); // Convertir gráfico a imagen
            $.ajax({
                url: './?action=resultados/export-graphic', // Archivo PHP para procesar la imagen
                type: 'POST',
                data: {
                    image: imageBase64
                },
                success: function (response) {
                    console.log("Imagen enviada correctamente.");
                    window.location.href = './?action=resultados/export-graphic&download=1'; // Descargar Excel
                },
                error: function (xhr, status, error) {
                    console.error("Error al enviar la imagen:", error);
                }
            });
        } else {
            alert("No hay gráfico disponible para exportar.");
        }
    });
});

    </script>
</body>
</html>