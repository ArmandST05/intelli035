<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departamentos</title>
    
    <style>
        ul {
            list-style-type: none;
        }
        ul li {
            margin-top: 5px;
        }
        #nuevoPersonal {
            background-color: #0016cc;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <!-- Columna del texto -->
            <div class="col-md-8">
                <h3>Listado Departamentos</h3>
                <p>
                    Indicaciones: En este módulo podrá agregar, eliminar y editar el personal. 
                    Utilice la opción Carga Masiva para cargar al personal mediante un archivo de Microsoft Excel.
                    Recuerde que al agregar a un personal se le activarán las encuestas seleccionadas del puesto.
                    De igual manera tiene la opción de seleccionar las encuestas a contestar de manera independiente 
                    por personal en la opción Encuestas seleccionadas.
                </p>
            </div>
            <!-- Columna de los botones -->
            <div class="col-md-4">
                <div class="d-flex flex-column gap-2">
                    
                            <button type="button" class="btn btn-primary" onclick="openModalAdd()">Agregar Departamento</button>
                        
                       
                                     
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal Nuevo Empleado -->
    <div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLongTitle">Agregar Nuevo Departamento</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Aquí se colocarán los campos del formulario -->
                    <form>
                        <div class="form-group">
                            <label for="employeeName">Nombre del Departamento</label>
                            <input type="text" class="form-control" id="employeeName" placeholder="Ingrese el nombre">
                        </div>
                        <div class="form-group">
                            <label for="employeeRole">Puesto</label>
                            <input type="text" class="form-control" id="employeeRole" placeholder="Ingrese el puesto">
                        </div>
                        <div class="form-group">
                            <label for="employeeRole">Empresa a la que pertenece este departamento</label>
                            <select class="form-control" name="empresa_id" id="empresa_id"></select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModalAdd() {
            // Configura el contenido dinámico del modal si es necesario.
            $('#exampleModalCenter').modal('show'); // Muestra el modal.
        }
    </script>
</body>
</html>
