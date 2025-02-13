<?php
class Directivas{
    
    
    public function menu(){
		$modelo_menu = new menu();
		$registros = $modelo_menu->obten_menu_permitido();
		$menus = $registros['registros'];
        
                $html = '<ul class="navbar-nav mr-auto">';
                $html .= '<li class="nav-item active"><a href="index.php?seccion=session&accion=inicio" class="nav-link"><i class="bi bi-house"></i><span>Inicio</span></a></li>';
                //$html .= '<li class="nav-item active"><a href="index.php?seccion=session&accion=logout" class="nav-link"><i class="bi bi-power"></i><span>Salir</span></a></li>';
                //echo"<pre> --------"; print_r('lllll' ); echo"</pre>";//die;
                    foreach ($menus as $key => $menu) {
                            $etiqueta_menu = str_replace('_', ' ', $menu['descripcion']);

                            $submenu = $this->submenu($menu['id']);
    
                            $html .=" <li class='nav-item active dropdown'>
                                <a href='#' class='nav-link dropdown-toggle' id='menu-categorias' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false' >
                                    <i class='".$menu['icono']."'></i><span>".ucfirst($etiqueta_menu)."</span>                              
                                </a>
                                <div class='dropdown-menu' aria-labelledby='menu-categorias'>
                                    ".$submenu."
                                </div></li>";                        
                    }
                    $html .= '<li class="nav-item active"><a href="index.php?seccion=session&accion=logout" class="nav-link"><i class="bi bi-power"></i><span>Salir</span></a></li>';
                $html .= '</ul>';
                
		return $html;
	}
        public function submenu($menu_id){
		$modelo_submenu = new Menu();
		$resultado = $modelo_submenu->obten_submenu_permitido($menu_id);
		$menus = $resultado['registros'];
        
		$html = "";
		foreach ($menus as $key => $menu) {
        //echo"<pre> --------"; print_r($menu  ); echo"</pre>";die;
                    $etiqueta_menu = str_replace('_', ' ', $menu['descripcion']);

                    $seccion_menu_descripcion = $menu['seccion'];

                    $submenu = $this->link_menu($menu['id'], $seccion_menu_descripcion);
    
                    $html = $html.$submenu;
		}
		return $html;

	}
        public function link_menu($seccion_menu_id, $seccion_menu_descripcion){
		$link_seccion_menu_descripcion = strtolower($seccion_menu_descripcion);
		$modelo_accion = new Accion();
		$resultado = $modelo_accion->obten_accion_permitida($seccion_menu_id);
		$menus = $resultado['registros'];
   //echo"<pre> --------"; print_r($menus  ); echo"</pre>";die;                     
		$html = "";
		foreach ($menus as $key => $menu) {
			//$link_accion = strtolower($menu['accion_descripcion']);
                        $link_accion = strtolower($menu['sis_accion_nombre']);
                        
			$etiqueta_accion = ucfirst($link_accion);
    
			$html .= "<a href='index.php?seccion=$link_seccion_menu_descripcion&accion=$link_accion' class='dropdown-item'>$etiqueta_accion</a>";
		}
    
		return $html;

	}
        
