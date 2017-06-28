<?php
session_start();
//Confirmar si existe una sesion activa en pagina
if (isset($_SESSION['username'])) {
    require '../Connect.php';
} else {
    header('Location: ../index.php');
}

//Generar consulta
if (isset($_POST['generarConsulta'])) {
    $idCliente = $_POST['generarConsulta'];
    $_SESSION['idCliente'] = $idCliente;

    $queryCCU = "select * from consulta";
    $result = mysqli_query($myconect, $queryCCU);

    if (mysqli_num_rows($result) == 0) {
        $idConsulta = 1;
        $idConsultaUsuario = 1;
    } else {
        $queryC = "select max(id)+1 as idConsulta from CONSULTA";
        $queryCU = "select max(id)+1 as idConsultaUsuario from CONSULTA_USUARIO";

        $resultC = mysqli_query($myconect, $queryC);
        $resultCU = mysqli_query($myconect, $queryCU);

        while ($row = mysqli_fetch_array($resultC)) {
            $idConsulta = $row['idConsulta'];
        }
        while ($row = mysqli_fetch_array($resultCU)) {
            $idConsultaUsuario = $row['idConsultaUsuario'];
        }
    }
    $queryInsertConsulta = "insert into consulta (id, fecha) values (" . $idConsulta . " , (select now()) );";
    $queryInsertConsultaUsuario = "insert into consulta_usuario (id, id_usuario, id_consulta) values (" . $idConsultaUsuario . "," . $idCliente . "," . $idConsulta . ");";

    mysqli_query($myconect, $queryInsertConsulta);
    mysqli_query($myconect, $queryInsertConsultaUsuario);

    $_SESSION['idConsulta'] = $idConsulta;
}

//Asociar sintoma a consulta
if (isset($_POST['idSintoma'])) {
    $idSintoma = $_POST['idSintoma'];
    $idConsulta = $_SESSION['idConsulta'];

    $queryCantidadCS = "select (case count(*) when 0 then 1 else max(id)+1 end) as maxid from CONSULTA_SINTOMA";
    $resultCantidadCS = mysqli_query($myconect, $queryCantidadCS);
    while ($row = mysqli_fetch_array($resultCantidadCS)) {
        $idConsultaSintoma = $row['maxid'];
    }

    $query = "select * from consulta_sintoma where id_consulta = " . $idConsulta . " and id_sintoma = " . $idSintoma . ";";
    $result = mysqli_query($myconect, $query);
    if (mysqli_num_rows($result) == 0) {
        $queryInsert = "insert into consulta_sintoma (id, id_consulta, id_sintoma) values ( " . $idConsultaSintoma . ", " . $idConsulta . ", " . $idSintoma . ");";
        mysqli_query($myconect, $queryInsert);
    }
}

//Eliminar sintoma a consulta
if (isset($_POST['idConsultaAEliminar'])) {
    $idConsultaAEliminar = $_POST['idConsultaAEliminar'];
    $query = "delete from consulta_sintoma where id = " . $idConsultaAEliminar . ";";
    mysqli_query($myconect, $query);
}

//Buscar consulta
if (isset($_POST['buscarConsulta'])) {
    $buscarConsulta = $_POST['buscarConsulta'];

    if ($buscarConsulta != "") {
        $query = "select cu.ID_USUARIO as idUsuario from consulta c left join CONSULTA_USUARIO cu on c.ID = cu.ID_CONSULTA where c.ID = " . $buscarConsulta . ";";
        $result = mysqli_query($myconect, $query);
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['idConsulta'] = $buscarConsulta;
            while ($row = mysqli_fetch_array($result)) {
                $_SESSION['idCliente'] = $row['idUsuario'];
            }
        } else {
            unset($_SESSION['idCliente']);
            unset($_SESSION['idConsulta']);
        }
    } else {
        unset($_SESSION['idCliente']);
        unset($_SESSION['idConsulta']);
    }
}

