<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes de Encuestas NOM-035</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>


     
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background-color: #fff;
        }

        

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }


        th {
            font-weight: bold;
        }
        td:first-child {
            text-align: left; /* Alinear las preguntas a la izquierda para mejor legibilidad */
        }

        /* Ajusta el ancho de las columnas */
        th:nth-child(1), td:nth-child(1) {
            width: 70%; /* 70% del ancho para la columna de preguntas */
        }

        th:nth-child(2), td:nth-child(2),
        th:nth-child(3), td:nth-child(3) {
            width: 15%; /* 15% del ancho para las columnas de Sí y No */
        }

        input[type="radio"] {
            transform: scale(1.2);
        }
    </style>
</head>
<body>
    <h1>Reportes por Empleado</h1>
        
    <form method="POST" action="index.php?action=reportes/get-questions">
    <div class="row">
    <!-- Select para empleados -->
    <div class="col-md-4">
        <label for="personal_id">Seleccionar Empleado:</label>
        <div class="form-group">
            <select class="form-control" id="personal_id" name="personal_id" required onchange="cargarPreguntas()">
                <option value="">Selecciona un empleado</option>
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
            <select class="form-control" id="survey_id" name="survey_id" required onchange="cargarPreguntas()">
                <option value="">Selecciona una encuesta</option>
                <?php 
                $encuestas = EncuestaData::getAll();
                if (!empty($encuestas)) {
                    foreach ($encuestas as $encuesta) {
                        echo "<option value='{$encuesta->id}'>{$encuesta->title}</option>";
                    }
                } else {
                    echo "<option value=''>No hay encuestas disponibles</option>";
                }
                ?>
            </select>
        </div>
    </div>
    <div class="col-md-4">
    <button type="button" class="btn btn-primary" onclick="printReport()">Imprimir lista</button>
    </div>
</div>

        

        <table id="tabla_preguntas" class="table table-bordered">
            
            <tbody>
                <!-- Las preguntas se cargarán aquí con AJAX -->
            </tbody>
        </table>
    </form>
    <script>
    // Función para cargar las preguntas y respuestas de la encuesta seleccionada
    function cargarPreguntas() {
        var encuesta_id = $("#survey_id").val();
        var personal_id = $("#personal_id").val();

        if (encuesta_id && personal_id) {
            // Realizamos la llamada AJAX para obtener las preguntas y respuestas
            $.ajax({
                url: encuesta_id == 2 || encuesta_id == 3 ? './?action=reportes/get-questions-multiple' : './?action=reportes/get-questions',
                type: 'GET',
                data: {
                    encuesta_id: encuesta_id,
                    personal_id: personal_id
                },
                success: function(response) {
                    $("#tabla_preguntas tbody").html(response); // Insertar las filas correctamente
                },

                error: function () {
                    alert('Ocurrió un error al cargar las preguntas y respuestas.');
                }
            });
        }
    }

    function printReport() {
    var encuesta_id = $("#survey_id").val();
    var personal_id = $("#personal_id").val();
    var personal_name = $("#personal_id option:selected").text(); // Obtiene el nombre del empleado seleccionado

    if (encuesta_id && personal_id) {
        // Clonar la tabla para modificarla sin afectar la original
        var clonedTable = $("#tabla_preguntas").clone();

        // Convertir los radio buttons marcados en texto
        clonedTable.find("input[type='radio']:checked").each(function () {
            var value = $(this).val();
            var cell = $(this).closest("td");
            cell.html(value === "1" ? "Sí" : "No"); // Sustituir checkbox por "Sí" o "No"
        });

        // Obtener el HTML modificado
        var tableHtml = clonedTable.html();

        if (!tableHtml) {
            alert("No hay contenido en la tabla.");
            return;
        }

        // Enviamos el HTML modificado y el nombre del empleado al backend
        $.ajax({
            url: './?action=reportes/export-pdf',
            type: "POST",
            data: {
                table_html: tableHtml, 
                encuesta_id: encuesta_id,  
                personal_id: personal_id,  
                personal_name: personal_name // Enviar también el nombre del empleado
            },
            success: function(response) {
                console.log("Respuesta del servidor: ", response);
                // Redirigir para descargar el PDF generado con los parámetros en la URL
                window.location.href = "./?action=reportes/export-pdf&download=1&encuesta_id=" + encuesta_id + "&personal_id=" + personal_id + "&personal_name=" + encodeURIComponent(personal_name);
            },
            error: function(xhr, status, error) {
                console.error("Error al generar el PDF:", error);
            }
        });
    } else {
        alert('Por favor, selecciona un empleado y una encuesta.');
    }
}


</script>

</body>
</html>
