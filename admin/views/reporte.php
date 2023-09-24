<?php
session_start();
require_once('../../vendor/autoload.php');
require_once('../../models/database/database.php');
require_once('../../models/user.php');
//SQL para consultas Empleados
$fechaInit = date("Y-m-d", strtotime($_POST['fechaCreacion']));
$fechaFin  = date("Y-m-d", strtotime($_POST['fechaCreacion']));
$query = $connection->prepare('SELECT us.Ndocumento,us.Nombre,us.Correo,us.fechaCreacion ,tipsus.TipoSuscripcion 
FROM usuario AS us LEFT OUTER JOIN Suscripcion as sus
ON sus.Ndocumento = us.Ndocumento LEFT OUTER JOIN 
TipoSuscripcion AS tipsus ON sus.TipoSuscripcion = tipsus.IDTipoSuscripcion 
WHERE (fechaCreacion>=:fechaIni) ORDER BY fechaCreacion ASC');
$query->bindParam(':fechaIni', $fechaInit);
$query->execute();
$usuario = $query->fetchAll(PDO::FETCH_ASSOC);
$alluserA = [];
?>
<?php ob_start();
?>
<html lang="en">
<head>
    <link rel="shortcut icon" type="image/png" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/img/logo.png' ?>">
    <link rel="stylesheet" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/css/all.min.css' ?>">
    <link rel="stylesheet" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/bootstrap/css/bootstrap.min.css' ?>">
    <link rel="stylesheet" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/css/animate.css' ?>">
    <link rel="stylesheet" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/css/meanmenu.min.css' ?>">
    <link rel="stylesheet" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/css/main.css' ?>">
    <link rel="stylesheet" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/css/responsive.css' ?>">
    <link rel="stylesheet" href="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/css/formstyle.css' ?>">
    <script src="https://raw.githack.com/eKoopmans/html2pdf/master/dist/html2pdf.bundle.js"></script>
    <meta charset="UTF-8">
    <title>Reporte</title>
</head>
<body>
    <main>
        <div class="container">
            <h1>Reporte de usuario</h1>
            <?php foreach ($usuario as  $usu) { ?>
                <table border='2px' style="width: 70vw;" class='table table-striped  '>
                    <thead class="thead-dark">
                        <tr>
                            <?php foreach ($usu as  $us => $value) { ?>
                                <th scope="col"><?php echo $us ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($usu as  $us => $value) { ?>
                                <td><?php echo $value ?></td>
                            <?php } ?>
                        </tr>
                    </tbody>
                </table>
                <br>
            <?php } ?>
        </div>
    </main>
    <style>
        table {
            border: 1px solid black;
            width: 100%;
        }
        th,
        td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
    </style>
    <!-- jquery -->
    <script src="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/js/jquery-1.11.3.min.js' ?>"></script>
    <script src="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/bootstrap/js/bootstrap.min.js' ?>"></script>
    <script src="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/js/jquery.isotope-3.0.6.min.js' ?>"></script>
    <script src="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/js/jquery.magnific-popup.min.js' ?>"></script>
    <script src="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/js/jquery.meanmenu.min.js' ?>"></script>
    <script src="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/js/sticker.js' ?>"></script>
    <script src="<?= 'http://' . $_SERVER['HTTP_HOST'] . '/secodeqr/views/assets/js/main.js' ?>"></script>
</body>
</html>
<?php
$html_doc = ob_get_clean();
// reference the Dompdf namespace
use Dompdf\Dompdf;
// instantiate and use the dompdf class
$dompdf = new Dompdf();
$dompdf->loadHtml($html_doc);
$options = $dompdf->getOptions();;
$options->set(array('isRemoteEnabled' => true));
$dompdf->setOptions($options);
$dompdf->setPaper('legal', 'landscape');
// Render the HTML as PDF
$doc = $dompdf->render();
$output = $dompdf->output();
// Output the generated PDF to Browser
$dompdf->stream("reporte.pdf", array("Attachment" => 0));
?>