//Asociar ambulancia
if (isset($_POST['idConsultaParaAmbulancia'])) {
    $idConsulta = $_POST['idConsultaParaAmbulancia'];
    $query = "select id from ambulancia where ESTADO = 0 limit 1";
    $result = mysqli_query($myconect, $query);
    if (mysqli_num_rows($result) == 1) {
        while ($row = mysqli_fetch_array($result)) {
            $idAmbulancia = $row['id'];
        }
        mysqli_query($myconect, "update consulta set id_ambulancia = " . $idAmbulancia . " where id = " . $idConsulta . ";");
        mysqli_query($myconect, "update ambulancia set estado = 1 where id = " . $idAmbulancia . ";");
    } else {
        $_SESSION['errorAmbulancia'] = "No hay ambulancias para enviar";
    }
}

if (isset($_POST['idConsultaTerminarVisita'])) {
    $idAmbulancia = $_POST['idConsultaTerminarVisita'];
    $query = "update ambulancia set estado = 0 where id = " . $idAmbulancia;
    mysqli_query($myconect, $query);
}
?>



<html>
    <head>  
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" >
        <link type="text/css" href="../css/bootstrap.min.css" rel="stylesheet">  
        <script type="text/javascript" src="../js/jquery.min.js"></script> 
        <script type="text/javascript" src="../js/bootstrap.min.js"></script>
    </head>
    <body>  
        <!--Menu-->
        <header>
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header" style="margin-left: 8%">                        
                        <a class="navbar-brand " href="#">RESCUE</a>                        
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav nav-pills">
                            <li role="presentation"><a href="../Personal/Personal.php" > Registrar Consulta </a></li>                                
                            <li role="presentation"><a href="../Mantenedores/Usuario/Usuario.php" > Usuarios </a></li>                                
                            <li role="presentation"><a href="../Mantenedores/Ambulancia/mantenedorAmbulancia.php" > Ambulancias </a> </li>
                            <li role="presentation"><a href="../Mantenedores/Sintomas/Sintomas.php" > Sintomas </a> </li>
                            <li role="presentation"><a href="../Mantenedores/PrimerosAuxilios/PrimerosAuxilios.php" > Primeros Auxilios </a> </li>
                        </ul>
                        <form class="navbar-form navbar-left" method="POST" action="">
                            <div class="form-group">
                                <input type="text" name="buscarConsulta" class="form-control" placeholder="buscar consulta">
                            </div>
                            <button type="submit" class="btn btn-default">Buscar</button>
                        </form>
                        <ul class="nav navbar-nav navbar-right" style="margin-right: 8%">
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Cuenta <span class="caret"></span></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Usuario Conectado: <?php echo $_SESSION['username'] ?></a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="../logout.php" > Salir </a></li>
                                </ul>  
                            <li>
                        </ul>   
                    </div>
                </div>
                </div>
            </nav>
        </header>

        <div class="row">
            <div class="col-md-1">
            </div>
            <!--Buscar Cliente-->
            <div class="col-md-5 well center-block" >
                <article>
                    <h2> Buscar Cliente </h2>
                    <form name="buscarCliente" method="POST" action="" class="navbar-form navbar-left">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Buscar por rut" name="txtBusquedaCliente" />
                            <input type="submit" class="btn btn-default" name="enviar" value="Buscar"/>   
                        </div>                    
                    </form> 
                </article>
                <article style="margin-top: 15%">
                    <?php
                    if (isset($_POST['txtBusquedaCliente'])) {
                        $txtBusquedaCliente = $_POST['txtBusquedaCliente'];
                        $result = mysqli_query($myconect, "select 
                        u.id as ID
                        ,u.RUT as RUT
                        ,CONCAT(U.NOMBRE, ' ', U.APELLIDO) AS 'NOMBRE_COMPLETO'
                        ,U.CORREO AS CORREO
                        from usuario u 
                        left join USUARIO_ROL ur on u.ID = ur.ID_USUARIO
                        left join ROLES r on ur.ID_ROL = r.ID
                        where r.id = 2 and u.RUT like '%" . $txtBusquedaCliente . "%';");

                        ECHO "<div class='panel panel-default' style='margin-left: 0%; margin-right: 2%'>
                                <div class='panel-heading'>Resultados de busqueda</div>
                                <table class='table'>
                                <thead>
                                    <tr>
                                        <th>Rut</th>
                                        <th>Nombre Completo</th>
                                        <th>Correo</th>                                
                                        <th></th>                             
                                    </tr>
                                </thead>
                                <tbody>";
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<TR>";
                            echo "<TD>" . $row['RUT'] . "</TD>";
                            echo "<TD>" . $row['NOMBRE_COMPLETO'] . "</TD>";
                            echo "<TD>" . $row['CORREO'] . "</TD>";
                            echo "<TD>";
                            echo "<form name='SeleccionCliente' method='POST' action=''>";
                            echo "<input type='hidden' NAME='idCliente' VALUE='" . $row['ID'] . "'/>";
                            echo "<button type='submit' class='btn btn-default btn-group-xs btn-success'>
                                                        <span class='glyphicon glyphicon-ok' aria-hidden='true'></span>
                                                    </button>";
                            echo "</form>";
                            echo "</TD>";
                            echo "</TR>";
                        }
                        echo "</tbody></table></div>";
                    }
                    ?> 
                </article>
            </div>
            <!--Buscar sintomas-->
            <div class="col-md-5 well center-block">
                <article>
                    <h2> Diagnosticar </h2>
                    <form name="buscarSintoma" method="POST" action="" class="navbar-form navbar-left">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="Escriba sintomas" name="txtBusquedaSintomas" />
                            <input type="submit" class="btn btn-default" name="enviar" value="Buscar" />
                        </div> 
                    </form> 
                </article>
                <article style="margin-top: 15%">
                    <?php
                    if (isset($_SESSION['idCliente'])) {
                        if (isset($_POST['txtBusquedaSintomas'])) {
                            $txtBusquedaSintomas = $_POST['txtBusquedaSintomas'];
                            $query = "select
                                id as id
                                ,DESCRIPCION as descripcion
                                ,(case NECESARIOAMBULANCIA
                                        when 1 then 'Es necesaria'
                                        else 'No es necesaria'
                                        end) as ambulancia
                                from SINTOMA
                                where 
                                DESCRIPCION like '%" . $txtBusquedaSintomas . "%';";
                            $result2 = mysqli_query($myconect, $query);
                            if (mysqli_num_rows($result2) >= 1) {
                                ECHO "<div class='panel panel-default' style='margin-left: 0%; margin-right: 2%'>
                                    <div class='panel-heading'>Resultados de busqueda</div>
                                    <table class='table'>
                                    <thead>
                                        <tr>
                                            <th>Sintoma</th>
                                            <th>Ambulancia</th>                              
                                            <th></th>                             
                                        </tr>
                                    </thead>
                                    <tbody>";
                                while ($row2 = mysqli_fetch_array($result2)) {
                                    echo "<tr>";
                                    echo "<td>" . $row2['descripcion'] . "</td>";
                                    echo "<td>" . $row2['ambulancia'] . "</td>";
                                    echo "<td>";
                                    echo "<form name='SeleccionSintoma' method='POST' action=''>";
                                    echo "<input type='hidden' name='idSintoma' value='" . $row2['id'] . "'/>";
                                    echo "<input type='submit' class='btn btn-default btn-success' value='Seleccionar' />";
                                    echo "</form>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody>"
                                . "</table>"
                                . "</div>";
                            } else {
                                echo "No hay resultados en busqueda";
                            }
                        }
                    } else {
                        
                    }
                    ?> 
                </article>
            </div>
            <div class="col-md-1">
            </div>
        </div> 
        <!--Datos consulta-->
        <?php
        if (isset($_POST['idCliente']) || isset($_SESSION['idConsulta'])) {
            echo "<div class='row well center-block' style='margin-left: 8%;margin-right: 8%'>";
            if (isset($_POST['idCliente'])) {
                unset($_SESSION['idCliente']);
                unset($_SESSION['idConsulta']);
                $id = $_POST['idCliente'];
            } else {
                $idConsulta = $_SESSION['idConsulta'];
                $id = $_SESSION['idCliente'];
            }
            $query = "select
                        u.id as id
                        ,u.RUT as rut
                        ,u.NOMBRE as nombre
                        ,u.APELLIDO as apellido
                        ,u.CORREO as correo
                        ,u.CELULAR as celular
                        , case 
                            when u.ID_PADRE is null then 'Titular'
                            else 'Carga'
                            end as carga
                        from usuario u
                        where u.id = '" . $id . "';";
            $result = mysqli_query($myconect, $query);

            ECHO "<div class='panel panel-default' style='margin-left: 0%; margin-right: 2%'>
                                <div class='panel-heading'>Cliente Seleccionado: </div><table class='table'>
                                <thead>
                                    <tr>
                                        <th>Rut</th>
                                        <th>Nombre</th>
                                        <th>Apellido</th>
                                        <th>Correo</th>                                
                                        <th>Celular</th>                                
                                        <th>Tipo</th>
                                        <th>Numero de Consulta</th>
                                    </tr>
                                </thead>
                                <tbody>";
            while ($row = mysqli_fetch_array($result)) {
                echo "<TR>";
                echo "<TD>" . $row['rut'] . "</TD>";
                echo "<TD>" . $row['nombre'] . "</TD>";
                echo "<TD>" . $row['apellido'] . "</TD>";
                echo "<TD>" . $row['correo'] . "</TD>";
                echo "<TD>" . $row['celular'] . "</TD>";
                echo "<TD>" . $row['carga'] . "</TD>";
                echo "<TD>";
                if (isset($_SESSION['idConsulta'])) {
                    echo $_SESSION['idConsulta'];
                } else {
                    echo "<form name='generarConsulta' method='POST' action=''>";
                    echo "<input type='hidden' NAME='generarConsulta' VALUE='" . $row['id'] . "'/>";
                    echo "<INPUT TYPE='SUBMIT' class='btn btn-default btn-success' value='Generar Consulta' />";
                    echo "</form>";
                }
                echo "</TD>";
                echo "</TR>";
            }
            echo "</tbody></table></div>";

            if (isset($_SESSION['idConsulta'])) {

                $queryAmbulancia = "SELECT 
                    a.ID as id
                     ,a.MARCA as marca
                     ,a.MODELO as modelo
                     ,a.PATENTE as patente
                     ,(case a.ESTADO
                        when 0 then 'Visita realizada'
                        when 1 then 'En proceso de visita' end) as estado
                    FROM consulta c 
                    left join ambulancia a on c.ID_AMBULANCIA = a.ID 
                    WHERE c.id = " . $idConsulta . "
                    and c.ID_AMBULANCIA is not null";
                $resultAmbulancia = mysqli_query($myconect, $queryAmbulancia);
                echo "<div class='row'>";
                echo "<div class='col-md-6'>";
                if (mysqli_num_rows($resultAmbulancia) == 1) {
                    ECHO "<div class='panel panel-default' style='margin-left: 0%; margin-right: 2%'>";
                    echo "<div class='panel-heading'>Ambulancia seleccionada: </div>";
                    echo"<table class='table'>
                                <thead>
                                    <tr>
                                        <th>Marca</th>
                                        <th>Modelo</th>
                                        <th>Patente</th>
                                        <th>Estado</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>";
                    while ($row = mysqli_fetch_array($resultAmbulancia)) {
                        echo "<TR>";
                        echo "<TD>" . $row['marca'] . "</TD>";
                        echo "<TD>" . $row['modelo'] . "</TD>";
                        echo "<TD>" . $row['patente'] . "</TD>";
                        echo "<TD>" . $row['estado'] . "</TD>";
                        echo "<TD>";
                        echo "<form name='Terminar Visita' method='POST' action=''>";
                        echo "<input type='hidden' NAME='idConsultaTerminarVisita' VALUE='" . $row['id'] . "'/>";
                        echo "<INPUT TYPE='SUBMIT' class='btn btn-default btn-success' value='Terminar Visita' />";
                        echo "</form>";
                        echo "</TD>";
                        echo "</TR>";
                    }
                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                }
                echo "</div>";

                $idConsulta = $_SESSION['idConsulta'];
                $query = "select 
                            cs.ID as id
                            ,s.id as idSintoma
                            ,s.DESCRIPCION as descripcion
                            ,(case s.NECESARIOAMBULANCIA
                            when 1 then 'Es necesaria'
                            else 'No es necesaria'
                            end) as ambulancia
                        from CONSULTA_SINTOMA cs
                            left join consulta c on cs.ID_CONSULTA = c.ID
                            left join sintoma s on cs.ID_SINTOMA = s.ID
                        where c.ID = " . $idConsulta . ";";
                $result = mysqli_query($myconect, $query);
                echo "<div class='col-md-6'>";
                echo "<div class='panel panel-default' style='margin-left: 0%; margin-right: 2%'>
                        <div class='panel-heading'>Sintomas: </div>";
                echo "<table class='table'>;
                            <thead>
                                <tr>
                                    <th>Sintoma</th>
                                    <th>Ambulancia</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>";
                while ($row = mysqli_fetch_array($result)) {
                    echo "<TR>";
                    echo "<TD>" . $row['descripcion'] . "</TD>";
                    echo "<TD>" . $row['ambulancia'] . "</TD>";
                    echo "<TD>";
                    echo "<form name='eliminarSintoma' method='POST' action=''>";
                    echo "<input type='hidden' NAME='idConsultaAEliminar' VALUE='" . $row['id'] . "'/>";
                    echo "<input type='hidden' NAME='idSintomaAEliminar' VALUE='" . $row['idSintoma'] . "'/>";
                    echo "<INPUT TYPE='SUBMIT' class='btn btn-default btn-danger' value='Eliminar' />";
                    echo "</form>";
                    echo "</TD>";
                    echo "</TR>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
            if (isset($_SESSION['idConsulta'])) {
                echo "<input type='button' class='btn btn-default navbar-right btn-success' data-toggle='modal' data-target='#Resultados' style='margin-right: 2%' value='Solicitar primeros auxilios' />";
                echo "<br>";
                echo "<br>";
                echo "<form name='eliminarSintoma' method='POST' action=''>";
                echo "</form>";
            }

            if (isset($_SESSION['errorAmbulancia'])) {
                echo "<div class='alert alert-warning' role='alert'>" . $_SESSION['errorAmbulancia'] . "</div>";
                unset($_SESSION['errorAmbulancia']);
            }
            echo "</div>";
        }
        ?>  

        <!--Modal de resultados-->
        <div class="modal fade" id="Resultados" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel">Recomendaciones</h4>
                    </div>
                    <div class="modal-body">
                        <p align="justify">
                            Estimado <?php echo $_SESSION['username'] ?>,
                            <br> Segun los sintomas entregados se pudo determinar
                            que ustede padece una extraña enfermadad, para la cual
                            le sugerimos lo siguiente:                            
                        </p>
                        <?php
                        $query = "select 
                                    distinct pa.DESCRIPCION as primerauxilio
                                    from CONSULTA c
                                    left join CONSULTA_SINTOMA cs on cs.ID_CONSULTA = c.ID
                                    left join SINTOMA s on cs.ID_SINTOMA = s.ID
                                    left join SINTOMA_PRIMEROSAUXILIOS spa on spa.ID_SINTOMA = s.ID
                                    left join PRIMEROSAUXILIOS pa on spa.ID_PRIMEROSAUXILIOS = pa.ID
                                    where c.ID = " . $idConsulta . ";";
                        $result = mysqli_query($myconect, $query);
                        echo "<pre>";
                        while ($row = mysqli_fetch_array($result)) {
                            echo $row['primerauxilio'];
                            echo "<br>";
                        }
                        echo "</pre>";
                        $query = "select 
                                    *
                                    from CONSULTA c
                                    left join CONSULTA_SINTOMA cs on cs.ID_CONSULTA = c.ID
                                    left join SINTOMA s on cs.ID_SINTOMA = s.ID
                                    where c.ID = " . $idConsulta . " and s.NECESARIOAMBULANCIA = 1";
                        $result = mysqli_query($myconect, $query);
                        if (mysqli_num_rows($result) >= 1) {
                            echo "<p align='justify'>Segun los sintamos entregados, 
                                se sugiere coordinar una visita de nuestros paramedicos
                                </p>";
                        } else {
                            echo "<p align='justify'>Segun los sintamos entregados, 
                                se sugiere no coordinar una visita de nuestros paramedicos
                                </p>";
                        }
                        ?>    
                    </div>
                    <form method="POST" name="Solicitarambulancia" action="">
                        <input type="hidden" name="idConsultaParaAmbulancia" value="<?php echo $idConsulta; ?>"/>
                        <div class="modal-footer">
                            <input type="submit" class="btn btn-primary" value="Enviar ambulancia"/>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </body>       
</html>