        public function obten_productos(){
            $modelo_producto = new Modelos();
            $resultado = $modelo_producto->obten_productos();
            
            $productos = $resultado['registros'];
            
            $html = '';
            foreach ($productos AS $producto){
    //echo"<pre> --------"; print_r($producto  ); echo"</pre>";die;            
                //$html = $html.'<a href="#"><div class="caja"><img class="img-fluid rounded" src="./img/$producto[]"></div></a>';
                $html = $html."<div id='producto_".$producto['producto_id']."' class='caja d-flex justify-content-center' precio='".$producto['precio']."'><img class='img-fluid rounded' src='./img/".$producto['imagen']."'></div>";                
            }
            $html = $html."<div id='producto_13' class='caja d-flex justify-content-center'><i class='icon-dollar peso_venta'></i></div>";
            return $html;
        }
        public function obten_mesas(){
            $modelo = new Modelos();
            $mesas = $modelo->obten_mesas();
        
            $html = '<div class="contenedor d-flex justify-content-between">';
            $html2 = '<div class="contenedor2 d-flex justify-content-between">';
    //echo"<pre> --------"; print_r($mesas  ); echo"</pre>";
            $modal = '';
            foreach($mesas AS $mesa){
                $activo = 0;
                $data_target = "";
                $estilo = "bg-secondary";
                
                if($mesa["pagado"] == "n"){
                    $activo = 1;
                    $data_target = "#modal_".$mesa["mesa_id"];
                    $estilo = "bg-info";
                }
               
                //$modal = $modal . $this->ventana_modal($mesa["mesa_id"], $mesa["nombre"]);
                
                if($mesa["clase"] == 'domino'){ // data-target="#modal_'.$mesa["mesa_id"].'"
                    $modal = $modal . $this->ventana_modal($mesa["mesa_id"], $mesa["nombre"]);
                    $html2 = $html2.'<a href="" id="div_mesa_'.$mesa["mesa_id"].'" data-toggle="modal" data-target="'.$data_target.'">
                                    <div id="mesa_'.$mesa["mesa_id"].'" class="'.$mesa["clase"].  " " .$estilo.'  border-0 rounded mesa" activo="'.$activo.'">
                                        '.$mesa["nombre"].'
                                        <br><span class="reloj" id="reloj_'.$mesa["mesa_id"].'"></span>
                                    </div>
                                </a>';
                }else if($mesa["clase"] == 'baraja'){
                    $modal = $modal . $this->ventana_modal_baraja($mesa["mesa_id"], $mesa["nombre"]);
                    $html2 = $html2.'<a href="" id="div_mesa_'.$mesa["mesa_id"].'" data-toggle="modal" data-target="#modal_'.$mesa["mesa_id"].'">
                                    <div id="mesa_'.$mesa["mesa_id"].'" class="'.$mesa["clase"].  " " .$estilo.'  border-0 rounded mesa_baraja" activo="'.$activo.'">
                                        '.$mesa["nombre"].'
                                        <br><span class="reloj" id="reloj_'.$mesa["mesa_id"].'"></span>
                                    </div>
                                </a>';
                }else{  // data-target="#modal_'.$mesa["mesa_id"].'"
                    $modal = $modal . $this->ventana_modal($mesa["mesa_id"], $mesa["nombre"]);
                    $html = $html.'<a href="" id="div_mesa_'.$mesa["mesa_id"].'" data-toggle="modal" data-target="'.$data_target.'">
                                    <div id="mesa_'.$mesa["mesa_id"].'" class="'.$mesa["clase"]. " " .$estilo. ' border-0 rounded mesa" activo="'.$activo.'">
                                        '.$mesa["nombre"].'
                                        <br><span class="reloj" id="reloj_'.$mesa["mesa_id"].'"></span>
                                    </div>
                                </a>';
                }
            }
            $html = $html . '</div>';
            $html2 = $html2 . '</div>';
            $html = $html . $html2 . $modal; 
            return $html;
        }
        
