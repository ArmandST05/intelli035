<?php
$searchUserType = (isset($_GET["userType"])) ? $_GET["userType"] : 0;
$searchStatus= (isset($_GET["status"])) ? $_GET["status"] : 1;

$users = UserData::getByUserTypeStatus($searchUserType,$searchStatus);
$userTypes = UserTypeData::getAll();
?>
<div class="row">
	<div class="col-md-12">
		<a href="index.php?view=users/new" class="btn btn-default pull-right"><i class="fas fa-plus"></i> Nuevo Usuario</a>
		<h1>Lista de Usuarios</h1>
		<br>
		<form>
			<input type="hidden" name="view" value="users/index">
			<div class="row">
				<div class="col-md-4">
					<label class="col-md-4 control-label">Tipo usuario</label>
					<select name="userType" id="searchUserType" class="form-control" required>
						<option value="0" <?php echo ($searchUserType == 0) ? "selected" : "" ?>>-- TODOS --</option>
						<?php foreach ($userTypes as $type) : ?>
							<option value="<?php echo $type->id; ?>" <?php echo ($type->id == $searchUserType) ? "selected" : "" ?>><?php echo $type->description; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-4">
					<label class="col-md-4 control-label">Estatus</label>
					<select name="status" id="searchStatus" class="form-control" required>
						<option value="all" <?php echo ($searchStatus == 'all') ? "selected" : "" ?>>-- TODOS --</option>
						<option value="1" <?php echo ($searchStatus == 1) ? "selected" : "" ?>>ACTIVO</option>
						<option value="0" <?php echo ($searchStatus == 0) ? "selected" : "" ?>>INACTIVO</option>
					</select>
				</div>
				<div class="col-md-2">
					<br>
					<input type="submit" class="btn btn-success btn-block" value="Procesar">
				</div>
			</div>
		</form>
		<br>
		<?php
		if (count($users) > 0) {
		?>
			<table class="table table-bordered table-hover">
				<thead>
					<th>Nombre completo</th>
					<th>Nombre de usuario</th>
					<th>Tipo usuario</th>
					<th>Activo</th>
					<th>Admin</th>
					<th></th>
				</thead>
				<?php
				foreach ($users as $user) {
				?>
					<tr>
						<td><?php echo $user->name . " " . $user->lastname; ?></td>
						<td><?php echo $user->username; ?></td>
						<td><?php echo $user->getUserType()->description; ?></td>
						<td>
							<?php if ($user->is_active) : ?>
								<i class="glyphicon glyphicon-ok"></i>
							<?php endif; ?>
						</td>
						<td>
							<?php if ($user->user_type == "su") : ?>
								<i class="glyphicon glyphicon-ok"></i>
							<?php endif; ?>
						</td>
						<td style="width:30px;"><a href="index.php?view=users/edit&id=<?php echo $user->id; ?>" class="btn btn-warning btn-xs"><i class="fas fa-pencil-alt"></i> Editar</a></td>
					</tr>
			<?php

				}
				echo "</table>";
			} else {
				// no hay usuarios
			}


			?>


	</div>
</div>
<script type="text/javascript">
	$(document).ready(function() {
		$("#searchUserType").select2({});
	});
</script>