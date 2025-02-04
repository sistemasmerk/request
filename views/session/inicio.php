<div class="row">
    <div class="columna col-lg-7">
        <div class=" widget area-juego" id="area-juego">
            <h3 class="titulo">Area de juego</h3>
            <?php echo $directivas->obten_mesas(); ?>
        </div>
        
        <div class="widget" >
            <h3 class="titulo d-flex justify-content-between"><span>Ventas</span> <span id="total_venta"></span> </h3> 
            <div id="area-ventas" class="contenedor d-flex flex-wrap justify-content-center" draggable="true" ondragstart="drag(event)">
                
            </div>
            <div id="area-comentarios" class="contenedor d-flex flex-wrap justify-content-center" >
                <input type="text" class="form-control mt-2 mb-2" name="comentarios" id="comentarios" placeholder="Comentrios (opcional)" style="display:none">
            </div>
            <div id="area-tiempos" class="contenedor d-flex flex-wrap justify-content-center">
                <?php echo $directivas->obten_tiempo_nocargado(); ?>
            </div>
            <div class="d-flex justify-content-start">
                <button id="nueva_cuenta" class="btn btn-secondary">Nueva cuenta</button>
                <button id="producto_pagado" class="btn btn-info">Pagado</button>                
            </div>
            <div class="cuenta_nueva_form d-flex justify-content-end">
                
            </div>
        </div>
        
    </div>
    <div class="columna col-lg-5">
        <div class=" widget productos">
            <h3 class="titulo">Productos</h3>
            <div class="contenedor d-flex flex-wrap justify-content-center area_productos">
                <?php
                    echo $directivas->obten_productos();
                ?>                
            </div>
        </div>
    </div>

</div>
<div class="row">
    <div class="columna col-lg-12">
        <div class="widget cuentas">
            <h3 class="titulo">Cuentas por cobrar</h3>
            <table class="table table-sm table-striped table-hover">
            <thead class="thead-dark">
                <tr class="encabezado">
                    <th class="boton" scope="col"></th>
                    <th class="nombre_consumo" scope="col">Nombre</th>
                    <th class="consumo" scope="col"></th>
                    <th class="actualizar" scope="col"></th>
                    <!--<th scope="col">Last</th>
                    <th scope="col">Handle</th>-->
              </tr>
            </thead>
            <tbody id="filas_cuentas">
                
              <?php
                    echo $directivas->obten_cuentas(); 
                ?>
              
            </tbody>
          </table>
        </div>
    </div>
</div>
                <?php
                    echo $directivas->crea_modal_cuenta();
                ?>



<script language="javascript" type="text/javascript">
    $(document).ready(function (){
        $('.mesaaaa').on("click", function() {
            $('#area-juego').load('./views/mesas.php');
            
            //alert("siiiiiiiiiiiii"  );
            
        });
    });
    
    
</script>    