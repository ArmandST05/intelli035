<div class="row">
	<div class="col-md-12">
		<div class="btn-group  pull-right"><a href="index.php?view=workshifts/new" class="btn btn-default"><i class="fas fa-plus"></i> Agregar turno</a>
		</div>
		<h1>Lista de turnos </h1>
		<div class="clearfix"></div>
		<?php

		$workshifts = WorkshiftData::getAll();
		if (count($workshifts) > 0):?>
			<table class="table table-bordered table-hover">
				<thead>
					<th>Nombre</th>
					<th>Color</th>
					<th>Estatus</th>
					<th style="width:80px;"></th>
				</thead>
				<?php
				foreach ($workshifts as $workshift) : ?>
					<tr>
						<td><?php echo $workshift->name ?></td>
						<td style="background-color:<?php echo $workshift->color ?>"></td>
						<td>ACTIVO</td>
						<td style="width:80px;" class="td-actions">
							<a href="index.php?view=workshifts/edit&id=<?php echo $workshift->id; ?>" rel="tooltip" title="Editar" class="btn btn-simple btn-warning btn-xs"><i class='fas fa-pencil-alt'></i></a>
							<!--<a href="index.php?action=workshifts/delete&id=<?php echo $workshift->id; ?>" rel="tooltip" title="Eliminar" onClick='return confirmDelete()' class=" btn-simple btn btn-danger btn-xs"><i class='far fa-trash-alt'></i></a>-->
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php else: ?>
			<p class='alert alert-danger'>No hay turnos registrados</p>
		<?php endif; ?>
		</table>
	</div>
</div>
</div>
</div>
<script type="text/javascript">
	function confirmDelete() {
		var flag = confirm("¿Seguro que deseas eliminar el turno?");
		if (flag == true) {
			return true;
		} else {
			return false;
		}
	}
</script>