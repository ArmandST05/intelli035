<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados generales por empleado</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <h1>Resultados de generales por empleado</h1>

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

    <!-- Contenedor para gráfico -->
    <div style="width: 60%; margin: 30px auto;">
        <canvas id="graficoDominios"></canvas>
    </div>

    <script>
        $(document).ready(function () {
            $('#personal_id, #survey_id').on('change', function () {
                const personal_id = $('#personal_id').val();
                const survey_id = $('#survey_id').val();

                if (personal_id && survey_id && personal_id !== "" && survey_id !== "") {
                    $.ajax({
                        url: './?action=resultados/get-general-results', // <--- CAMBIA ESTA LÍNEA
                        method: 'GET',
                        data: {
                            personal_id: personal_id,
                            survey_id: survey_id
                        },
                        success: function (response) {
                            try {
                                const json = JSON.parse(response);
                                const mensaje = json.mensaje;

                                let nivel = mensaje.replace("El resultado es ", "").replace(".", "");
                                let conteo = calcularValorPorNivel(nivel); // para graficar un valor numérico
                                generarGrafico(["Resultado general"], [conteo], [nivel]);
                            } catch (e) {
                                alert("Error al procesar la respuesta");
                            }
                        },
                        error: function () {
                            alert("Error al obtener los resultados");
                        }
                    });
                }
            });
        });

        // Asignamos un valor numérico a cada nivel
        function calcularValorPorNivel(nivel) {
            switch (nivel) {
                case "Muy Alto": return 100;
                case "Alto": return 80;
                case "Medio": return 60;
                case "Bajo": return 40;
                case "Nulo": return 20;
                default: return 0;
            }
        }

        function generarGrafico(labels, data, niveles) {
            var ctx = document.getElementById('graficoDominios').getContext('2d');

            if (window.dominioChart) {
                window.dominioChart.destroy();
            }

            var categoriasColores = niveles.map(function(nivel) {
                switch (nivel) {
                    case "Muy Alto":
                        return "rgba(255, 0, 0, 0.8)";
                    case "Alto":
                        return "rgba(255, 159, 64, 0.8)";
                    case "Medio":
                        return "rgb(251, 255, 0)";
                    case "Bajo":
                        return "rgba(75, 192, 192, 0.8)";
                    case "Nulo":
                        return "rgba(0, 225, 255, 0.8)";
                    default:
                        return "rgba(0, 0, 0, 0.8)";
                }
            });

            window.dominioChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Valor Total',
                        data: data,
                        backgroundColor: categoriasColores,
                        borderColor: categoriasColores.map(color => color.replace('0.8', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(tooltipItem) {
                                    return `Valor Total: ${tooltipItem.raw}`;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
