
var inicios = [];

//var timeout = this.timeout1;
/*timeout["timeout1"] = 0;
timeout["timeout2"] = 0;
timeout["timeout3"] = 0;
timeout["timeout4"] = 0;
timeout["timeout5"] = 0;
timeout["timeout6"] = 0;
*/


function inicia_reloj(id){
      
  //var id = elemento.substring(5);
//alert(id);      
  //if( this.timeout["timeout"+id] == 0){
    this.inicios["inicio"+id] = new Date().getTime();   
    localStorage.setItem("inicio"+id, this.inicios["inicio"+id]);
    corre_reloj(id);
  /*}else{
    clearTimeout(this.timeout["timeout"+id]);
    localStorage.removeItem("inicio"+id);
    this.timeout["timeout"+id] = 0;    
  }*/
}

function detiene_reloj(id){
    clearTimeout(this.timeout["timeout"+id]);
    localStorage.removeItem("inicio"+id);
    this.timeout["timeout"+id] = 0;
}

function corre_reloj(id){
  var actual = new Date().getTime();
  
  var diferencia = new Date(actual - this.inicios["inicio"+id]);
  
  var resultado = LeadingZero(diferencia.getUTCHours()) + ":" +  LeadingZero(diferencia.getUTCMinutes()) + ":" + LeadingZero(diferencia.getUTCSeconds());

  $("#reloj_" + id).text(resultado);
    
  this.timeout["timeout"+id] = setTimeout("corre_reloj("+id+")", 1000);
}

function LeadingZero(Time){
  return (Time < 10) ? "0" + Time : + Time;
}

function obten_mesas(){
  //var mesas = $(".mesa").toArray().length;
  
  var contador = 0;
  $(".mesa").each(function(){
      contador++; 
      timeout["timeout"+contador] = 0;
  });
  
}

function recarga_pagina(tiempo){//7200000
  setInterval(function(){ 
    location. reload();
  }, tiempo);//7200000
}

window.onload=function()
    {
      timeout = [];            
      obten_mesas();
      pagado = "";
      contador_inicio = 1;
      contador_ventas = 0;
      
      recarga_pagina(7200000);
      
      for(var i = 1; i <= 6; i++){
        if(localStorage.getItem("inicio"+i)!=null){
            this.inicios["inicio"+i] = localStorage.getItem("inicio"+i);
            corre_reloj(i);
            
            $("#"+"mesa_"+i).removeClass('bg-secondary');
            $("#"+"mesa_"+i).addClass('bg-info');
        }
      }
    }

function llama_accion_php(id, funcion, dato1='', dato2='', dato3='', dato4=''){
    
  $.ajax({
      type: 'POST',
      url: "./index_ajax.php?seccion=session&accion=registra_tiempo",
      data:{mesa: id, funcion: funcion, dato1: dato1, dato2: dato2, dato3: dato3, dato4: dato4},
      cache: false,
      success: function(data){
        //var ok = JSON.parse(data);
        //alert(id);
        if(funcion == "actualiza_modal"){
            var resp = JSON.parse(data);
            
            $('#inicio_'+id).text(resp.inicio);
            $('#fin_'+id).text(resp.fin);
            $('#tiempo_'+id).text(resp.tiempo);
            $('#costo_'+id).text(resp.costo);
           //alert( resp.costo); 
        }else if( funcion == "inicio"){
            var resp = JSON.parse(data);
            if(resp.status == 1){
                $("#div_mesa_"+id).attr('data-target', '#modal_'+id);
                $("#mesa_"+id).removeClass('bg-secondary');
                $("#mesa_"+id).addClass('bg-info');
                $("#mesa_"+id).attr('activo', 1);
                inicia_reloj(id);
                funcion_alert_exito('Exito', 'Accion realizada exitosamente', 'blue');
            }else{
                funcion_alert_error('Error', 'Accion con error, actualiza la pagina e intentalo de nuevo');
            }
            
        }else if(funcion == 'fin'){
            var resp = JSON.parse(data);
    //alert('--------------> ' + resp.status)        
            if(resp.status == 1){
                detiene_reloj(id);
                $('#reloj_'+id).empty();
                $("#mesa_"+id).removeClass('bg-info');
                $("#mesa_"+id).addClass('bg-secondary');
                $("#mesa_"+id).attr('activo', 0);
                $("#modal_"+id).modal('hide');
                $("#div_mesa_"+id).attr('data-target', '');
                funcion_alert_exito('Exito', 'Accion realizada exitosamente', 'blue');
            }else{
                funcion_alert_error('Error', 'Accion con error, actualiza la pagina e intentalo de nuevo');
            }
            
        }
        else{
            /*$("#"+mesa).removeClass('bg-info');
            $("#"+mesa).addClass('bg-secondary');
            $("#"+mesa).attr('activo', 0);
            */
        }
        
        //$('.titulo').text(data);
      },
      error: function(xhr, status){
        alert("ATENCION: No se registro el tiempo correctamente.");
      }
    });
}