        public function ventana_modal($id, $nombre){
             
    //echo"<pre> -----++++++++++++++++++++++++++++---- siiiiii"; print_r($tiempo  ); echo"</pre>";//die;  
            
                $html = ' <div class="modal fade modal_mesas" id="modal_'.$id.'" tabindex="-1" role="dialog" aria-labelledby="modal_'.$id.'" aria-hidden="true"> 
                       <div class="modal-dialog ">
                           <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title" id="nombre_'.$id.'"><strong>'.$nombre.'</strong></h5>
                                   <button class="close" data-dismiss="modal" aria-label="Cerrar">
                                       <span aria-hidden="true">&times;</span>
                                   </button>
                               </div>
                               <div class="modal-body">
                                    <div class="container-fluid">
                                        
                                        <table id="tabla-baraja" class="table table-sm table-striped table-hover">
                                            <thead class="thead-dark">
                                                <tr>
                                                    <th scope="col">Inicio</th>
                                                    <th scope="col">Fin</th>
                                                    <th scope="col">Tiempo</th>
                                                    <th scope="col">Total $</th>                                                    
                                                </tr>
                                            </thead>
                                            <tbody id="tiempo_modal_'.$id.'">


                                            </tbody>
                                        </table>
                                    </div>
                               </div>
                               
                               <div class="modal-footer">
                                    <div class="dividir_tiempo"> 
                                        <form action="" class="form-inline d-flex justify-content-start">                                                
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-lg dividir" name="dividir" id="dividir_modal_'.$id.'" placeholder="Dividir entre">
                                            </div>
                                            <div class="form-group">
                                                <h2><strong id="de_modal_'.$id.'"></strong></h2>
                                            </div>
                                        </form>                                            
                                    </div>
                                </div>
                                
                               <div class="modal-footer">
                                    <div class="cobro_tiempo"> 
                                        <form action="" class="form-inline d-flex justify-content-start">                                                
                                            <div class="form-group">
                                                <input type="text" class="form-control form-control-lg recibo_tiempo" name="recibo_tiempo" id="recibo_modal_'.$id.'" placeholder="Recibo la cantidad">
                                            </div>
                                            <div class="form-group">
                                                <h2><strong id="cambio_modal_'.$id.'"></strong></h2>
                                            </div>
                                        </form>                                            
                                    </div>
                                </div>
                               <div class="modal-footer">
                                    <button id="pagado_'.$id.'" class="btn btn-info pagado">Pagado</button>
                                    <button id="" class="btn btn-info cargar_tiempo_mesa">Cargar</button>
                                    <button id="" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                               </div>
                           </div>
                       </div>
                    </div> ';
            return $html;
        }
        
        public function ventana_modal_baraja($id, $nombre){
            $html = '';
            
            //echo"<pre> -----++++++++++++++++++++++++++++---- siiiiii"; print_r($jugadores  ); echo"</pre>"; die;
            
            $html .= ' <div class="modal fade modal_baraja" id="modal_'.$id.'" tabindex="-1" role="dialog" aria-labelledby="modal_'.$id.'" aria-hidden="true"> 
                       <div class="modal-dialog ">
                           <div class="modal-content">
                               <div class="modal-header">
                                   <h5 class="modal-title" id="nombre_'.$id.'"><strong>'.$nombre.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</strong></h5>
                                       
                                   <button id="agregar" class="btn btn-info">+</button>&nbsp;&nbsp;&nbsp;
                                   <button id="eliminar" class="btn btn-secondary">-</button>
                                    <button class="close" data-dismiss="modal" aria-label="Cerrar">
                                       <span aria-hidden="true">&times;</span>
                                   </button>
                               </div>
                               <div class="modal-body">
                                   <div class="container-fluid">

            <table id="tabla-baraja" class="table table-sm table-striped table-hover">
            <thead class="thead-dark">
              <tr>
                <th scope="col">Nombre</th>
                <th scope="col">Inicio</th>
                <th scope="col">Tiempo</th>
                <th scope="col">Costo</th>
                <th scope="col"></th>
                <th scope="col"></th>
                
              </tr>
            </thead>
            <tbody id="tiempos-baraja">';
              
            $html .= $this->actualiza_jugador_baraja($id);
            
            $html .= '</tbody>
          </table>
        
                                    </div>
                               </div>
                               <div class="modal-footer">
                                    <button id="guardar_baraja" class="btn btn-info guardar">Guardar</button>
                                    <button id="" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                               </div>
                           </div>
                       </div>
                    </div> ';
            return $html;
        }
        
