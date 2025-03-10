<?php
$user_types = UserTypeData::getAll();
?>
<div class="row">
  <div class="col-md-12">
    <h1>Agregar Usuario</h1>
    <br>
    <form class="form-horizontal" method="post" action="index.php?action=users/add" role="form">


      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre*</label>
        <div class="col-md-6">
          <input type="text" name="name" class="form-control" id="name" placeholder="Nombre">
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Nombre de Usuario*</label>
        <div class="col-md-6">
          <input type="text" name="username" class="form-control" required id="username" placeholder="Nombre de Usuario">
        </div>
      </div>
      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Contraseña</label>
        <div class="col-md-6">
          <input type="password" name="password" class="form-control" id="inputEmail1" placeholder="Contraseña">
        </div>
      </div>


      <div class="form-group">
        <label for="inputEmail1" class="col-lg-2 control-label">Tipo de Usuario</label>
        <div class="col-md-6">
          <div>
            <label>
              <select class="form-control" name="user_type">
                <option value="">-- SELECCIONE --</option>
                <?php foreach ($user_types as $type) : ?>
                  <option value="<?php echo $type->id; ?>"><?php echo $type->description; ?></option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <div class="col-lg-offset-2 col-lg-10">
          <button type="submit" class="btn btn-primary">Agregar Usuario</button>
        </div>
      </div>
    </form>
  </div>
</div>