function funcion_alert_exito(titulo, contenido, tipo){
    $.confirm({
        title: titulo,
        content: contenido,
        type: tipo,
        icon: 'icon-users',
        autoClose: 'ok|1000',
        buttons: {
            ok: {
                btnClass: 'btn btn-info',
                text: 'ok',
                action: function(){
                    
                }
            },             
        }
    });
}

function funcion_alert_error(titulo, contenido){
    $.confirm({
        title: titulo,
        content: contenido,
        type: 'red',
        icon: 'icon-users',
        buttons: {
            ok: {
                btnClass: 'btn btn-red',
                text: 'ok',
                action: function(){
                    
                }
            },             
        }
    });
}

function funcion_alert_pregunta(titulo, contenido, tipo, funcion, id, datos='', datos2=''){
    $.confirm({
        title: titulo,
        content: contenido,
        type: tipo,
        icon: 'icon-users',
        buttons: {
            si: {
                btnClass: 'btn btn-info btn-lg',
                text: '.si.',
                action: function(){
                    jquery_php(datos, datos2, funcion, id);
                }
            },
            no: function () {
                
            },                    
        }
    });
}

function jquery_php(texto, inicio, funcion, id="", dato1=""){
    $.ajax({
      type: 'POST',
      url: "./index_ajax.php?seccion=session&accion=jquery_php",
      data:{texto: texto, inicio: inicio, funcion: funcion, id: id, dato1: dato1},
      cache: false,
      success: function(data){
            //var resp = JSON.parse(data);
            //alert(resp.id);
            if( funcion == "paga_jugador"){
                var resp = JSON.parse(data);
                if(resp.status == 1){
                  $("#tiempos-baraja #"+id).remove();
                  if(resp.status_mesa == 0){
                        var modal_id = $('.show').attr('id');
                        var indice = modal_id.indexOf("_"); 
                        var mesa_id = modal_id.substring(indice + 1);
                        
                        $('.show').modal('hide');
                        $("#"+"mesa_"+mesa_id).removeClass('bg-info');
                        $("#"+"mesa_"+mesa_id).addClass('bg-secondary');
                        detiene_reloj(mesa_id);
                        $('#reloj_'+mesa_id).text('');
                        //detener el reloj
                    }
                }
            }else if(funcion == "carga_ventas"){
                var resp = JSON.parse(data);
                if(resp.status != 'ok'){
                    //ya no hay inventarios para completar la venta
                    //funcion_alert_error(resp.status); 
                    var indice = resp.status.indexOf("_"); 
                    var producto = resp.status.substring(indice + 1); //alert(producto);
                    //$('#producto_'+producto).remove();
                    [].forEach.call(document.querySelectorAll("#producto_"+producto), function(regla){
                        regla.parentNode.removeChild(regla);
                    });
                    funcion_alert_error('Error', 'No hay productos suficientes en el inventario, faltaron de registrar: '+resp.status);
                    
                }else{   
                    $("#comentarios").val('');
                    $("#comentarios").css('display', 'none');
                    carga_productos(resp.id);
                    funcion_alert_exito('Carga exitosa', 'Registro cargado con exito', 'blue');                    
                }
            }else if(funcion == 'actualiza_consumos'){
                var resp = JSON.parse(data);
                var html = resp.html;
                actualiza_consumos(id, html);
            }else if(funcion == 'actualiza_ventas'){
                var resp = JSON.parse(data);
                var html = resp.html;
                actualiza_ventas(id, texto, html, resp.total);
            }else if(funcion == 'pagar_cuenta'){
                var resp = JSON.parse(data);
                if(resp.status == 'elimina_fila'){
                    funcion_alert_exito('Operacion exitosa', 'Cuenta pagada con exito', 'blue');
                    eliminar_fila(id);
                }else if(resp.status == 'actualiza_fila'){
                    funcion_alert_exito('Operacion exitosa', 'Cuenta pagada con exito', 'blue');
                    jquery_php('', '', "actualiza_consumos", id);
                }
                else{
                    funcion_alert_error('Error', 'La cuenta no se pago satisfactoriamente');
                }
                $('#cuenta_modal').modal('hide');
            }else if(funcion == 'nueva_cuenta'){//---------------------------------------------------------------------------------------------------
                var resp = JSON.parse(data);                
                if(resp.status == 1){
                    if($('#area-ventas .producto_venta').length > 0){//alert('siiiiiii carga ventaaaaaaaaa');
                        carga_venta(resp.id);
                    }else if($('#area-tiempos .tiempo_venta').length > 0){//alert('siiiiiii carga tiempooooooo');
                        carga_tiempo_mesa(resp.id, texto);
                    }
                    
                    $('#filas_cuentas').append(resp.html);
                }else{                    
                    funcion_alert_error('Error', resp.status);
                }
            }else if(funcion == 'carga_tiempo'){
                var resp = JSON.parse(data);
                if(resp.status == 1){
                    $('#tiempos-baraja #'+resp.id).remove();
                    funcion_alert_exito('Operacion exitosa', 'Tiempo cargado con exito', 'blue');
                    //actualiza cuenta en deudor
    
                    if(resp.status_mesa == 0){
                        var modal_id = $('.show').attr('id');
                        var indice = modal_id.indexOf("_"); 
                        var mesa_id = modal_id.substring(indice + 1);
    //alert(modal_id + ' ' + indice + ' ' + mesa_id + ' ' + id);                    
                        $('.show').modal('hide');
                        $("#"+"mesa_"+mesa_id).removeClass('bg-info');
                        $("#"+"mesa_"+mesa_id).addClass('bg-secondary');
                        detiene_reloj(mesa_id);
                        $('#reloj_'+mesa_id).text('');
                        //detener el reloj ------------------------------------------------------------------ atencion
                    }
                  
                    if(resp.tipo == 'n'){
                        $('#filas_cuentas').append(resp.html);
                    }else{
                        $('#filas_cuentas #'+resp.cuenta).find('.consumos').empty();
                        $('#filas_cuentas #'+resp.cuenta).find('.consumos').html(resp.html);
                    }
                }else{
                    funcion_alert_error('Error', 'El tiempo no se cargó satisfactoriamente');
                }
            }else if(funcion == 'actualiza_jugador_baraja'){
                var resp = JSON.parse(data);
                $('#tiempos-baraja').empty();
                $('#tiempos-baraja').html(resp.html);
                
                if(resp.html != ''){
                    if($('#reloj_'+id).text() == ''){
                        inicia_reloj(id);
                        $("#mesa_"+id).removeClass('bg-secondary');
                        $("#mesa_"+id).addClass('bg-info');
                        $("#mesa_"+id).attr('activo', 1);
                    }
                }/*else{
                    detiene_reloj(id);
                    $('#reloj_'+id).empty();
                    $("#mesa_"+id).removeClass('bg-info');
                    $("#mesa_"+id).addClass('bg-secondary');
                    $("#mesa_"+id).attr('activo', 0);
                }*/
            }else if(funcion == 'actualiza_pago_mesa'){
                var resp = JSON.parse(data);
                $('#'+resp.id).html(resp.html);
            }else if(funcion == 'carga_tiempo_mesa'){
                var resp = JSON.parse(data);
                if(resp.status == 1){
                    funcion_alert_exito('Operacion exitosa', 'Tiempo cargado con exito', 'blue');
                    $('.tiempo_venta:first').remove();
                    
                    if($('#area-tiempos').length == 0){
                        $('#area-tiempos .tiempo_venta').empty();
                    }

                    if(resp.tipo == 'n'){
                        $('#filas_cuentas').append(resp.html);
                    }else{
                        $('#filas_cuentas #'+resp.cuenta).find('.consumos').empty();
                        $('#filas_cuentas #'+resp.cuenta).find('.consumos').html(resp.html);
                    }
                }else{
                    funcion_alert_error('Error', 'El tiempo no se cargó satisfactoriamente');
                }
            }
              
      },
      error: function(xhr, status){
        console.log(texto + "   " + status + "   " + xhr);
        alert("ATENCION: No se registro correctamente. " + texto);
      }
      });
}