        public function actualiza_jugador_baraja($id){
            $modelo = new Modelos(); 
            $jugadores = $modelo->consulta_tiempos_baraja($id);
            $html = '';
            
            if($jugadores){
                foreach($jugadores AS $jugador){
                    $inicio = substr($jugador["inicio"], 10, -3 );
                    $html = $html . '<tr id="'.$jugador["tiempo_id"].'" class="fila" inicio="'.$jugador["inicio"].'" precio="'.$jugador["precio"].'">
                                            <td><span class="jugador">'.$jugador["jugador"].'</span></td>
                                            <td><span class="inicio">'.$inicio.'</span></td>
                                            <td><span class="tiempo"></span></td>
                                            <th scope="row"><span class="costo_baraja"></span></th>
                                            <td>'.$this->link_pagado($id).'</td>
                                            <td>'.$this->link_asignar($id).'</td>
                                      </tr>';
                }
            }
            return $html;
        }
        
        public function link_pagado($id){
		$html = "<a href='#' class='link_accion p-2 m-0 icono tiempo_pagado' title='pagado'>
                            <i class='icon-dollar icono_boton'></i>
                        </a>";
  		return $html;
	}
        public function link_asignar($id){
		$html = "<a href='#' class='link_accion p-2 m-0 icono carga_tiempo' title='Asignar tiempo'>
                            <i class='icon-angle-circled-right icono_boton'></i>
                        </a>";
  		return $html;
	}
        
        public function link_cargar($id){
		$html = "<a href='#' class='link_accion p-2 m-0 icono carga' title='Cargar'>
                            <i class='icon-angle-circled-right icono_boton'></i>
                        </a>";
  		return $html;
	}
        
        public function link_actualiza_cuenta(){
		$html = "<a href='#' class='link_accion p-2 m-0 icono actualiza' title='Actualizar cuentas'>
                            <i class='icon-arrows-cw icono_boton'></i>
                        </a>";
  		return $html;
	}
        
        public function obten_cuentas($cuenta_id=''){
            $modelo = new Modelos();
            $html = '';
            
            if($cuenta_id){
                $cuentas = $modelo->obten_cuenta($cuenta_id);
            }else{
                $cuentas = $modelo->obten_cuentas();
            }
            
            $ventas = $modelo->obten_ventas($cuenta_id);
            //$tiempos = $modelo->obten_tiempos_cuenta();
        //echo"<pre> -----++++++++++++++++++++++++++++---- siiiiii"; print_r($cuentas  ); echo"</pre>";    
            foreach ($cuentas AS $cuenta){
                $html2 = '';
                //$html3 = '';
                $html = $html . '<tr id="'.$cuenta['cuenta_id'].'" class="deudor" nombre="'.$cuenta['nombre'].'" >';
                $html = $html . '<td class="">'.$this->link_cargar($cuenta['cuenta_id']).'</td> <td class="nombre_fila" data-toggle="modal" data-target="#cuenta_modal">'.$cuenta['nombre'].'</td>';
                
                $html = $html . '<td class=" " data-toggle="modal" data-target="#cuenta_modal">';//d-flex flex-row producto_cuenta
                foreach($ventas AS $venta){
                    if($cuenta['cuenta_id'] == $venta['cuenta_id']){                        
                        if($venta['producto_id'] == 13){
                            $html2 .= '<i class="icon-dollar"></i>';
                        }else{
                        
                            if($venta['tipo'] == 'tiempo'){
                                $html2 .= '<i class="icon-clock"></i>';
                            }else{
                                for($i=0; $i<$venta['cantidad']; $i++){
                                    $html2 .= '<img class="img-fluid rounded producto_cuenta" src="./img/'.$venta['imagen'].'">';                            
                                }
                            }
                        }
                        
                    }
                }
                /*foreach($tiempos AS $tiempo){
                    if($cuenta['cuenta_id'] == $tiempo['cuenta_id']){
                        $html3 .= '<i class="icon-clock"></i>';
                    }
                }*/
                $html = $html . '<div class="d-flex flex-wrap justify-content-start consumos">';
                $html = $html . $html2;
                $html = $html . '</div>';
                $html = $html . '<td>' . $this->link_actualiza_cuenta() . '</td>';
                $html = $html . '</td></tr>';
            }
            return $html;
        }
        
