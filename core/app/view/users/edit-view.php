<?php
$user = UserData::getById($_GET["id"]);
$userTypes = UserTypeData::getAll();
?>

<div class="row">
  <div class="col-md-12">
    <h1>Editar Usuario</h1>
    <br>
    <form class="form-horizontal" method="post" id="edituser" action="index.php?action=users/update" role="form">
      
      <!-- Nombre -->
      <div class="form-group">
        <label for="name" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" value="<?php echo $user->name; ?>" class="form-control" id="name" placeholder="Nombre" required>
        </div>
      </div>

      <!-- Apellido -->
      <div class="form-group">
        <label for="lastname" class="col-lg-2 control-label">Apellido*</label>
        <div class="col-md-6">
          <input type="text" name="lastname" value="<?php echo $user->lastname; ?>" class="form-control" id="lastname" placeholder="Apellido" required>
        </div>
      </div>

      <!-- Correo Electrónico -->
      <div class="form-group">
        <label for="email" class="col-lg-2 control-label">Correo Electrónico*</label>
        <div class="col-md-6">
          <input type="email" name="email" value="<?php echo $user->email; ?>" class="form-control" id="email" placeholder="Correo Electrónico" required>
        </div>
      </div>

      <!-- Nombre de Usuario -->
      <div class="form-group">
        <label for="username" class="col-lg-2 control-label">Nombre de Usuario*</label>
        <div class="col-md-6">
          <input type="text" name="username" value="<?php echo $user->username; ?>" class="form-control" id="username" placeholder="Nombre de Usuario" required>
        </div>
      </div>

      <!-- Contraseña -->
      <div class="form-group">
        <label for="password" class="col-lg-2 control-label">Contraseña</label>
        <div class="col-md-6">
          <input type="password" name="password" class="form-control" id="password" placeholder="Contraseña">
          <p class="help-block">La contraseña sólo se modificará si escribes algo, en caso contrario no se modifica.</p>
        </div>
      </div>

      <!-- Tipo de Usuario -->
      <div class="form-group">
        <label for="user_type" class="col-lg-2 control-label">Tipo de Usuario</label>
        <div class="col-md-6">
          <select class="form-control" name="user_type" id="user_type" required>
            <option value="">-- SELECCIONE --</option>
            <?php foreach ($userTypes as $type) : ?>
              <option value="<?php echo $type->id; ?>" <?php echo ($user->user_type == $type->id) ? "selected" : "" ?>>
                <?php echo $type->description; ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- Departamento -->
      <div class="form-group">
        <label for="departamento" class="col-lg-2 control-label">Departamento*</label>
        <div class="col-md-6">
          <input type="text" name="departamento" value="<?php echo $user->departamento; ?>" class="form-control" id="departamento" placeholder="Departamento" required>
        </div>
      </div>

      <!-- Fecha de Nacimiento -->
      <div class="form-group">
        <label for="date_of_birth" class="col-lg-2 control-label">Fecha de Nacimiento*</label>
        <div class="col-md-6">
          <input type="date" name="date_of_birth" value="<?php echo $user->date_of_birth; ?>" class="form-control" id="date_of_birth" required>
        </div>
      </div>

      <!-- Activo/Inactivo -->
      <div class="form-group">
        <label for="is_active" class="col-lg-2 control-label">Activo</label>
        <div class="col-md-6">
          <input type="checkbox" name="is_active" id="is_active" <?php echo ($user->is_active) ? "checked" : ""; ?>>
        </div>
      </div>

      <!-- Botón de Guardar -->
      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <input type="hidden" name="user_id" value="<?php echo $user->id; ?>">
          <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
        </div>
      </div>
    </form>
  </div>
</div>
