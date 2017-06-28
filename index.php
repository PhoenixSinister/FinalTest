<html>
    <head>
        <script type="text/javascript">
            function NoLogin()
            {
                alert('Usuario invalido');
                window.location = "index.php";
            }
        </script>  
        <?php
        session_start();
        if (isset($_POST['txtRut'])) {
            require 'connect.php';
            $rut = $_POST['txtRut'];
            $password = $_POST['txTContraseña'];
            $result = mysqli_query($myconect, 'select * from USUARIO where RUT = "' . $rut . '"  and CONTRASENA = "' . $password . '"');
            if (mysqli_num_rows($result) == 1) {
                $resultName = mysqli_query($myconect, 'select  nombre, ID from usuario where RUT = "' . $rut . '"');

                while ($row = mysqli_fetch_array($resultName)) {
                    $_SESSION['username'] = $row['nombre'];
                    $_SESSION['idUserName'] = $row['ID'];
                    $id = $row['ID'];
                }
                $resultTipo = mysqli_query($myconect, "select ID_ROL from usuario_rol where ID_USUARIO = " . $id);
                while ($row = mysqli_fetch_array($resultTipo)) {
                    $_SESSION['tipo'] = $row['ID_ROL'];
                }
                if (mysqli_num_rows($resultTipo) > 1) {
                    header('Location: Mantenedores/Usuario/Usuario.php');
                } else {
                    if ($_SESSION['tipo'] == 1) {
                        header('Location: Mantenedores/Usuario/Usuario.php');
                    } else {
                        header('Location: Mantenedores/Usuario/Usuario.php');
                    }
                }
            } else {
                echo "<script> NoLogin(); </script>";
            }
        }
        ?>
        <title>Web Admin</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/logincss.css">
        <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
        <script src="js/jquery-3.2.0.min.js"></script> 
        <script src="js/bootstrap.min.js"></script> 
        <script src="js/jquery.validate.min.js"></script>
        <script src="js/loginjavascript.js"></script>
    <head>

    <body>

        <header class="main-header" role="banner">
           
        </header>

        <section>

            <div class="wrapper">
              

                    <form id="formIngreso" class="form-signin" method="Post">
                        <h2 class="form-signin-heading">Bienvenido a Duralex</h2>
                        <input class="form-control" type="text" id="txtRut" name="txtRut" placeholder="Rut usuario" style="margin-bottom: 15px" required autofocus/>
                        <input class="form-control" type="password" name="txTContraseña" placeholder="Contraseña" style="margin-bottom: 15px" required/>
                        <button class= "btn btn-lg btn-primary btn-block" value="submint">Ingresar</button>
                    </form>
                </div>    
            

        </section>
        <footer class="main-footer" role="banner">
           
        </footer>
    </body>

</html>