        //public function crea_modal_cuenta($id, $nombre){
        public function crea_modal_cuenta(){
            //$modelo = new Modelos(); 
            //$cuentas = $modelo->obten_cuentas();
    //echo"<pre> ---------"; print_r($cuentas  ); echo"</pre>";        
            $html = '';
            //foreach ($cuentas AS $cuenta){
                $html .= ' <div class="modal fade modal_cuenta" id="cuenta_modal" tabindex="-1" role="dialog" aria-labelledby="cuenta_modal" aria-hidden="true"> 
                           <div class="modal-dialog modal-lg" id="modal_dial">
                               <div class="modal-content">
                                   <div class="modal-header">
                                       <h3 class="modal-title"><strong"><span id="nombre_cuenta"></span></strong></h3>
                                       <input type="hidden" id="cuenta_id" value="">
                                        <button class="close" data-dismiss="modal" aria-label="Cerrar">
                                           <span aria-hidden="true">&times;</span>
                                       </button>
                                   </div>
                                   <div class="modal-body">
                                       <div class="container-fluid">                                        
                                            <table id="tabla-baraja" class="table table-sm table-striped table-hover">
                                                <thead class="thead-dark">
                                                  <tr>
                                                  <th scope="col"></th>
                                                    <th scope="col">Producto</th>
                                                    <th scope="col">Cantidad</th>
                                                    <th scope="col">Costo</th>
                                                    <th scope="col">Hora</th>
                                                    <th scope="col">Comentarios</th>
                                                  </tr>
                                                </thead>
                                                <tbody id="cuenta_ventas">
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                   </div>
                                    <div class="modal-footer">
                                        <div class=" cobro"> 
                                            <form action="" class="form-inline d-flex justify-content-between">
                                                <div class="form-group">
                                                    <h2><strong>Total: $</strong><strong id="total"></strong></h2>
                                                </div>
                                                <div class="form-group">
                                                    <input type="text" class="form-control form-control-lg" name="recibo" id="recibo" placeholder="Recibo la cantidad">
                                                </div>
                                                <div class="form-group">
                                                    <h2><strong id="cambio"></strong></h2>
                                                </div>
                                            </form>                                            
                                        </div>
                                    </div>
                                   <div class="modal-footer">
                                        <button id="pagar_venta" class="btn btn-info guardar">Pagado</button>
                                        <button id="" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                   </div>
                               </div>
                           </div>
                        </div> ';
            //}
            return $html;
        }
        
        public function obten_tiempo_nocargado(){
            $modelo = new Modelos();
            $tiempos = $modelo->tiempo_nocargado();
                                                       // echo"<pre>"; print_r($tiempos  ); echo"</pre>";         
            if($tiempos){
                $tiempo = $tiempos[0];
                $html = '';
                for($i=1; $i<$tiempo['entre']+1; $i++){
                    
                    $html .= ' <div id="tiempo_'.$i.'" class="producto_venta tiempo_venta" precio="'.$tiempo["parcialidades"].'" tiempo_id="'.$tiempo["tiempo_id"].'" entre="'.$tiempo["entre"].'"> ';
                        $html .= '<i class="icon-clock reloj_carga"></i>';
                    $html .= '</div>';
                }
                $html .= '<span><h4>Tiempo de mesa ' . $tiempo["mesa_id"] . '</h4></span>' ; 
                return $html;
            }
            
        }
        
        public function nav_breadcumbs($cols, $offset, $breadcrumbs){
		$breadcrumbs = $this->breadcrumbs($breadcrumbs, ACCION);
		$html = "<nav class='breadcrumb  col-md-$cols col-md-offset-$offset'>
  			$breadcrumbs
			</nav>";
		return $html;
	}
        
