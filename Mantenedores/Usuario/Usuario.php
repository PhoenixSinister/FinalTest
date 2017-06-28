<?php
session_start();
//Confirmar si existe una sesion activa en pagina
if (isset($_SESSION['username'])) {
    require '../../Connect.php';
} else {
    header('Location: ../../index.php');
}


if (isset($_POST['txtRutAgregar'])) {
    $rut = $_POST['txtRutAgregar'];
    $fecha = $_POST['txtFechaActual'];
    $nombre = $_POST['txtNombreAgregar'];
    $tipoPersona = $_POST['ddlTipoPersona'];
    $direccion = $_POST['txtDireccionAgregar'];
    $celular = $_POST['txtCelularAgregar'];
    $contrasena = $_POST['txtContrasenaAgregar'];
    $rol = $_POST['rol'];

    $query = "select id from usuario where rut = '" . $rut . "'";
    $result = mysqli_query($myconect, $query);
    
    

    if (mysqli_num_rows($result) == 0) {
        $query = "insert into usuario (rut, nombre, fecha, tipo_persona, direccion, celular, contrasena, id_padre) "
                . "values ('" . $rut . "','" . $nombre . "','" . $fecha . "','" . $tipoPersona . "','" . $direccion . "','" . $celular . "', '" .$contrasena . "', '" . $rol . "');";
        mysqli_query($myconect, $query);

        $query = "select id from usuario where rut = '" . $rut . "';";
        $result = mysqli_query($myconect, $query);
        while ($row = mysqli_fetch_array($result)) {
            $idUsuario = $row['id'];
        }

        //$query = "insert into usuario_rol (id_usuario, id_rol)"
        //        . " values (" . $idUsuario . "','" . $idPadre . "');";
        //mysqli_query($myconect, $query);

        $_SESSION['ErrorAgregarUsuario'] = "Usuario creado exitosamente";
    } else {
        $_SESSION['ErrorAgregarUsuario'] = "Usuario ya se encuentra registrado";
    }
}

if (isset($_POST['idUsuarioEditado'])) {
    $id = $_POST['idUsuarioEditado'];
    $rut = $_POST['txtRutEditar'];
    $nombre = $_POST['txtNombreEditar'];
    $tipoPersona = $_POST['ddlTipoPersona'];
    $contrasena = $_POST['txtContrasenaEditar'];
    $direccion = $_POST['txtDireccionEditar'];
    $celular = $_POST['txtCelularEditar'];
    $rol = $_POST['ddlRol'];

    $query = "update usuario set rut = '" . $rut . "' "
            . ", nombre = '" . $nombre . "'"
            . ", tipo_persona = '" . $tipoPersona . "'"
            . ", contrasena = '" . $contrasena . "'"
            . ", direccion = '" . $direccion . "'"
            . ", celular = '" . $celular . "'"
            . ", id_padre = '" . $rol . "'"
            . " where id = " . $id;
    mysqli_query($myconect, $query);
    /*
    if ($PerfilPersonal == 1) {
        $query = "select * from usuario_rol where id_usuario = " . $id . " and id_rol = 1;";
        $result = mysqli_query($myconect, $query);
        if (mysqli_num_rows($result) == 0) {
            $query = "insert into usuario_rol (id_usuario, id_rol) values (" . $id . ", 1)";
            mysqli_query($myconect, $query);
        }
    } else {
        $query = "select * from usuario_rol where id_usuario = " . $id . " and id_rol = 1;";
        $result = mysqli_query($myconect, $query);
        if (mysqli_num_rows($result) > 0) {
            $query = "delete from usuario_rol where id_rol = 1 and id_usuario = " . $id ;
        }
        mysqli_query($myconect, $query);
    }*/
    $_SESSION['ErrorAgregarUsuario'] = "Usuario editado";
}

if (isset($_POST['idElimina'])) {

    $idEliminar = $_POST['idElimina'];
    $queryEliminar = "delete from usuario where id = " . $idEliminar . ";";
    $result = mysqli_query($myconect, $queryEliminar);
    $_SESSION['ErrorAgregarUsuario'] = "Carga eliminada";
}

$queryRol = "select * from roles";
$resultRol = mysqli_query($myconect, $queryRol);

?>

