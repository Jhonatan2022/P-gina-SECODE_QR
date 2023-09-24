<?php
session_start();

if (isset($_SESSION['user_id'])) {
  header('Location: ./index.php');
}

//importamos conexion base de datos
require_once '../models/database/database.php';

//si el usuario va a iniciar sesion
if (
  isset($_POST['email']) &&
  isset($_POST['password']) &&
  !empty($_POST['email']) &&
  !empty($_POST['password'])
) {

  $email_user1 = $_POST['email'];
  $password_user1 = $_POST['password'];

  if (filter_var($email_user1, FILTER_VALIDATE_EMAIL)) {

    $consult = " SELECT Nombre,Correo,Contrasena,Ndocumento,Direccion, Genero, FechaNacimiento,Telefono, Img_perfil, TipoImg
    FROM usuario WHERE Correo= :correo";
    $parametros = $connection->prepare($consult);
    $parametros->bindParam(':correo', $email_user1);
    $parametros->execute();
    $results = $parametros->fetch(PDO::FETCH_ASSOC);

    if (!empty($results)) {
      try {
        if (count($results) > 0 && password_verify($password_user1, $results['Contrasena'])) {
          //ssecure seesion start
          $keysecret = $results['Ndocumento'];
          $_SESSION['fingerprint'] = md5($_SERVER['HTTP_USER_AGENT'] . $keysecret . $_SERVER['REMOTE_ADDR']);
          session_regenerate_id(true);

          //varaibles user definition from session
          $_SESSION['user_id'] = $results['Ndocumento'];
          $_SESSION['Correo'] = $results['Correo'];
          $_SESSION['Nombre'] = $results['Nombre'];
          $_SESSION['Direccion'] = $results['Direccion'];
          $_SESSION['Genero'] = $results['Genero'];
          $_SESSION['FechaNacimiento'] = $results['FechaNacimiento'];
          $_SESSION['Telefono'] = $results['Telefono'];
          if (empty($results['Img_perfil']) || $results['Img_perfil'] == null) {
            $_SESSION['Img_perfil'] = "./assets/img/userimg.png";
          } else {
            $_SESSION['Img_perfil'] = 'data:' . $results['TipoImg'] . ";base64," . base64_encode($results['Img_perfil']);
          }

          header("Location: ./index.php");
        } else {

          $message = array(' Error', 'Datos ingresados erroneos', 'warning');
        }
      } catch (Exception $e) {

        $message = array(' Error', `Ocurrio un error $e`, 'error');
      }
    } else {
      $message = array(' Error', 'Correo no Registrado, primero registrese e intente de nuevo.', 'warning');
    }
  } else {

    $message = array(' Error', 'Correo no valido. intente de nuevo.', 'warning');
  }
} //si el usuario va a registrarse
elseif (
  isset($_POST['user-name']) &&
  isset($_POST['user-email']) &&
  isset($_POST['user-password']) &&
  isset($_POST['num-doc']) &&
  isset($_POST['terminos']) &&
  !empty($_POST['num-doc']) &&
  !empty($_POST['user-email']) &&
  !empty($_POST['user-password']) &&
  !empty($_POST['user-name'])
) {

  //variables de datos ingresados
  $email_user = $_POST['user-email'];
  $numdoc = $_POST['num-doc'];
  $password_user = $_POST['user-password'];
  $name_user = ucwords($_POST['user-name']);
  $token = bin2hex(random_bytes(16));

  if (filter_var($email_user, FILTER_VALIDATE_EMAIL)) {

    //consulta que verifica la existencia de el correo ingresado
    $consult = "SELECT Correo,Ndocumento FROM usuario WHERE Correo= :useremail OR Ndocumento= :Ndocument";
    $params = $connection->prepare($consult);
    $params->bindParam(':Ndocument', $numdoc);
    $params->bindParam(':useremail', $email_user);

    if ($params->execute()) { //ejecucion consulta
      $results1 = $params->fetch(PDO::FETCH_ASSOC);

      //si el resultado de la consulta es igual al del ingresado
      if (!empty($results1)) {
        if (strtolower($results1["Correo"]) == strtolower($email_user)) {
          $message = array(' Error', 'Correo registrado, revise e intente de nuevo', 'warning');
        } elseif ($results1["Ndocumento"] == $numdoc) {
          $message = array(' Error', 'Numero de documento registrado, revise e intente de nuevo', 'warning');
        }
      } else {
        if (strlen($numdoc) > 11) {
          $message = array(' Error', 'Numero de documento excede el numero de caracteres. Intente de nuevo', 'warning');
        } else {
          $consult = "INSERT INTO usuario 
        (Ndocumento, Nombre,direccion,Genero,Correo,Contrasena,FechaNacimiento,id,Img_perfil,token_reset,TipoImg, fechaCreacion) 
        VALUES 
        (:ndoc, :username, null, null, :useremail, :userpassword, null, :ideps, null, :token, null, :fecha)";
          $params = $connection->prepare($consult);
          $params->bindParam(':useremail', $email_user);
          $password = password_hash($password_user, PASSWORD_BCRYPT);
          $params->bindParam(':userpassword', $password);
          $params->bindParam(':username', $name_user);
          $id_eps = 10;
          $params->bindParam(':ideps', $id_eps);
          $params->bindParam(':ndoc', $numdoc);
          $params->bindParam(':token', $token);
          $fecha = date("Y-m-d");
          $params->bindParam(':fecha', $fecha);
          //estabklecemos los parametros de la consulta

          $query = "INSERT INTO `Suscripcion` (`IDSuscripcion`, `Ndocumento`, `TipoSuscripcion`) VALUES (NULL, :numdoc, 1)";
          $params2 = $connection->prepare($query);
          $params2->bindParam(':numdoc', $numdoc);
          if ($params->execute() && $params2->execute()) {
            $message = array(' Ok Registrado ', ' Realizado correctamente, usuario registrado, inicie sesión, para continuar...', 'success');
          } else {
            $message = array(' Error', 'Perdon hubo un error al crear el usuario', 'error');
          }
        }
      }
    } else {
      $message = array(' Error', 'Perdon hubo un error al crear el usuario', 'error');
    }
  } else {
    $message = array(' Error', 'Correo no valido. intente de nuevo.', 'warning');
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar y registrar</title>
  <!-- fontawesome -->
  <link rel="stylesheet" href="assets/css/all.min.css">
  <!-- estilos -->
  <link rel="stylesheet" href="assets/css/styles.css" />
  <!-- favicon -->
  <link rel="shortcut icon" type="image/png" href="./assets/img/logo.png">
  <link rel="stylesheet" href="npm i bootstrap-icons">
  <!-- google font -->
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Poppins:400,700&display=swap" rel="stylesheet">
  <!-- fontawesome -->
  <!-- jQuery -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <!-- SweetAlert2 -->
  <?php
  include('./templates/sweetalerts2.php');
  ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/7.2.0/sweetalert2.all.min.js"></script>
  <style>
    #terminos {
      background: none;
      color: rgb(0, 0, 0);
      border: 0;
      font-size: 18px;
      font-weight: 500;
      cursor: pointer;
    }

    #terminos:hover {
      cursor: pointer;
    }

    .terminos {
      max-width: 90%;
      margin: auto;
      color: black;
      text-align: justify;
      font-size: 18px;
      margin-right: 10px;
    }

    b {
      font-size: 30px;
      color: black;
      text-align: left;
    }

    button {
      font-size: 16px;
      margin-left: 5px;
    }

    u {
      margin-left: 10px
    }
    button {
    background: none;
    color: rgb(0, 0, 0);
    border: 0;
    font-size: 18px;
    font-weight: 500;
    cursor: pointer;
  }

  .terminos {
    max-width: 90%;
    margin: auto;
    color: black;
    text-align: justify;
    font-size: 18px;
  }

  b {
    font-size: 30px;
    color: black;
    text-align: left;
  }

  button {
    font-size: 16px;
    margin-left: 5px;
  }

  u {
    margin-left: 10px
  }
  </style>
