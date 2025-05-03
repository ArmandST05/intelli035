<!DOCTYPE html>
<?php
// Obtén el usuario actual basado en la sesión
if (isset($_SESSION['typeUser']) && $_SESSION['typeUser'] === 'e') {
  // Si es un empleado
  if (isset($_SESSION['user_name'])) {
      $user_name = $_SESSION['user_name'];
  } else {
      $user_name = "Invitado";
  }
} else {
  // Si es un usuario del sistema
  $user = UserData::getLoggedIn();
  if ($user) {
      $user_name = $user->name;
  } else {
      $user_name = "Invitado";
  }
}
?>

<html>

<head>
  <meta charset="UTF-8">
  <title>INTELLI035</title>
  <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
  <link rel="icon" href="assets/icon-intelli.png">

  <!-- Jquery -->
  <script src="assets/jquery-2.1.1.min.js" type="text/javascript"></script>

  <!-- Bootstrap 3.3.4 -->
  <link href="plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
  <!-- Font Awesome Icons -->
  <link href="plugins/font-awesome/css/all.min.css" rel="stylesheet" type="text/css" />
  <!-- Theme style -->
  <link href="plugins/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
  <link href="plugins/dist/css/skins/skin-blue-light.min.css" rel="stylesheet" type="text/css" />
  <!--link rel="stylesheet" href="plugins/datatables/dataTables.bootstrap.css"-->
  <link href="plugins/colorpicker/dist/css/bootstrap-colorpicker.css" rel="stylesheet" type="text/css">

  <!-- SweetAlert -->
  <link href="plugins/sweetalert/min.css" rel="stylesheet" type="text/css" />
  <script src="plugins/sweetalert/min.js"></script>

  <script type='text/javascript' src='plugins/nicedit/nicEdit.js'></script>
  <!--<?php if ($userType == "su" || $userType == "do") : ?>
    <script type='text/javascript' src='plugins/nicedit/nicEdit.js'></script>
    <script type='text/javascript'>
      bkLib.onDomLoaded(function() {
        nicEditors.allTextAreas()
      });
    </script>
  <?php endif; ?>-->

  <script src="plugins/jquery/jquery-2.1.4.min.js"></script>
  <script src="plugins/morris/raphael-min.js"></script>
  <script src="plugins/morris/morris.js"></script>
  <link rel="stylesheet" href="plugins/morris/morris.css">
  <link rel="stylesheet" href="plugins/morris/example.css">
  <script src="plugins/jspdf/jspdf.min.js"></script>
  <script src="plugins/jspdf/jspdf.plugin.autotable.js"></script>

  <!-- StackTable.js -->
  <link rel="stylesheet" href="/plugins/stacktablejs/stacktable.css" />
  <script src="plugins/stacktablejs/stacktable.js"></script>
  <!-- StackTable.js -->

  <?php if (isset($_GET["view"]) && $_GET["view"] == "sales") : ?>
    <script type="text/javascript" src="plugins/jsqrcode/llqrcode.js"></script>
    <script type="text/javascript" src="plugins/jsqrcode/webqr.js"></script>
  <?php endif; ?>

  <!-- Sweet Alert -->
  <script src="plugins/sweetalert/min.js"></script>
  <!--  Sweet Alert-->
  <!-- ColorPicker -->
  <script src="plugins/colorpicker/dist/js/bootstrap-colorpicker.js"></script>
  <!-- ColorPicker -->
  <!-- Select2 -->
  <link href="plugins/select2/select2.min.css" rel="stylesheet" />
  <script src="plugins/select2/select2.min.js"></script>
  <!-- Select2 -->
  <!-- CALENDAR-->
  <script src='assets/js/moment.min.js'></script>
  <script src='assets/js/jquery-ui.min.js'></script>

  <script src='node_modules/fullcalendar/index.global.min.js'></script>
  <script src='node_modules/@fullcalendar/core/locales-all.global.js'></script>
  <!-- CALENDAR -->

  <!--AUTOCOMPLETE JQUERY UI-->
  <!--<script src="https://code.jquery.com/jquery-3.6.0.js"></script>-->
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
</head>