<html>
    <head>  
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" >
        <link type="text/css" href="../../css/bootstrap.min.css" rel="stylesheet">  
        <script type="text/javascript" src="../../js/jquery.min.js"></script> 
        <script type="text/javascript" src="../../js/bootstrap.min.js"></script>
    </head>
    <body>  
        <!--Menu-->
        <header>
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header" style="margin-left: 8%">                        
                        <a class="navbar-brand " href="#">DURALEX</a>                        
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav nav-pills">
                            <li role="presentation"><a href="../../Personal/Personal.php" > Registrar Atencion </a></li>                                
                            <li role="presentation"><a href="../Usuario/Usuario.php" > Usuarios </a></li>                                
                            <li role="presentation"><a href="../Ambulancia/mantenedorAmbulancia.php" > Abogados </a> </li>
                            <li role="presentation"><a href="../Sintomas/Sintomas.php" > Estadisticas </a> </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right" style="margin-right: 8%">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Cuenta <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Usuario Conectado: <?php echo $_SESSION['username'] ?></a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="../../logout.php" > Salir </a></li>
                                </ul>  
                            <li>
                        </ul>   
                    </div>
                </div>
                </div>
            </nav>
        </header>

        <?php if (isset($_SESSION['ErrorAgregarUsuario'])) { ?>
            <div class="alert alert-info" role="alert" style="margin-left: 20%; margin-right: 20%">
                <span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
                <span class="sr-only">Información: </span>
                <?php echo $_SESSION['ErrorAgregarUsuario'] ?>
            </div>                     
            <?php
            unset($_SESSION['ErrorAgregarUsuario']);
        }
        ?>

        <!--Mantenedor -->

        <div class="row">
            <div class="col-lg-2">                
            </div>            
            <div class="col-lg-4 well center-block" style="margin-right: 2px">
                <table>
                    <tr>
                        <td>
                            <form class="form-inline" name="buscarUsuario" method="POST" action="">
                                <input type="text" name="txtRutBuscado" class="form-control" placeholder="Rut"/>                         
                                <input type="submit" class="btn btn-default" value="Buscar"/>                   
                            </form> 
                        </td>
                        <td>
                            <form class="form-inline" name="Agregar" method="POST" action="">
                                <input type="hidden" name="agregarUsuario" />
                                <input type="submit" class="btn btn-default btn-success" value="Agregar"/>                   
                            </form>
                        </td>
                    </tr>
                </table>
                <?php
                if (isset($_POST['txtRutBuscado'])) {
                    $rutBusqueda = $_POST['txtRutBuscado'];
                    $query = "select id, rut , nombre, tipo_persona
                                from usuario where rut like '%" . $rutBusqueda . "%';";
                    $result = mysqli_query($myconect, $query);
                    if (mysqli_num_rows($result) == 0) {
                        echo "No hay resultados";
                    } else {
                        ?>
                        <div class="panel panel-default" style="margin-left: 0%; margin-right: 2%">                                
                            <div class="panel-heading">Resultados de busqueda</div>     
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Rut</th>
                                            <th>Nombre</th>
                                            <th>Tipo Persona</th>
                                            <th></th>                             
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_array($result)) { ?>
                                            <tr>
                                                <td><?php echo $row['rut']; ?></td>
                                                <td><?php echo $row['nombre']; ?></td>
                                                <td><?php echo $row['tipo_persona']; ?></td>
                                                <td>
                                                    <table>
                                                        <tr>

                                                            <td>
                                                                <form method="post">
                                                                    <input type="hidden" name="idEditar" value="<?php echo $row['id']; ?>"/>
                                                                    <button type="submit" class="btn btn-default btn-group-xs" data-toggle="tooltip" data-placement="botton" title="Editar Usuario">
                                                                        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                            <td>
                                                                <form method="post">
                                                                    <input type="hidden" name="idElimina" value="<?php echo $row['id']; ?>"/>
                                                                    <button type="submit" class="btn btn-default btn-group-xs" data-toggle="tooltip" data-placement="botton" title="Eliminar">
                                                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                                                    </button>
                                                                </form>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table> 
                            </div>
                        </div>
                    <?php } ?>                    
                <?php } ?>
            </div> 
            
            <?php 

            ?>
            
            <div class="col-lg-4 well center-block">
                <?php if (isset($_POST['agregarUsuario'])) { ?>
                    <form class="form-group" name="crearUsuario" method="POST" action="">
                        <h3>Usuario Nuevo </h3>
                        <br>
                        <div class="input-group" style="margin-bottom: 15px">
                            <input type="text" name="txtRutAgregar" class="form-control" placeholder="Rut">
                        </div>
                        <div class="form-inline" style="margin-bottom: 15px">
                            <input type="text" name="txtNombreAgregar" class="form-control" placeholder="Nombre" >
                        </div>
                        <div class="form-inline" style="margin-bottom: 15px">
                            <?php $date = date('d/m/Y', time()); ?>
                            <input type="text" name="txtFechaActual" class="form-control" placeholder="Fecha actual" type="text" value="<?php echo "{$date}"; ?>" readonly >
                        </div>
                        <div class="form-inline" style="margin-bottom: 15px">
                            <select name="ddlTipoPersona" class="form-control">
                                <option value="natural" class="form-control">Natural</option>
                                <option value="juridica" class="form-control">Juridica</option>
                            </select>
                        </div>
                        
                        <div class="form-inline" style="margin-bottom: 15px">
                            <select name="rol" class="form-control">
                            <?php
                                    while($row = mysqli_fetch_array($resultRol)){ 
                            ?>
                                    <option value="<?php echo $row['ID'] ?>"><?php echo $row['DESCRIPCION'] ?></option>
                            <?php
                                    }  
                            ?>
                            </select>
                        </div>
                        <div class="form-inline" style="margin-bottom: 15px">
                            <input type="password" name="txtContrasenaAgregar" class="form-control" placeholder="Contraseña" >                            
                            <input type="password" name="txtContrasenaAgregar2" class="form-control" placeholder="Repita Contraseña" >
                        </div>
                        <div class="form-inline" style="margin-bottom: 15px">
                            <input type="text" name="txtDireccionAgregar" class="form-control" placeholder="Direccion">
                        </div>
                        <div class="input-group" style="margin-bottom: 15px">
                            <input type="text" name="txtCelularAgregar" class="form-control" placeholder="Celular">
                        </div>
                        <input type="submit" class="btn btn-default btn-success" value="Guardar"/>   
                    </form>                
                    <?php
                } else if (isset($_POST['idEditar'])) {
                    $idEditar = $_POST['idEditar'];
                    $query = "select id ,rut, nombre, contrasena, direccion, celular from usuario where id = " . $idEditar . ";";
                    $result = mysqli_query($myconect, $query);


                    while ($row = mysqli_fetch_array($result)) {
                        ?>
                        <form class="form-group" name="editarUsuario" method="POST" action="">
                            <h3>Editar Usuario</h3>
                            <br>
                            <input type="hidden" name="idUsuarioEditado" value="<?php echo $row['id']; ?>"/>
                            <div class="input-group" style="margin-bottom: 15px">
                                <input type="text" name="txtRutEditar" class="form-control" placeholder="Rut" value="<?php echo $row['rut']; ?>">
                            </div>
                            <div class="form-inline" style="margin-bottom: 15px">
                                <input type="text" name="txtNombreEditar" class="form-control" placeholder="Nombre" value="<?php echo $row['nombre']; ?>">                          
                            </div>
                            
                            
                            
                            <div class="form-inline" style="margin-bottom: 15px">
                                <input type="password" name="txtContrasenaEditar" class="form-control" placeholder="Contraseña" value="<?php echo $row['contrasena']; ?>">
                                <input type="password" name="txtContrasenaEditar2" class="form-control" placeholder="Repita Contraseña" value="<?php echo $row['contrasena']; ?>">
                            </div>
                            <div class="input-group" style="margin-bottom: 15px">
                                <input type="text" name="txtDireccionEditar" class="form-control" placeholder="Direccion" value="<?php echo $row['direccion']; ?>">
                            </div>
                            <div class="input-group" style="margin-bottom: 15px">
                                <input type="text" name="txtCelularEditar" class="form-control" placeholder="Celular" value="<?php echo $row['celular']; ?>">
                            </div> 
                            <div class="form-inline" style="margin-bottom: 15px">
                                <select name="ddlTipoPersona" class="form-control">
                                    <option value="natural" class="form-control">Natural</option>
                                    <option value="juridica" class="form-control">Juridica</option>
                                </select>
                            </div>
                            <div class="form-inline" style="margin-bottom: 15px">
                                <select name="ddlRol" class="form-control">
                                <?php
                                        while($row = mysqli_fetch_array($resultRol)){ 
                                ?>
                                        <option value="<?php echo $row['ID'] ?>"><?php echo $row['DESCRIPCION'] ?></option>
                                <?php
                                        }  
                                ?>
                                </select>
                            </div>
                            
                            <div class="input-group" style="margin-bottom: 15px">
                                <input type="submit" class="btn btn-default btn-success right" value="Guardar"/>                           
                            </div>

                        </form>
                        <?php
                    }
                } else {
                    ?>
                    <center><img src="../../img/duralex.jpg" height="350" width="350"/></center>
                <?php } ?>
            </div>            
            <div class="col-lg-2">                
            </div> 
        </div>
    </body>
</html>
