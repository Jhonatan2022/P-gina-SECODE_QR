<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    http_response_code(404);
    header('Location: ../../index.php');
}
require_once('../../main.php');
require_once(BaseDir . '/models/database/database.php');
$records = $connection->prepare('SELECT Ndocumento,Img_perfil, TipoImg,Nombre,rol FROM usuario WHERE Ndocumento = :id ');
$records->bindParam(':id', $_SESSION['user_id']);

if ($records->execute()) {
    $resultsUser = $records->fetch(PDO::FETCH_ASSOC);
} else {
    $message = array(' Error', 'Ocurrio un error en la consulta datos user. intente de nuevo.', 'error');
}
if ($resultsUser['rol'] == 2) {
    $data = $connection->query("SELECT * FROM usuario");
    $data->execute();
    $usuarios = $data->fetchAll(PDO::FETCH_ASSOC);
} else {
    http_response_code(404);
    header('Location: ../../index.php');
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/es.css">
    <link rel="stylesheet" href="../css/fontawesome.css">
    <link rel="stylesheet" href="../css/Flex.css">
    <script src="https://kit.fontawesome.com/9165abed33.js" crossorigin="anonymous"></script>
    <title>Tablero Gestión</title>
</head>
<body style="background-color:#DEDEDE">
    <nav class="main-navbar">
        <ul class="navbar-container">
            <li class="logo" style="margin-top: -4%px;">
                <a href="../../views/" class="navbar-link">
                    <img src="../img/logito.svg" style="width:15%; margin-right:0%;">
                    <span class="link-text" style="font-weight:500;">SECODE_QR</span>
                </a>
            </li>
            <li class="navbar-item">
                <a href="../views/tablero.php" class="navbar-link">
                    <svg aria-hidden="true" focusable="false" data-prefix="fab" xmlns="http://www.w3.org/2000/svg" viewBox="0 2 512 512" class="fa-primary" role="img" style="width:30%; margin-top:2%;">
                        <path d="M0 96C0 60.7 28.7 32 64 32H448c35.3 0 64 28.7 64 64V416c0 35.3-28.7 64-64 64H64c-35.3 0-64-28.7-64-64V96zm64 64V416H224V160H64zm384 0H288V416H448V160z" />
                    </svg>
                    <span class="link-text" style="margin-top:2%;">Tablero Gestión</span>
                </a>
            </li>
            <li class="navbar-item">
                <a href="../views/registraru.php" class="navbar-link">
                    <svg aria-hidden="true" focusable="false" data-prefix="fab" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" style="width:30%;  margin-top:2%;">
                        <path d="M352 128c0 70.7-57.3 128-128 128s-128-57.3-128-128S153.3 0 224 0s128 57.3 128 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM504 312V248H440c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V136c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H552v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z" />
                    </svg>
                    <span class="link-text" style="margin-top:2%;">Registrar</span>
                </a>
            </li>
            <li class="navbar-item">
                <a href="planes.php" class="navbar-link">
                    <svg aria-hidden="true" focusable="false" data-prefix="fab" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="fa-primary" role="img" style="width:17px;  margin-top:-40px;">
                        <path d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V384c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H384 96zm0 384H352v64H96c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16zm16 48H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16z" />
                    </svg>
                    </svg>
                    <span class="link-text" style="margin-top:-40px;">Planes de usuarios</span>
                </a>
            </li>
            <li class="navbar-item">
                <a href="getAcces.php" class="navbar-link">
                    <svg aria-hidden="true" focusable="false" data-prefix="fab" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="fa-primary" role="img" style="width:30%;  margin-top:2%;">
                        <path d="M224 256c70.7 0 128-57.3 128-128S294.7 0 224 0S96 57.3 96 128s57.3 128 128 128zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512H418.3c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304H178.3z" />
                    </svg>
                    <span class="link-text" style="margin-top:2%;">Acceso Administradores</span>
                </a>
            </li>
            <li class="navbar-item">
                <a href="../../views/perfil.php" class="navbar-link">
                    <svg aria-hidden="true" focusable="false" data-prefix="fas" data-icon="power-off" class="fa-primary" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" style="width:30%;  margin-top:2%;">
                        <path fill="currentColor" d="M400 54.1c63 45 104 118.6 104 201.9 0 136.8-110.8 247.7-247.5 248C120 504.3 8.2 393 8 256.4 7.9 173.1 48.9 99.3 111.8 54.2c11.7-8.3 28-4.8 35 7.7L162.6 90c5.9 10.5 3.1 23.8-6.6 31-41.5 30.8-68 79.6-68 134.9-.1 92.3 74.5 168.1 168 168.1 91.6 0 168.6-74.2 168-169.1-.3-51.8-24.7-101.8-68.1-134-9.7-7.2-12.4-20.5-6.5-30.9l15.8-28.1c7-12.4 23.2-16.1 34.8-7.8zM296 264V24c0-13.3-10.7-24-24-24h-32c-13.3 0-24 10.7-24 24v240c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24z">
                        </path>
                    </svg>
                    <span class="link-text" style="margin-top:2%;">Cerrar sesión</span>
                </a>
            </li>
        </ul>
    </nav>
    <main>
        <img src="../img/SECODE_QR.png" style="margin-left:40%;">
        <hr class="hr" style="margin-left:10%;">
        <div style="margin-left:60%; margin-bottom:5%">
            <a class="btn btn-info" href="../views/registraru.php">Registrar</a>
        </div>
        <br>
        <section>
            <div class="col-md-9 text-center" style="margin-left:auto; margin-top:-15%;">
                <form action="reporte.php" method="post" accept-charset="utf-8">
                    <div class="row">
                        <div class="col">
                            <input type="date" name="fechaCreacion" class="form-control" placeholder="Fecha de Inicio" required>
                        </div>
                        <div class="col">
                            <input type="date" name="fechaFin" class="form-control" placeholder="Fecha Final" required>
                        </div>
                        <div class="col">
                            <span class="btn btn-dark mb-1" id="filtro"><i class="fas fa-search"></i></span>
                            <button type="submit" class="btn btn-danger mb-1"><i class="far fa-file-pdf" style="width:100px;"></i></button>
                        </div>
                        <div class="col">
                            <a href="alluser.php" target="_blank" class="btn btn-success mb-1"><i class="fas fa-users" style="width:100px;"></i></a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-10 text-center mt-5">
                <span id="loaderFiltro"> </span>
            </div>
            <div class="col-10" style="margin-left:1%; margin-top:5%;">
                <div class="table resultadoFiltro">
                    <table class="table table-hover align-text-top" id="tablausuario">
                        <thead>
                            <tr>
                                <th scope="col">N°Documento</th>
                                <th scope="col">Nombre</th>
                                <th scope="col">Direccion</th>
                                <th scope="col">Genero</th>
                                <th scope="col">Correo</th>
                                <th scope="col">Fecha creación</th>
                                <th scope="col">Telefono</th>
                                <th scope="col">Modificar</th>
                            </tr>
                        </thead>
                        <?php
                        include('configuracion.php');
                        $sqlfinalsecode = ('SELECT * FROM usuario ORDER BY fechaCreacion ASC');
                        $query = mysqli_query($con, $sqlfinalsecode);
                        $i = 1;
                        while ($dataRow = mysqli_fetch_array($query)) { ?>
                            <tbody>
                                <tr>
                                    <td><?php echo $dataRow['Ndocumento']; ?> </td>
                                    <td> <?php echo $dataRow['Nombre']; ?> </td>
                                    <td> <?php echo $dataRow['Direccion']; ?> </td>
                                    <td> <?php echo $dataRow['Genero']; ?></td>
                                    <td> <?php echo $dataRow['Correo']; ?> </td>
                                    <td> <?php echo $dataRow['fechaCreacion']; ?></td>
                                    <td> <?php echo $dataRow['Telefono']; ?></td>
                                    <td>
                                        <a class="btn btn-warning mb-10" href="editar.php?id=<?= $dataRow['Ndocumento'] ?> ">
                                            <i class="fas fa-user-edit"></i>
                                            <a class="btn btn-danger" href="eliminar.php?id=<?= $dataRow['Ndocumento'] ?>">
                                                <i class="fa fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                            </tbody>
                    </table>
                </div>
        </section>
    </main>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#tablausuario').DataTable();
        });
    </script>
    <style>
        body {
            margin-top: 0px;
            font-family: var(--bs-body-font-family);
            font-size: var(--bs-body-font-size);
            font-weight: var(--bs-body-font-weight);
            line-height: var(--bs-body-line-height);
            color: var(--bs-body-color);
            text-align: var(--bs-body-text-align);
            background-color: var(--bs-body-bg);
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
            background: #fff;
        }
        .logo {
            margin-top: -70px;
        }
        .table {
            margin-left: center;
            margin-top: 20px;
        }
        .hr {
            width: 80%;
            background-color: #530046;
            margin-top: 10px;
            border: 1px solid;
            border-color: #530046;
        }
    </style>
    <?php
    if (isset($_GET['estado'])) {
        $estado = $_GET['estado'];
        if ($estado == 1) {
            echo "<script> alert('Usuario modificado correctamente'); </script>";
        } elseif ($estado == 2) {
            echo "<script> alert('ERROR al modificar usuario'); </script>";
        }
    } ?>
    <script>
        $(function() {
            setTimeout(function() {
                $('body').addClass('loaded');
            }, 1000);
            //FILTRANDO REGISTROS
            $("#filtro").on("click", function(e) {
                e.preventDefault();
                loaderF(true);
                var f_ingreso = $('input[name=fechaCreacion]').val();
                var f_fin = $('input[name=fechaFin]').val();
                console.log(f_ingreso + '' + f_fin);
                if (f_ingreso != "" && f_fin != "") {
                    $.post("filtro.php", {
                        f_ingreso,
                        f_fin
                    }, function(data) {
                        $("#tablausuario").hide();
                        $(".resultadoFiltro").html(data);
                        loaderF(false);
                    });
                } else {
                    $("#loaderFiltro").html('<p style="color:red;  font-weight:bold;">Debe seleccionar ambas fechas</p>');
                }
            });
            function loaderF(statusLoader) {
                console.log(statusLoader);
                if (statusLoader) {
                    $("#loaderFiltro").show();
                    $("#loaderFiltro").html('<img class="img-fluid" src="../img/cargando.svg" style="left:50%; right: 50%; width:50px;">');
                } else {
                    $("#loaderFiltro").hide();
                }
            }
        });
    </script>
</body>
</html>