function status_mesa(id, mesa, seccion, accion){
    $.ajax({
      type: 'POST',
      dataType: 'text',
      url: "./index_ajax.php?seccion="+seccion+"&accion="+accion,
      data:{mesa: id, funcion: "status_mesa"},
      cache: false,
      success: function(data){                                            //console.log(mesa);
        var resp = JSON.parse(data);
        // resp.status == 0 (mesa jugando) resp.status == 1 (mesa inactiva)
        //alert('****************** ' + resp.status);
        if(resp.status == 0){
          
          //llama_accion_php("session", "registra_tiempo", id, "actualiza_modal");
          
          /*$("#pagado_"+id).on("click", function(){              
              llama_accion_php("session", "registra_tiempo", id, "fin");
              return false;
            });*/
        }else{
          llama_accion_php(id, "inicio");          
        }
      },
      error: function(xhr, status){
        //alert("ATENCION: No se registro el tiempo correctamente.");
      }
    });
}

function botones_baraja(){
  var html = "<td></a> </td>";
  html += "<td> </a> </td>";
  return html;
}

function nuevo_jugador(){
      var fecha = new Date(Date.now());  
  
      var mes = (((fecha.getMonth()+1) < 10) ? "0" : "" ) + (fecha.getMonth() + 1);
      var dia = ((fecha.getDate() < 10) ? "0" : "" ) + fecha.getDate();     
      var hora = ((fecha.getHours() < 10) ? "0" : "" ) + fecha.getHours();
      var minuto = ((fecha.getMinutes() < 10) ? "0" : "" ) + fecha.getMinutes();
      var segundo = ((fecha.getSeconds() < 10) ? "0" : "" ) + fecha.getSeconds();
      var inicio = hora + ":" + minuto;

      var fecha_inicio  = fecha.getFullYear() + "-" + mes + "-" + dia + " " + hora + ":" + minuto + ":" + segundo;
      var html = '<tr class="nuevo"> <td><input type="text" class="form-control form-control-sm nombre" placeholder="Nombre" inicio="'+fecha_inicio+'"></td> <td>'+inicio+'</td> <td></td> <th scope="row"></th>';   
      html += "<td></a> </td><td></a> </td>";

      contador_inicio ++;
      return html;
}