        public function breadcrumbs($breadcrumbs, $active){
            $html = $this->breadcrumb(SECCION). "  ";//     /-
            foreach ($breadcrumbs as $key => $value) {
                    $link = strtolower($value);
                    $etiqueta = ucfirst($link);
                    $html = $html.$this->breadcrumb($etiqueta, $link).'  ';//    /+			
            }

            $html = $html.$this->breadcrumb_active($active);

            return $html;
	}
        
        public function breadcrumb($etiqueta, $accion=""){
            $etiqueta = strtolower($etiqueta);
            $etiqueta = ucfirst($etiqueta);
            $etiqueta = str_replace('_', ' ', $etiqueta);
            if($accion == ""){
                $link = "#";
            }
            else{
                $link = "./index.php?seccion=".SECCION."&accion=$accion";
            }
            $html = "<a class='breadcrumb-item' href='$link'>$etiqueta</a>";
            return $html;
	}
        
        public function breadcrumb_active($etiqueta){
            $etiqueta = strtolower($etiqueta);
            $etiqueta = ucfirst($etiqueta);
            $etiqueta = str_replace('_', ' ', $etiqueta);

            $html = "<span class='breadcrumb-item active'>$etiqueta</span>";
            return $html;
	}
        
        public function nav_breadcumbs_modifica($cols, $offset, $breadcrumbs){
            $breadcrumbs = $this->breadcrumbs($breadcrumbs, ACCION);
            $html = "<nav class='breadcrumb col-md-$cols col-md-offset-$offset'>
                    $breadcrumbs
                    </nav>";
            return $html;
	}
        
        public function encabezado_form_alta($accion, $accion_envio=False){
	    if(!$accion_envio){
	        $accion_envio = 'alta_bd';
            }
            $html = "<h3>".ucfirst(SECCION)."</h3>";
            $html .= "<form id='form-".SECCION."-$accion' name='form-".SECCION."-$accion' method='POST' 
                        action='./index.php?seccion=".SECCION."&accion=$accion_envio&session_accion=$accion' enctype='multipart/form-data' class='formularios col-12 d-flex align-items-center flex-column'> ";
            return $html;
	}
        
        public function btn_enviar($cols=12){
            $html = "<div class='form-group p-0 col-md-$cols'>	
                        <button type='submit' class='btn btn-lg btn-info btn-block' >Guardar</button>
                    </div>";
            return $html;		
	}
        
        public function input_texto($value="", $cols=6, $campo, $capital="", $requerido="required"){
            $label_guion = ucwords($campo);
            $label = str_replace('_', ' ', $label_guion);

            $html = "<div class='form-group mb-2 p-0 col-md-$cols'>
                        <label for='$campo'>$label:</label>
                        <input type='text' class='form-control $campo input-md $capital' name='$campo' placeholder='Ingresa $label' 
                                $requerido title='Ingrese $label' value='$value'>
                    </div>";
            return $html;
        }
        
        public function input_custom_texto($value="", $imagen, $campo, $name, $capital="", $requerido="required"){
            $label_guion = ucwords($campo);
            $label = str_replace('_', ' ', $label_guion);

            $html = "<label class='sr-only' for='$campo'>Username</label>
                        <div class='input-group mb-2 p-0 col-md-12'>
                            <div class='input-group-prepend w-1'>
                                <div class='input-group-text'><img src='./img/".$imagen."' class='card-img imagen_caja' alt='...'></div>
                            </div>
                        
                        <input type='text' class='form-control $campo form-control-lg $capital' name='$name' placeholder='Ingresa $label' 
                                $requerido title='Ingrese $label' value='$value' id='$campo'>
                    </div>";
            return $html;
        }
        
        public function input_hidden($name){
            $html = "<div hidden class='form-group'>
                        <input type='text' class='form-control $name' name='$name' value=''>
                    </div>";
            return $html;
        }
        
