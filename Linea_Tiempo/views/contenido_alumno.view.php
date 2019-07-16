<!DOCTYPE html>
<html lang="en">
    <head> 
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Linea del tiempo</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
        <script src="js/jquery.min.js"></script>
        <script src="js/moment.min.js"></script>
        <!-- FullCalendar-->
        <link rel="stylesheet" href="css/fullcalendar.min.css">
        <script src="js/fullcalendar.min.js"></script>
        <script src="js/es.js"></script>
        <!--Clockpicker-->
        <script src="js/bootstrap-clockpicker.js"></script>
        <link rel="stylesheet" href="css/bootstrap-clockpicker.css">
        <!--bootstrap-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

        <style>
            .fc th{
                padding: 10px 0px;
                vertical-align: middle;
                background: #F2F2F2;
            }
        </style>
        
    </head>
    <body>


        <header class="encabezado">
            <div class=configurar-usu>
                <a href="configurar_maestro">Mis datos</a>
            </div>

            <div class=btn-cerrar>
                <a href="cerrar.php">Cerrar Sesion</a>
            </div>
        </header>

        <div class="contain">
            <div class="row">
                <div class="col"></div>
                <div class="col-7">
                    <div id="calendario_curso"></div>
                </div>
                <div class="col"></div>
            </div>
        </div>
        <script>
            $(document).ready(function(){
                $('#calendario_curso').fullCalendar({
                    header:{
                        left: 'today, prev,next',
                        center: 'title',
                        right: 'month, agendaWeek, agendaDay'
                    },
                    customButtons:{
                        miBoton:{
                            text: "Botón 1",
                            click:function(){
                                alert("Accion del boton");
                            }
                        }
                    },
                    dayClick:function(date, jsEvent, view){
                        $('#btnAgregar').prop("disabled", false);
                        $('#btnModificar').prop("disabled", true);
                        $('#btnEliminar').prop("disabled", true);

                        limpiarFormulario();
                        $('#txtFecha').val(date.format());
                        $("#ModalEventos").modal();
                    },
                    events:'http://localhost/calendario_curso/eventos.php',
                        
                    eventClick:function(calEvent, jsEvent, view){

                        $('#btnAgregar').prop("disabled", true);
                        $('#btnModificar').prop("disabled", false);
                        $('#btnEliminar').prop("disabled", false);

                        //H2 
                        $('#tituloEvento').html(calEvent.title);

                        // Mostrar la informacion del evento en los inputs
                        $('#txtCategoria').val(calEvent.categoria);
                        $('#txtDescripcion').val(calEvent.descripcion);
                        $('#txtID').val(calEvent.id);
                        $('#txtTitle').val(calEvent.title);
                        $('#txtColor').val(calEvent.color);

                        FechaHora = calEvent.start._i.split(" ");
                        $('#txtFecha').val(FechaHora[0]);
                        $('#txtHora').val(FechaHora[1]);


                        $('#ModalEventos').modal();
                    },

                    editable: false,
                    eventDrop: function(calEvent){
                        $('#txtID').val(calEvent.id);
                        $('#txtTitulo').val(calEvent.title);
                        $('#txtColor').val(calEvent.color);
                        $('#txtDescription').val(calEvent.description);

                        var fechaHora = calEvent.start.format().split("T");
                        $('txtFecha').val(FechaHora[0]);
                        $('txtHora').val(FechaHora[1]);

                        RecolectarDatosGUI();
                        EnviarInformacion('modificar', NuevoEvento);
                    }

                });
            });
        </script>

            <!-- Modal (Agregar, modificar, eliminar) -->
        <div class="modal fade" id="ModalEventos" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tituloEvento"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                    <input type="hidden" id="txtID" name="txtID">
                    <input type="hidden" id="txtFecha" name="txtFecha" />
                    <div class="form-row">
                        <div class="form-group col-md-8">
                            <label>Categoría:</label>
                            <input type="text" id="txtCategoria" class="form-control" placeholder="Título del evento">
                        </div>
                        <div class="form-group col-md-4">
                            <label>Hora del evento:</label>
                            <input type="text" id="txtHora" value="10:30" class="form-control">
                        </div>
                    </div>

                    <!-- Nombre del evento  -->
                    <div class="form-group">
                        <label>Nombre del evento:</label>
                        <input type="text" id="txtTitle" class="form-control"></input>
                    </div>

                    <div class="form-group">
                        <label>Descripción:</label>
                        <textarea id="txtDescripcion" rows="3" class="form-control"></textarea>
                    </div>

                </div>
                <!-- Botones -->
                <div class="modal-footer">
                    <a href="rubrica_individual.php" id="btnCalificar" class=" btn btn-sucess">Ver Rubrica</a>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </div>
            </div>
        </div>

        <script>

            var NuevoEvento;
            // Boton Agregar
            $('#btnAgregar').click(function(){
                RecolectarDatosGUI();
                EnviarInformacion('agregar', NuevoEvento);
            });

            //Boton Eliminar
            $('#btnEliminar').click(function(){
                RecolectarDatosGUI();
                EnviarInformacion('eliminar', NuevoEvento);
            });

            //Boton Modificar
            $('#btnModificar').click(function(){
                RecolectarDatosGUI();
                EnviarInformacion('modificar', NuevoEvento);
            });

            function RecolectarDatosGUI () {
                NuevoEvento = {
                    id:$('#txtID').val(),
                    categoria:$('#txtCategoria').val(),
                    title:$('#txtTitle').val(),
                    start:$('#txtFecha').val()+" "+$('#txtHora').val(),
                    color:$('#txtColor').val(),
                    descripcion:$('#txtDescripcion').val(),
                    textColor:"#FFFFFF",
                    end:$('#txtFecha').val()+" "+$('#txtHora').val(),
                };
            }
            function EnviarInformacion(accion, objEvento, modal){
                $.ajax({
                    type:'POST',
                    url:'eventos.php?accion='+accion,
                    data:objEvento,
                    success:function(msg){
                        if(msg){
                            $('#calendario_curso').fullCalendar('refetchEvents');
                            if(!modal){
                                $("#ModalEventos").modal('toggle');
                            }
                        }
                    },
                    error:function(){
                        alert("Hay un error...");
                    }
                });
            }
            $('.clockpicker').clockpicker();

            function limpiarFormulario(){
                $('#txtID').val('');
                $('#txtCategoria').val('');
                $('#txtTitle').val('');
                $('#txtColor').val('');
                $('#txtDescripcion').val('');
            }
        </script>       
    </body>
</html> 