function calcula_pago(inicio, precio){
    var actual = new Date(Date.now());
    var ini = new Date(inicio);
    var diferencia = Math.abs(actual - ini);
    var minutos_total = Math.ceil((diferencia / 1000) / 60);
    var costo = Math.ceil((precio / 60) * minutos_total);
    var dias = Math.floor(diferencia/86400000);
    var horas_temp = Math.floor((24 * dias) + (diferencia % 86400000)/3600000);
    var minutos_temp = Math.round(((diferencia % 86400000) % 3600000)/60000);
    var horas = ((horas_temp < 10) ? "0" : "" ) + horas_temp;
    var minutos = ((minutos_temp < 10) ? "0" : "" ) + minutos_temp + ":00";
    
    var tiempo_precio = {"tiempo": horas + ":" + minutos, "costo": costo};
    return tiempo_precio;
    //alert(horas + ":" + minutos );
}

function allowDrop(ev) {
    //alert("2 $$$$$$$$$$$$ allow " + ev.preventDefault());
  ev.preventDefault();
}
function drag(ev) {
    //alert("1 +++++++ drag " + ev.target.id);
  ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    //alert(dragover(ev));
    //alert("3 ---------> drop");
  //ev.preventDefault();
  
  //var data = ev.dataTransfer.getData("text");
  //ev.target.appendChild(document.getElementById(data));
}

function carga_venta(id){
    var productos = new Array();
    var contador = 1; 
    
    $(".producto_venta").each(function(){
        var id = $(this).attr('id');

        if(id == 'producto_13'){
            var padre = $(this).closest('.area_dinero');
            var cantidad = $(padre).find('.dinero').val();
            
            productos.push(id+'-dinero'+contador);
            productos[id+'-dinero'+contador] = cantidad;
            contador++;
        }else{
            if(productos[id]){
                productos[id] = productos[id] + 1;
            }else{
                productos.push(id);
                productos[id] = 1;
            }
        }

        
    });
    
    var comentarios = $('#comentarios').val();
    
    productos.forEach(function(item){
        var indice = item.indexOf("_"); 
        var producto = item.substring(indice + 1);  //alert(producto);
        var cantidad = productos[item];
       //alert( item + " -> " + productos[item]); 
       jquery_php(producto, cantidad, 'carga_ventas', id, comentarios);
    });
}

function carga_tiempo_mesa(id, nombre){
    
    if($('#area-tiempos .tiempo_venta').length > 0){
        var tiempo = $('.tiempo_venta:first').attr('tiempo_id');
    //alert('jjjjjjjjjjjjj ' + tiempo + '   ' + nombre + '   ' + id);    
        jquery_php(tiempo, nombre, 'carga_tiempo_mesa', id);
    }
    //alert(id);
}

function carga_productos(id){
    var productos = '';
    var imagen = '';
    $("#area-ventas").empty();
    var nombre = $("#nombre").val('');
    //var comentario = $("#comentarios").val('');
    $('.cuenta_nueva_form').empty();
    
    $("#total_venta").text('');
    contador_ventas = 0;
    jquery_php('', '', "actualiza_consumos", id);
}

function actualiza_consumos(id, html){
    $('#'+id).find('.consumos').empty();
    $('#'+id).find('.consumos').html(html);
}

function actualiza_ventas(id, nombre, html, total){
    $('#cuenta_ventas').empty();
    $('#cuenta_ventas').html(html);
    $('#nombre_cuenta').text(nombre);
    $('#total').text(total);
    $('#cuenta_id').val(id);
}

function eliminar_fila(id){
    $('#'+id).remove();    
}

