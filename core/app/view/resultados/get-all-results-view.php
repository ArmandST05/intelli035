<h2>Resumen general de empleados</h2>

<div class="container">
    <!-- Fila del select -->
    <div class="row mb-3">
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
    </div>

    <!-- Fila de la tabla -->
    <div class="row">
        <div class="col-12">
            <div class="table-responsive mt-3">
                <table id="tabla-empleados" class="table table-bordered table-striped">
                    <thead class="thead-dark">
                        <tr>
                            <th>Nombre</th>
                            <th>Total</th>
                            <th>Nivel</th>
                            <th>Color</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $('#survey_id').on('change', function () {
        const survey_id = $(this).val();
        console.log("Encuesta seleccionada:", survey_id); // 🐞 Verifica que haya valor

        if (!survey_id) {
            alert("Selecciona una encuesta válida.");
            return;
        }

        $.ajax({
            url: './?action=resultados/get-all-results', // Cambia si lo estás llamando directo
            method: 'GET',
            data: { survey_id: survey_id },
            beforeSend: function () {
                console.log("Enviando solicitud AJAX con survey_id:", survey_id);
            },
            success: function (response) {
                console.log("Respuesta recibida del servidor:", response); // 🐞 Revisa lo que responde

                try {
                    const json = JSON.parse(response);
                    console.log("JSON parseado correctamente:", json);

                    if (!json.empleados || !Array.isArray(json.empleados)) {
                        console.error("La estructura de 'empleados' no es válida:", json);
                        alert("La respuesta del servidor no tiene el formato esperado.");
                        return;
                    }

                    const empleados = json.empleados;
                    const tbody = $('#tabla-empleados tbody');
                    tbody.empty();

                    if (empleados.length === 0) {
                        tbody.append(`<tr><td colspan="4">No se encontraron resultados.</td></tr>`);
                        return;
                    }

                    empleados.forEach(function (emp, index) {
                        console.log(`Empleado ${index + 1}:`, emp); // 🐞 Verifica cada empleado

                        const fila = `
                            <tr>
                                <td>${emp.nombre}</td>
                                <td>${emp.total}</td>
                                <td>${emp.nivel}</td>
                                <td><div style="width: 30px; height: 20px; background-color: ${emp.color};"></div></td>
                            </tr>`;
                        tbody.append(fila);
                    });

                } catch (e) {
                    console.error("Error al parsear JSON:", e);
                    console.warn("Respuesta cruda:", response);
                    alert("Hubo un error procesando la información del servidor.");
                }
            },
            error: function (xhr, status, error) {
                console.error("Error AJAX:", status, error);
                console.log("Detalles del error:", xhr.responseText);
                alert("Error al consultar los resultados. Revisa la consola para más detalles.");
            }
        });
    });
</script>