        public function input_file($col=12){
            $html = '<div class="input-group mb-3 p-0 col-'.$col.'">
                        <div class="input-group-prepend">
                          <span class="input-group-text">Subir</span>
                        </div>
                        <div class="custom-file">
                          <input type="file" name="archivo" class="custom-file-input" id="subir_archivo" aria-describedby="subir_archivo">
                          <label class="custom-file-label" for="subir_archivo">Elegir archivo</label>
                        </div>
                      </div>';
            return $html;
        }
        
        public function input_check($label, $clase, $value='', $checked=''){// se cambio el valor del name, antes era $value
            $html = '<div class="custom-control custom-switch ml-2 mb-2 '.$clase.'">
                        <input type="checkbox" class="custom-control-input" name="'.strtolower($label).'" value="'.$value.'" id="'.$label.'" '.$checked.'>
                        <label class="custom-control-label" for="'.$label.'">'.$label.'</label>
                      </div>';
            return $html;
	}
        
        public function input_select($value, $col=12, $campo, $tabla, $name){
            $modelo = new Modelos();
            $label_guion = ucwords($campo);
            $label = str_replace('_', ' ', $label_guion);
            $opciones = $modelo->llena_select($tabla);
          
            $html = '<div class="form-group mb-2 col-'.$col.'">
                        <label for="'.$campo.'">'.$label.'</label>
                        <select class="custom-select " name="'.$name.'">';
            
                $html .='<option >Selecciona un producto</option>';
                    foreach($opciones AS $opcion){
                        if($opcion['producto_id'] == $value){
                            $html .= '<option selected value="'.$opcion['producto_id'].'">'.$opcion['nombre'].'</option>';
                        }else{
                            $html .= '<option value="'.$opcion['producto_id'].'">'.$opcion['nombre'].'</option>';
                        }
                        
                    }
                        
                $html .='</div></select>';
            return $html;
        }
        
        public function crea_card($registro, $campos, $tabla, $acciones){//echo'---' . $accion .' <pre>';print_r($registro); echo'</pre>'; //die;
            $tabla = $tabla == 'productos' ? 'producto' : $tabla;// tenia $tabla2 se le quito para que sea generica
            $registro['status'] = $registro['status'] == 1 ? 'Activo' : 'Inactivo';
            $html = '<div class="card mb-3 col-md-2 m-2 p-0 ">';//style="max-width: 540px;
                //$html .= '<div class="row no-gutters">';
                    
                    //$html .= $this->card_imagen($registro['imagen']);
                    $html .= '<div class="col-md-12 pl-0 pr-0 registro_id" id="'.$registro[$tabla.'_id'].'">';
                        $html .= '<div class="card-header bg-secondary text-white d-flex justify-content-between">'.$registro['nombre'].'   <img src="./img/'.$registro['imagen'].'" class="card-img imagen_lista" alt="..."></div>';
                        //$html .= $this->card_imagen($registro['imagen']);
                        $html .= '<div class="card-body p-3">';
                        foreach($campos AS $campo){
                            $html .= '<p class="card-text"><strong>'. str_replace('_', ' ', ucfirst($campo)) . ":</strong> <span class='".$campo."'>". $registro[$campo].'</span></p>';
                        }
                        $html .= '</div><div class="card-footer bg-transparent">' . $this->obten_acciones($tabla, $registro[$tabla.'_id'], $registro['status'], $acciones) .'</div>'; //poner los botones para las acciones
                        
            $html .= '</div></div>';//</div>
            return $html;
        }
        
        public function card_imagen($imagen){
            $html = '<div class="col-md-3 d-flex align-items-center">';
                //$html .= '<div class="card-header bg-info"></div>';
                $html .= '<img src="./img/'.$imagen.'" class="card-img" alt="...">';
            $html .= '</div>';
            return $html;
        }
        