function activa_desactiva(elemento, accion){
    var etiqueta_alert = '';
    var accion_url = '';
    var etiqueta_nuevo_status = '';
    var quita_clase = '';
    var inserta_clase = '';
    var icono = '';
    if(accion == 'desactivar'){
        etiqueta_alert = 'desactivar';
        accion_url = 'desactiva_bd';
        etiqueta_nuevo_status = 'Inactivo';
        quita_clase = 'desactiva';
        inserta_clase = 'activa';
        //tipo_panel_anterior = 'panel-info';
        icono = 'icon-ok';
    }
    else{
        if(accion == 'activar'){
            etiqueta_alert = 'activar';
            accion_url = 'activa_bd';
            etiqueta_nuevo_status = 'Activo';
            quita_clase = 'activa';
            inserta_clase = 'desactiva';
            icono = 'icon-window-minimize';
        }
    }
 //var ok = $(elemento).closest('.icono_accion').removeClass(quita_clase).addClass(inserta_clase); 
    var registro_id = $(elemento).closest('.registro_id').attr("id");
    var url = new URL(window.location.href);
    var seccion = url.searchParams.get("seccion");
    var result = confirm("Estás seguro de "+etiqueta_alert+" el registro??");
    var url_ejecucion = "./index_ajax.php?seccion="+seccion+"&accion="+accion_url+"&registro_id="+registro_id;
    if(result == true){
        $.ajax({
            url: url_ejecucion,
            type: "POST",
            data: {},
            success: function() {
                $(elemento).empty();
                $(elemento).append('<i class="'+icono+' icono_boton"></i>');
                $('#'+registro_id).find('.status').text(etiqueta_nuevo_status);
                $(elemento).closest('.icono_accion').removeClass(quita_clase).addClass(inserta_clase);
                
                elemento.unbind('click');
                if(accion == 'activar'){
                    alert('Registro activado con éxito');
                    elemento.click(function () {
                        activa_desactiva(elemento,'desactivar');
                    });
                }
                else{
                    if(accion=='desactivar'){
                        alert('Registro desactivado con éxito');
                        elemento.click(function () {
                            activa_desactiva(elemento,'activar');
                        });
                    }
                }
            },
            error: function() {
                alert('Error: '+ url_ejecucion);
            }
        });
    }

}