<body onload=''  class="<?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])) : ?>  skin-blue-light sidebar-mini <?php else : ?>login-page<?php endif; ?>">
  <div class="background">
    <div class="wrapper">
      <!-- Main Header -->
      <?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])) : ?>
        <header class="main-header" style="background-color: #0073e6;">
          <!-- Logo -->
          <a href="./" class="logo" style="background-color: white;">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"> <img src="assets/IntelliPsicosocial.png" width="40"></span>
            <!-- logo for regular state and mobile devices -->
            <img src="assets/IntelliPsicosocial.png" width="100" height="60">
          </a>

          <!-- Header Navbar -->
          <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button"><i class="fas fa-bars"></i>
              <span class="sr-only">Toggle navigation</span>
            </a>
            <!-- Navbar Right Menu -->
            <div class="navbar-custom-menu" style="color: white;">
              <ul class="nav navbar-nav">

                <!-- User Account Menu -->
                <li class="dropdown user user-menu" >
                  <!-- Menu Toggle Button -->
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <!-- The user image in the navbar-->
                    <!-- hidden-xs hides the username on small devices so only the image appears. -->
                    <span class="" style="color: white;">
    <i class="fas fa-user"></i> <?php echo $user_name; ?>
</span>
                  </a>
                </li>
                <li class="dropdown user user-menu">
                  <!-- Menu Toggle Button -->
                  <a href="./logout.php" class="dropdown-toggle" style="color: white;">Salir <i class="fas fa-sign-out-alt"></i></a>
                </li>
                <!-- Control Sidebar Toggle Button -->
              </ul>
            </div>
          </nav>
        </header>
        <!-- Left side column. contains the logo and sidebar -->
        <aside class="main-sidebar">

          <!-- sidebar: style can be found in sidebar.less -->
          <section class="sidebar">

            <!-- Sidebar Menu  ADMINISTRADOR-->
            <ul class="sidebar-menu">

              <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "su")) : ?>
                <meta charset="UTF-8">
                <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>

                <li class="treeview">
                  <a href="#"><i class="fa-solid fa-user-tie"></i> <span>Puestos</span><i class="fa fa-angle-left pull-right"></i> </a>
                <ul class="treeview-menu">  
                <li><a href="./?view=puestos/index">Listado Puestos</a></li>
                  <li><a href="./?view=puestos/carga">Carga Masiva</a></li>
                  
                </ul>
                </li>
                <li class="treeview">
                <a href="#"><i class="fa-solid fa-user-nurse"></i>  <span>Personal</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">  
                <li><a href="./?view=personal/listado">Listado Personal</a></li>
                  <li><a href="./?view=personal/carga">Carga Masiva</a></li>
                  <li><a href="./?view=personal/credentials">Enviar credenciales</a></li>
                  <li><a href="./?view=personal/historial">Historial</a></li>
                </ul>
                </li>

                <li class="treeview">
                <a href="#"><i class="fa-solid fa-building"></i>  <span>Mis empresas</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">  
                  <li><a href="./?view=empresas/index">Listado Empresas</a></li>
                    <li><a href="./?view=empresas/logos">Logos Empresas</a></li>
                  </ul>
                  </li>
                </li>
                <li class="treeview">
                  
                <a href="#"><i class="fa-solid fa-calendar"></i>  <span>Periodos</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">  
                  <li><a href="./?view=periodos/index">Ver periodos</a></li>
                  
                </ul>
                </li>
                <li class="treeview">
                <a href="#"><i class="fa-solid fa-timeline"></i>  <span>Reportes</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">  
                <li><a href="./?view=reportes/index">Reportes por empleado</a></li>
                <li><a href="./?view=resultados/get-all-results">Resumen general</a></li>
                <li><a href="./?view=resultados/general-results">Reportes por generales</a></li>
                  <li><a href="./?view=resultados/category-results">Reportes por categoria</a></li>
                  <li><a href="./?view=resultados/domain-results">Reportes por dominio</a></li>

                </ul>
                </li>
                <!-- <li class="treeview">
                  <a href="#"><i class='fa fa-th-list'></i> <span>Catálogos</span> <i class="fa fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    <li><a href="./?view=areas/index">Áreas</span></a></li>
                    <li><a href="./?view=beds/index">Camas</span></a></li>
                    <li><a href="./?view=diagnostics/index">Diagnósticos</a></li>
                    <li><a href="./?view=medic-categories/index">Especialidades personal</span></a></li>
                    <li><a href="./?view=laboratories/index">Laboratorios/Consultorios</a></li>
                    <li><a href="./?view=medics/index">Personal médico</a></li>
                    <li><a href="./?view=medicines/index">Medicamentos</a></li>
                    <li><a href="./?view=bed-symbols/index">Simbología de camas</span></a></li>
                    <li><a href="./?view=workshifts/index">Turnos</span></a></li>
                  </ul>
                </li> -->


                <li class="treeview">
                  <a href="#"><i class='fas fa-cog'></i> <span>Configuración</span> <i class="fas fa-angle-left pull-right"></i></a>
                  <ul class="treeview-menu">
                    
                    <li><a href="./?view=users/index">Usuarios</a></li>
                  </ul>
                </li>
            </ul>
          <?php endif; ?>

          <!-- MÉDICO GENERAL ASISTENTE (SUB-ADMINISTRADOR)-->
          <ul class="sidebar-menu">
            <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "mg")) : ?>
              <meta charset="UTF-8">
              <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>
              <li><a href="./?view=patients/index"><i class='fas fa-user-alt'></i> <span>Pacientes</span></a></li>
          </ul>
        <?php endif; ?>

        <!-- Doctor-->
        <ul class="sidebar-menu">

          <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "do")) : ?>
            <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>
            <!--li><a href="./?view=sell"><i class='fa fa-usd'></i> <span>Vender</span></a></li-->
            <li><a href="./?view=patients/index"><i class='fas fa-user-alt'></i> <span>Pacientes</span></a></li>
            <li class="treeview">
              <a href="#"><i class="fa-solid fa-user-nurse"></i> <span>Enfermería</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li><a href="./?view=infirmary-daily-notes/index">Notas enfermería</a></li>
                <li><a href="./?view=infirmary-kardex/index">Kardex enfermería</a></li>
                <li><a href="./?view=infirmary-fluid-balance/index">Balance de líquidos</a></li>
                <li><a href="./?view=infirmary-vital-signs/index">Signos vitales</a></li>
                    <li><a href="./?view=infirmary-surgical-formats/index">Formatos quirúrgicos</a></li>
                <li><a href="./?view=informed-consents/index">Consentimientos informados</a></li>
                <li><a href="./?view=beds/hospitalization-index">Hosptalización Camas</a></li>
              </ul>
            </li>
            <li><a href="./?view=configuration/edit-medic-profile"><i class='fas fa-cog'></i> <span>Perfil</span></a></li>
          <?php endif; ?>

          <!-- Recepcionista-->
          <ul class="sidebar-menu">

            <?php if ((isset($_SESSION["user_id"])) && ($_SESSION['typeUser'] == "r")) : ?>
              <li><a href="./index.php?view=home"><i class='fa fa-home'></i> <span>Inicio</span></a></li>
              <!--li><a href="./?view=sell"><i class='fa fa-usd'></i> <span>Vender</span></a></li-->
              <li><a href="./?view=patients/index"><i class='fas fa-user-alt'></i> <span>Pacientes</span></a></li>
              <li class="treeview">
                <a href="#"><i class="fa-solid fa-user-nurse"></i> <span>Enfermería</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="./?view=infirmary-daily-notes/index">Notas enfermería</a></li>
                  <li><a href="./?view=infirmary-kardex/index">Kardex enfermería</a></li>
                  <li><a href="./?view=infirmary-fluid-balance/index">Balance de líquidos</a></li>
                  <li><a href="./?view=infirmary-vital-signs/index">Signos vitales</a></li>
                    <li><a href="./?view=infirmary-surgical-formats/index">Formatos quirúrgicos</a></li>
                  <li><a href="./?view=informed-consents/index">Consentimientos informados</a></li>
                  <li><a href="./?view=beds/hospitalization-index">Hosptalización Camas</a></li>
                </ul>
              </li>
              <!--<li class="treeview">
                <a href="#"><i class='fas fa-money-bill-alt'></i> <span>Gastos</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="./?view=expenses/new">Agregar gasto</a></li>
                  <li><a href="./?view=expenses/index&limit">Ver gastos</a></li>
                </ul>
              </li>-->

              <li class="treeview">
                <a href="#"><i class='fa fa-database'></i> <span>Ingresos</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="./?view=sales/index">Ingresos</a></li>
                  <li><a href="./?view=cashier-balance/index-personal">Cortes Personal</a></li>
                </ul>
              </li>
              <li class="treeview">
                <a href="#"><i class="fas fa-envelope"></i> <span>Notificaciones</span> <i class="fas fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li><a href="./?view=notifications/index">Notificación Citas</a></li>
                </ul>
              </li>
            <?php endif; ?>
          </section>
        </aside>
      <?php endif; ?>

      <!-- Content Wrapper. Contains page content -->
      <?php if (isset($_SESSION["user_id"]) || isset($_SESSION["client_id"])) : ?>
        <div class="content-wrapper">
          <div class="content">
            <?php View::load("index"); ?>
          </div>
        </div><!-- /.content-wrapper -->

        <footer class="main-footer">
          <div class="pull-right hidden-xs">
          </div>
          Copyright © <a href="https://www.v2technoconsulting.com" target="_blank">Techno Consulting</a>
        </footer>
      <?php else : ?>
        <style>
          body::after {
            content: "";
            background-image: url("assets/backgroundLogin.jpeg") !important;
            background-size:  100%!important;
            background-repeat: no-repeat;
            opacity: 0.2;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            position: absolute;
            z-index: -1;
          }
        </style>
        <div class="login-box">
          <div class="login-box-body">
            <form action="./?action=processLogin" method="post">

              <!--<span class="label label-primary"></span>-->
              <div class="form-group" style="text-align: center;">
                <img src="assets/logo.jpeg" width="230px;"><hr>
                
              </div>
              <div class="form-group has-feedback">
                <input type="text" name="username" required class="form-control" placeholder="Usuario" />
                <span class="glyphicon glyphicon-user form-control-feedback"></span>
              </div>
              <div class="form-group has-feedback">
                <input type="password" name="password" required class="form-control" placeholder="Contraseña" />
                <span class="glyphicon glyphicon-lock form-control-feedback"></span>
              </div>
              <div class="row">
                <div class="col-xs-12">
                  <button type="submit" class="btn btn-primary btn-block btn-flat">Acceder</button>
                </div><!-- /.col -->
              </div>
            </form>
          </div><!-- /.login-box-body -->
        </div><!-- /.login-box -->
      <?php endif; ?>

    </div><!-- ./wrapper -->
  </div>
  <!-- REQUIRED JS SCRIPTS -->

  <!-- jQuery 2.1.4 -->
  <!-- Bootstrap 3.3.2 JS -->
  <script src="plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
  <script src="plugins/dist/js/app.min.js" type="text/javascript"></script>

  <script src="plugins/datatables/jquery.dataTables.js"></script>
  <script src="plugins/datatables/dataTables.bootstrap.js"></script>

  <!-- Locales for moment.js-->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.19.4/locale/es.js"></script>
  <script src="https://cdn.datatables.net/plug-ins/1.10.15/sorting/datetime-moment.js"></script>

  <!--script src="plugins/datatables/jquery.dataTables.min.js"></script>
      <script src="plugins/datatables/dataTables.bootstrap.min.js"></script-->
  <!-- Optionally, you can add Slimscroll and FastClick plugins.
            Both of these plugins are recommended to enhance the
            user experience. Slimscroll is required when using the
            fixed layout. -->


</html>