        public function obten_acciones($tabla, $id, $status, $acciones){
            $modelo_accion = new Accion();
            $html = '';
            $icono = 0;

            foreach ($acciones AS $accion){
               $permiso = 0;
//echo'------------------' . $accion . '<br>';
                $acciones_permitidas = $modelo_accion->obten_accion_permitida_session($tabla, $accion);
                $accion_permitida = $acciones_permitidas['registros'];
                $permiso = $accion_permitida[0]['n_registros'];
//echo'------------------' . $accion .' <pre>';print_r($acciones_permitidas); echo'</pre>'; //die;
                if ($accion == 'modifica' && $permiso == 1) {
                    $html .= $this->link_modifica($tabla, $id);
                }
                if ($accion == 'elimina_bd' && $permiso == 1) {
                    $html .= $this->link_elimina($tabla, $id);
                }
                if (($accion == 'activa_bd' || $accion == 'desactiva_bd') && $icono == 0 && $permiso == 1) {
                    $icono = 1;
                    $html .= $this->link_cambia_status($tabla, $id, $status);
                }
            }
            return $html;
        }
    
        public function link_modifica($seccion,$id){
            $html = "<a href='index.php?seccion=$seccion&accion=modifica&registro_id=$id' class='icono_accion p-2 m-0 icono_accion modifica' title='Modifica'>
                        <i class='icon-doc-text icono_boton'></i>
                    </a>";
            return $html;
        }
        
        public function link_elimina($id){
		$html = "<a href='#registro_$id' class='icono_accion p-2 elimina' title='Elimina'>
                            <i class='icon-cancel icono_boton'></i>
                        </a>";
  		return $html;
	}
        
        public function link_activa($id){
            $html = "<a href='#registro_$id' class='icono_accion p-2 activa' title='Activa registro'>
                        <i class='icon-ok icono_boton'></i>
                    </a>";
            return $html;
	}
        
        public function link_desactiva($id){
            $html = "<a href='#registro_$id' class='icono_accion p-2 desactiva' title='Desactiva registro'>
                        <i class='icon-window-minimize icono_boton'></i>
                    </a>";
            return $html;
	}
        
        public function link_cambia_status($seccion, $id, $status){
            $html = "";
            if($status=='Activo'){
                $html = $html.$this->link_desactiva($seccion, $id); 
            }
            else{ 
                $html = $html.$this->link_activa($seccion, $id);
            } 
            return $html;		
	}
        
        public function encabezado_form_modifica($registro_id){
            $html = "<h3>".ucfirst(SECCION)."</h3>";
                        
            $html = "<form id='form-".SECCION."-modifica' name='form-".SECCION."-modifica' method='post' action='./index.php?seccion=".SECCION."&accion=modifica_bd&registro_id=".$registro_id."'
                    enctype='multipart/form-data' class='formularios col-12 d-flex align-items-center flex-column'>
            ";
            return $html;
	}
        
        public function crea_tabla($encabezados, $registros){
            $html = '<div class="columna col-lg-7 col-md-12">
                    <div class="widget">
                    <table id="tabla" class="table table-sm table-striped table-hover col-lg-12">
                        <thead class="thead-dark">
                            <tr>';
                                foreach($encabezados AS $encabezado){
                                    $html .= '<th scope="col">'.str_replace('_', ' ',ucfirst($encabezado)).'</th>';
                                }
                   $html .= '</tr>
                        </thead>
                        <tbody id="">';
                        foreach ($registros AS $registro){
                            $clase = '';
                            if($registro['diferencia'] != $registro['venta']){
                                $clase = 'texto_rojo';
                            }
                            $html .= '<tr id="'.$registro['producto_id'].'" class="fila '.$clase.'">';
                            foreach($encabezados AS $encabezado){
                                $html .= '<td><span class="'.$encabezado.'">'.$registro[$encabezado].'</span></td>';
                            }
                            $html .= '</tr>';
                        }

                $html .= '</tbody>
                    </table></div></div>';
            return $html;
        }
        
}