</head>
<body>
  <?php if (!empty($message)) :
  ?>
    <script>
      Swal.fire(
        '<?php echo $message[0]; ?>',
        '<?php echo $message[1]; ?>',
        '<?php echo $message[2]; ?>')
    </script>
  <?php endif;
  ?>
  <!--PreLoader-->
  <div class="loader">
    <div class="inner"></div>
    <div class="inner"></div>
    <div class="inner"></div>
    <div class="inner"></div>
    <div class="inner"></div>
  </div>
  <!--PreLoader Ends-->
  <div class="container">
    <div class="forms-container">
      <div class="signin-signup">
        <form action="./iniciar.php" method="POST" class="sign-in-form">
          <!-- logo -->
          <a href="./index.php">
            <img src="assets/img/logo.png" alt=""> </a>
          <!-- logo -->
          <h2 class="title">Iniciar sesión</h2>
          <div class="input-field">
            <i class="fa fa-at"></i>
            <input type="email" name="email" placeholder="Correo" required />
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Contraseña" required />
          </div>
          <input type="submit" value="Ingresar" class="btn solid" />
          <a href="./recovery/index.php" class="learn-more">
            <span class="circle" aria-hidden="true">
              <span class="icon arrow"></span></span>
            <span class="button-text">Recuperar contraseña</span>
          </a>
        </form>
        <form action="./iniciar.php" class="sign-up-form" method="POST">
          <!-- logo -->
          <a href="./index.php">
            <img src="assets/img/logo.png" alt=""> </a>
          <!-- logo -->
          <h2 class="title">Registrarse</h2>
          <div class="input-field">
            <i class="fas fa-user"></i>
            <input type="text" placeholder="Nombres" maxlength="30" name="user-name" required />
          </div>
          <div class="input-field">
            <i class="fas fa-id-card"></i>
            <input type="number" placeholder="Numero Documento" name="num-doc" maxlength="10" required />
          </div>
          <div class="input-field">
            <i class="fas fa-at"></i>
            <input type="email" placeholder="Email" maxlength="60" name="user-email" required />
          </div>
          <div class="input-field">
            <i class="fas fa-lock"></i>
            <input type="password" placeholder="Password" name="user-password" required />
          </div>
          <input type="submit" class="btn" value="Registrar" />
          <div id="terminos">
            <input type="checkbox" name="terminos" required>
            <u class="terminos">Terminos y Condiciones</u>
          </div>
        </form>
      </div>
    </div>
    <div class="panels-container">
      <div class="panel left-panel">
        <div class="content">
          <h3>Eres nuevo ?</h3>
          <p>
            Oprime el botón para registrarte.
          </p>
          <button class="btn transparent" id="sign-up-btn">
            Registrarse
          </button>
        </div>
        <img src="./assets/img/log.svg" class="image" alt="" />
      </div>
      <div class="panel right-panel">
        <div class="content">
          <h3>Ya tienes cuenta ? </h3>
          <p>
            Oprime el botón para iniciar sesión.
          </p>
          <button class="btn transparent" id="sign-in-btn">
            Iniciar sesión
          </button>
        </div>
        <img src="./assets/img/register.svg" class="image" alt="" />
      </div>
    </div>
  </div>
  <script src="assets/js/app.js"></script>
  <!-- jquery -->
  <script src="assets/js/jquery-1.11.3.min.js"></script>
  <!-- main js -->
  <script src="assets/js/main.js"></script>
  <script src="./assets/js/terminos.js"></script>
</body>
</html>