$(document).ready(function (){

  $('.mesa').on("click", function() {
    var mesa = $(this).attr("id");
    
    var indice = mesa.indexOf("_");
    
    var id = mesa.substring(indice + 1);
    
    //alert(mesa + ' ' + id);
    status_mesa(id, mesa, 'session', 'registra_tiempo');
    
   
  });
  
  $('.calcular').on('click', function(){
    //
    var entre = parseFloat($(".dividir").val());    
    var show = $(".show").attr("id"); 
    var indice = show.indexOf("_");
    var id = show.substr(indice + 1);
    var total = parseFloat($(".costo_"+id).text());
    
    var parcialidades = Math.ceil( total / entre );
    
    $(".persona").text(parcialidades);
  });
  
  $("#agregar").on("click", function(){
      $("#tiempos-baraja").append(nuevo_jugador());
  });
  
  $("#guardar_baraja").on("click", function(){
      var padre = $(this).closest(".modal").attr("id");
      var indice = padre.indexOf("_");
      var id = padre.substr(indice + 1);
      var indicador = 0;
    
      $(".nombre").each(function(){
                
        if( $(this).val() == ""){
            alert("Atencion: favor de indicar el nombre del jugador.");
            indicador = 0;
            return false;             
        }else{
          indicador ++;
          
          jquery_php($(this).val(), $(this).attr("inicio"), "guarda_baraja", id);
          $(this).replaceWith("<span>" + $(this).val() + "</span>");
        }
      });
    
        setTimeout(jquery_php('', '', 'actualiza_jugador_baraja', id), 3000);
      
      
  });
  
  $(".modal_mesas").on('show.bs.modal', function () { // se ejecuta cuando la modal se cierra            
      var modal = $(this).attr('id');
      var indice = modal.indexOf('_');
      var id = modal.substring(indice+1);
      
      jquery_php('', '', "actualiza_pago_mesa", id);
      //
      //alert(id);
  });
  
  
  $("#eliminar").on("click", function(){
      $(".nuevo:last").remove();
  });
  
  //$('.fila').on('click', function(){
  $('#tiempos-baraja').on('click', '.fila', function(){
      var inicio = $(this).attr("inicio");   
      var precio = $(this).attr("precio");
    
      var resultado = calcula_pago(inicio, precio);
      $(this).find('.tiempo').text(resultado.tiempo);
      $(this).find('.costo_baraja').text(resultado.costo);
      
      //alert(resultado.tiempo + " " + resultado.costo);
  });
  
  $('#tiempos-baraja').on('click', '.fila .tiempo_pagado', function(){
        var id = $(this).closest("tr").attr("id");
        
        var tiempo = $("#"+id).find(".tiempo").text();
        var costo = $("#"+id).find(".costo_baraja").text();
        var jugador = $("#"+id).find(".jugador").text();
                            
        if(tiempo == "" && costo == ""){

        }else{
            $.confirm({
                title: 'El jugador: ' + jugador,
                content: '¿Pago el tiempo?',
                type: 'blue',
                icon: 'icon-users',
                buttons: {
                            
                    si: {
                        btnClass: 'btn btn-info',
                        text: 'si',
                        action: function(){
                            jquery_php(tiempo, costo, "paga_jugador", id);
                        }
                    },
                    no: function () {
                        //$.alert('Canceled!');
                    },                    
                }
            });
            
        }

    });
  
    $('#tiempos-baraja').on('click', '.fila .carga_tiempo', function(){
        var id = $(this).closest("tr").attr("id");
        
        var tiempo = $("#"+id).find(".tiempo").text();
        var costo = $("#"+id).find(".costo_baraja").text();
        var jugador = $("#"+id).find(".jugador").text();        
    
        if(tiempo == "" && costo == ""){

        }else{
            funcion_alert_pregunta('Carga cuenta', 'Esta seguro de cargar el tiempo a: ' + jugador, 'blue', 'carga_tiempo', id, jugador, costo);
        }
    });
  
    $(".caja").on("click", function(){
        if($('#area-tiempos .tiempo_venta').length > 0){
            funcion_alert_error('Atencion', 'Carga primero los tiempos pendientes');
        }else{
            var imagen = $(this).html(); //imagen
            $("#comentarios").css('display', 'block');
            var id = $(this).attr("id");
            var precio = $(this).attr("precio");
            
            precio = id == 'producto_13' ? 0 :  precio;
            
            var html = '';
            var clase = '';
            
            if(id == 'producto_13'){
                html += "<div class='area_dinero' precio='"+precio+"'>";
                clase = 'producto_13';
            }

            html += "<div id='"+id+"' class='producto_venta producto "+clase+"' precio='"+precio+"'>";
            //var html = "<div class='producto_venta' precio='"+precio+"'>";
                        
            html += imagen;            
            html += "</div>";
            
            if(id == 'producto_13'){
                html += '<input type="text" class="form-control form-control-sm dinero" name="" id="producto_dinero" ></div>';
            }

            //html += '<input type="text" class="form-control mt-2 mb-2" name="comentarios" id="comentarios" placeholder="Comentrios (opcional)">';

            $("#area-ventas").append(html);

            contador_ventas += parseInt(precio);

            $("#total_venta").text("Total: $" + contador_ventas);
        }
            //alert(contador_ventas);
    });
    
    $("#area-ventas").on("click", ".producto_venta", function(){
        var precio = $(this).attr("precio");
        contador_ventas -= parseInt(precio);
        
        if(contador_ventas == 0){
            $("#total_venta").text("");
        }else{
            $("#total_venta").text("Total: $" + contador_ventas);
        }
        
        var id = $(this).attr('id');
        
        if(id == 'producto_13'){
            $(this).closest('.area_dinero').remove();
        }else{
            $(this).remove();
        }
        
        if($('#area-ventas .producto_venta').length == 0){
            $("#comentarios").val('');
            $("#comentarios").css('display', 'none');
        }
        
        //alert("contador_ventas");
    });  
    
/*    $(".deudor").on("dragenter", function(){
        $(this).css("background", "red");// border-bottom-style:dashed; border-bottom-width:2px;");

    });
    
    $(".deudor").on("dragleave", function(){
        $(this).css("background", "");// border-bottom-style:dashed; border-bottom-width:2px;");

    });
*/    
/*    $(".carga").on("click", function(){
        if($('#area-ventas .producto_venta').length > 0){
    
            var nombre = $(this).attr('nombre');
            
            var id = $(this).closest('tr').attr('id');
            
            $.confirm({
                title: nombre ,
                content: '¿Esta seguro de cargar la venta?',
                type: 'blue',
                icon: 'icon-users',
                buttons: {
                    si: {
                        btnClass: 'btn btn-info',
                        text: 'si',
                        action: function(){
                            carga_venta(id);
                        }
                    },
                    no: function () {
                        $('#cuenta_'+id).modal('hide');
                    },                    
                }
            });

        }else{
            funcion_alert_exito('No hay productos', 'Favor de agregar productos para poder hacer la carga', 'red');
        }
    });
*/    
    $("#filas_cuentas").on("click", ".deudor .carga", function(){
        if($('#area-ventas .producto_venta').length > 0){
    
            var nombre = $(this).closest('tr').attr('nombre');            
            var id = $(this).closest('tr').attr('id');                                   
            
            $.confirm({
                title: nombre ,
                content: '¿Esta seguro de cargar la venta a: '+nombre+'?',
                type: 'blue',
                icon: 'icon-users',
                buttons: {
                    si: {
                        btnClass: 'btn btn-info',
                        text: 'si',
                        action: function(){
                            carga_venta(id);
                        }
                    },
                    no: function () {
                    },                    
                }
            });

        }else if($('#area-tiempos .tiempo_venta').length > 0){
            // accion para cargar los tiempos
            var nombre = $(this).closest('tr').attr('nombre');            
            var id = $(this).closest('tr').attr('id');
            
            $.confirm({
                title: nombre ,
                content: '¿Esta seguro de cargar el tiempo a: '+nombre+'?',
                type: 'blue',
                icon: 'icon-users',
                buttons: {
                    si: {
                        btnClass: 'btn btn-info',
                        text: 'si',
                        action: function(){
                            carga_tiempo_mesa(id, nombre);
                        }
                    },
                    no: function () {
                    },                    
                }
            });
            
        }else{
            funcion_alert_exito('No hay productos', 'Favor de agregar productos para poder hacer la carga', 'red');
        }
    });
    
/*    $('.actualiza').on('click', function(){
        var id = $(this).closest("tr").attr("id");
        
        jquery_php('', '', "actualiza_consumos", id);
    });
*/    
    $('#filas_cuentas').on('click', '.deudor .actualiza' , function(){
        var id = $(this).closest("tr").attr("id");
        
        jquery_php('', '', "actualiza_consumos", id);
    });
    
/*    $('.deudor').on('click', function(){
        var id = $(this).attr('id');
        var nombre = $(this).find('.nombre_fila').text();
        
        jquery_php(nombre, '', "actualiza_ventas", id);
    });
*/    
    $('#filas_cuentas').on('click', '.deudor', function(){
        var id = $(this).attr('id');
        var nombre = $(this).find('.nombre_fila').text();
        
        jquery_php(nombre, '', "actualiza_ventas", id);
    });
    
    $('#recibo').keypress(function(e){
        //var keycode = e.keyCode;
        if (e.which == 13) {
            var recibo = parseInt($('#recibo').val());
            var total = parseInt($('#total').text());
            var cambio = recibo - total;
            
            if(isNaN(cambio)){
                $('#recibo').val('');
                $('#cambio').text('');
            }else{
                if(recibo > total){
                    $('#cambio').text('Cambio: $' + cambio);
                }else{
                    $('#cambio').text('');
                    $('#recibo').val('');
                }//alert("validoooooo")
            }
            return false;
        }
    });
    
    $('.recibo_tiempo').keypress(function(e){
        if (e.which == 13) {
            var id = $('.show').attr('id');
            var costo = parseInt($('#'+id).find('.costo_tiempo').text());
            var recibo = parseInt($('#recibo_'+id).val());
            var cambio = recibo - costo;
                        
            if(isNaN(cambio)){
                $('#recibo_'+id).val('');
                $('#cambio'+id).text('');
            }else{
                if(recibo > costo){
                    $('#cambio_'+id).text('Cambio: $' + cambio);
                }else{
                    $('#recibo_'+id).val('');
                    $('#cambio_'+id).text('');
                }
                
            }
        return false;            
        }
    });
    
    $('.dividir_tiempo').keypress(function(e){
     
        if (e.which == 13) {
            var id = $('.show').attr('id');
            var costo = parseFloat($('#tiempo_'+id).find('.costo_tiempo').text());
            var entre = parseFloat($('#dividir_'+id).val());
            var parcialidades = Math.ceil(costo / entre);
    
            if(isNaN(parcialidades)){
                $('#dividir_'+id).val('');
                $('#de_'+id).text('');
            }else{                
                $('#de_'+id).text('De: $' + parcialidades);
            }
        return false;            
        }
    });
    
    $('#cuenta_modal').on('hidden.bs.modal' , function(){
        $('#recibo').val('');
        $('#cambio').text('');
    });
    
    $('.modal_mesas').on('hidden.bs.modal' , function(){
        var id = $(this).attr('id');
        $('#recibo_'+id).val('');
        $('#cambio_'+id).text('');
        $('#dividir_'+id).val('');
        $('#de_'+id).text('');
    });
    
    $('#pagar_venta').on('click', function(){
        var id = $('#cuenta_id').val();
        var check = '';
        var venta = '';
        var tiempo = '';
       
        $("input:checkbox:checked").each(function() {
            var tipo = $(this).attr('tipo');
            if(tipo == 'venta'){
                venta += ',' + $(this).val();
            }else{
                tiempo += ',' + $(this).val();
            }
                         
        });
        var ventas = venta.substring(1); 
        var tiempos = tiempo.substring(1);
       
        funcion_alert_pregunta('Cuenta', '¿Esta seguro que la cuenta ya esta pagada?', 'blue', 'pagar_cuenta', id, ventas, tiempos);
   });
   
   $('#nueva_cuenta').on('click', function(){
       if($('.producto_venta').length > 0){
            var html = '<form action="" class="form-inline d-flex justify-content-end formulario">';
                 html += '<input type="text" class="form-control mt-3" name="nombre" id="nombre" placeholder="Nombre" required>';
                 //html += '<textarea class="form-control mt-2" name="comentarios" id="comentarios" placeholder="Comentrios"></textarea>';
                 html += '<button id="oculta_cuenta" class="btn btn-secondary mt-2">Ocultar</button>';
                 html += '<button id="guarda_cuenta" class="btn btn-info mt-2">Guardar</button>';
             html += '</form>';
            
            $('.cuenta_nueva_form').html(html);
        }
   });

   $('.cuenta_nueva_form').on('click', '#guarda_cuenta', function (){
        
        if($('#area-ventas .producto_venta').length > 0 || $('#area-tiempos .tiempo_venta').length > 0){
            var nombre = $("#nombre").val();

            jquery_php(nombre, '', "nueva_cuenta", '');

            return false;
       }else{
           return false;
       }
   });
   
    $('.cuenta_nueva_form').on('click', '#oculta_cuenta', function (){
        $('.formulario').empty();
        return false; 
    });
   
    $('#producto_pagado').on('click', function(){
        if($('.producto_venta').length > 0){
            carga_venta('contado');
        }        
    });
    
    $('.cargar_tiempo_mesa').on('click', function(){
        if($('#area-ventas .producto_venta').length > 0){
            funcion_alert_error('Atencion', 'Cargue o marque como pagados los productos pendientes')
        }else{
            var id = $('.show').attr('id');
            var indice = id.indexOf('_');
            var mesa_id = id.substring(indice + 1);
            var costo = $('#tiempo_'+id).find('.costo_tiempo').text();
            var entre = $('#dividir_'+id).val();
            var tiempo_id = $('#tiempo_'+id).find('tr').attr('id');        
            var parcialidades = Math.ceil(parseFloat(costo) / parseFloat(entre));

            parcialidades = isNaN(parcialidades) ? 0 : parcialidades;

            var html = '';

            if(entre == ''){
                html += ' <div id="tiempo_1" class="producto_venta tiempo_venta" precio="'+costo+'" tiempo_id="'+tiempo_id+'" entre="1"> ';
                    html += '<i class="icon-clock reloj_carga"></i>';
                html += '</div>';
            }else{
                for(var i=1; i<parseInt(entre)+1; i++){           //alert(i);
                    html += ' <div id="tiempo_'+i+'" class="producto_venta tiempo_venta" precio="'+parcialidades+'" tiempo_id="'+tiempo_id+'" entre="'+entre+'"> ';
                        html += '<i class="icon-clock reloj_carga"></i>';
                    html += '</div>';
                }
            }

            $('#area-tiempos').empty();
            $('#area-tiempos').append(html);

            $('#'+id).modal('hide');
            
            llama_accion_php(mesa_id, "fin", entre, parcialidades, 0, tiempo_id);
            
            //alert(mesa_id + '  -----' + entre + '  ' + tiempo_id + '  ' +"parc   " +  parcialidades);
        }
    });
   
   
   
    $('.modal_mesas').on('show.bs.modal' , function(){
        var modal_id = $(this).attr('id');
        var indice = modal_id.indexOf('_');
        var id = modal_id.substring(indice + 1);
        //var entre = $('#dividir_'+id).val();
        //var costo = $('#tiempo_'+id).find('.costo_tiempo').text();
        //var parcialidades = Math.ceil(parseFloat(costo) / parseFloat(entre));
        //parcialidades = isNaN(parcialidades) ? 0 : parcialidades;
        
        
        
        $("#pagado_"+id).off('click');
        $("#pagado_"+id).click(function(e){ 
            var tiempo_id = $('#tiempo_modal_'+id).find('tr').attr('id');
            e.preventDefault();
            //alert(tiempo_id);
            llama_accion_php(id, "fin", '', '', '', tiempo_id);
            //llama_accion_php(mesa_id, "fin", entre, parcialidades, 0, tiempo_id);
        });
       
       
    
    });
    
    $('.cantidad').on('keyup', function(){
        $('.stock').val($('.cantidad').val());
    });
    
    $('.precio_compra').on('keyup', function(){
       var precio = parseFloat($('.precio_compra').val()); 
       var cantidad = parseFloat($('.cantidad').val()); 
       
       $('.costo_unitario').val(precio / cantidad);
       
    });
    
    $('.desactiva').on('click', function (){
        activa_desactiva($(this), 'desactivar');
    });
    
    $('.activa').on('click', function (){
        activa_desactiva($(this), 'activar');
    });
    
    $('.elimina').click(function (){
        var registro_id = $(this).closest('.registro_id').attr("id");
        var container = $(this).closest('.card');
        var url = new URL(window.location.href);
        var seccion = url.searchParams.get("seccion");
        //var valor_consulta = $(this).val();
        var result = confirm("Estás seguro de eliminar el registro ?");
        var url_ejecucion = "./index_ajax.php?seccion="+seccion+"&accion=elimina_bd&registro_id="+registro_id;
        if(result == true){
            $.ajax({ 
                url: url_ejecucion,
                type: "POST", //send it through get method
                //data: {},
                success: function() {
                    container.remove();
                    alert('Registro eliminado con éxito');
                },
                error: function() {
                    alert('Error: '+url_ejecucion);
                }
            });
        }
    });
    
});  