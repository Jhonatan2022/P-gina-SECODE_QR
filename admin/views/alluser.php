<?php
session_start();
require_once('../../vendor/autoload.php');
require_once('../../models/database/database.php');
require_once('../../models/user.php');
if (isset($_SESSION['user_id'])) {
    $userActual = getUser($_SESSION['user_id']);
    if ($userActual['rol'] == 1) {

        http_response_code(404);
        header('Location: ../../index.php');
    } else {
        $alluser = 'SELECT 
        us.Ndocumento,tipdoc.TipoDocumento,us.Nombre,us.Apellidos,us.Correo,tipsus.TipoSuscripcion, sus.FechaExpiracion ,us.Direccion,lc.Localidad, gn.Genero,us.Telefono
        FROM usuario AS us
        LEFT OUTER JOIN tipodocumento AS tipdoc
        ON us.TipoDoc = tipdoc.IDTipoDoc
        LEFT OUTER JOIN genero AS gn
        ON us.Genero = gn.IDGenero
        LEFT OUTER JOIN estrato AS est 
        ON us.Estrato = est.IDEstrato
        LEFT OUTER JOIN rol AS rl
        ON us.rol = rl.id
        LEFT OUTER JOIN localidad AS lc
        ON us.Localidad = lc.IDLocalidad
        LEFT OUTER JOIN Suscripcion as sus
        ON sus.Ndocumento = us.Ndocumento
        LEFT OUTER JOIN TipoSuscripcion AS tipsus
        ON sus.TipoSuscripcion = tipsus.IDTipoSuscripcion
        LEFT OUTER JOIN eps 
        ON us.id = eps.id
        LEFT OUTER JOIN estrato AS estr 
        ON us.Estrato = estr.IDEstrato';
        $alluser = $connection->prepare($alluser);
        $alluser->execute();
        $alluser = $alluser->fetchAll(PDO::FETCH_ASSOC);
    }
} else {
    http_response_code(404);
    header('Location: ../../index.php');
}
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="shortcut icon" type="image/png" href="./assets/img/logo.png">
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
        <div class="container1">
            <h1>Usuarios</h1>
            <?php foreach ($alluser as  $allus) { ?>
                <table border='2px' class='table table-striped'>
                    <thead class="thead-dark">
                        <tr>
                            <?php foreach ($allus as  $user => $values) { ?>
                                <th scope="col"><?php echo $user ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <?php foreach ($allus as  $user => $values) { ?>
                                <td><?php echo $values ?></td>
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
//$dompdf->setPaper('A4','portrait');
// Render the HTML as PDF
$doc = $dompdf->render();
$output = $dompdf->output();
//file_put_contents('reporte.pdf', $output);
// Output the generated PDF to Browser
$dompdf->stream("reporte.pdf", array("Attachment" => 0));
?>