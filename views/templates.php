<?php
class templates{
    public $directiva;
    public $modelo;
    public $campos_lista;
    public $acciones;
    public $campos_modificables_text;
    
    public function __construct() {
        $this->directiva = new Directivas();
        $this->modelo = new Modelos();
    }
    
    public function alta($accion, $breadcrumbs, $tabla){
        
        $producto = empty($_GET['producto_id']) ? '' : $_GET['producto_id'];
        $html = $this->directiva->encabezado_form_alta($accion);
        $html .= $breadcrumbs;
        $html .= "<div class='col-md-8 col-md-offset-2 alta  '>";
        $html .= $this->genera_campos_alta($tabla, $accion, $producto);
        $html .= $this->directiva->btn_enviar();
        $html .= "</div></form>";
        return $html;
    }
    
    public function genera_campos_alta($tabla, $accion, $producto=''){
        $html = '';
        if($tabla == 'productos'){
            $html .= $this->directiva->input_texto('', 12, 'nombre', 'capital');
            $html .= $this->directiva->input_texto('', 12, 'proveedor', 'capital', '');
            $html .= $this->directiva->input_texto('', 12, 'unidad', 'capital', '');
            $html .= $this->directiva->input_texto('', 12, 'orden', 'capital', '');
            $html .= $this->directiva->input_file(12);
            $html .= $this->directiva->input_check('Visible', $tabla.'_visible', 'visible');            
        }else if($tabla == 'precios'){
            $html .= $this->directiva->input_select($producto, 12, 'Producto', $tabla, 'producto_id');
            $html .= $this->directiva->input_texto('',12, 'precio', 'capital');
        }else if($tabla == 'inventario'){
            $html .= $this->directiva->input_select($producto, 12, 'Producto', $tabla, 'producto_id');
            $html .= $this->directiva->input_texto('', 12, 'cantidad');
            $html .= $this->directiva->input_texto('', 12, 'precio_compra');
            $html .= $this->directiva->input_hidden('costo_unitario');
            $html .= $this->directiva->input_hidden('stock');
        }else if($tabla == 'caja'){
            $registros = $this->modelo->obten_productos_activos();
            $productos = $registros['registros'];
            foreach ($productos AS $producto){
                $html .= $this->directiva->input_custom_texto('', $producto['imagen'], $producto['nombre'], $producto['producto_id']);
            } 
            $html .= $this->directiva->input_custom_texto('', 'efectivo.png', 'Efectivo', 13);
        }
        
        return $html;
    }
    
    public function genera_campos_lista($tabla){
        if($tabla == 'productos'){
            $this->campos_lista = array('proveedor', 'unidad', 'orden', 'visible', 'status');
        }if($tabla == 'precios'){
            $this->campos_lista = array('precio');
        }if($tabla == 'inventario'){
            $this->campos_lista = array('cantidad', 'precio_compra', 'costo_unitario', 'fecha_captura', 'stock');
        }
    }
    
    public function lista($breadcrumbs, $seccion, $registros){
        $html = $breadcrumbs;
        $this->genera_campos_lista($seccion);
        $this->acciones($seccion);
        $html .= '<div class="row ml-3">';
        foreach($registros AS $registro){
            $html .= $this->directiva->crea_card($registro, $this->campos_lista, $seccion, $this->acciones);
        }
        $html .= '</div>';
    //echo"<pre>"; print_r($registros); echo"</pre>"; die;    
        return $html;
    }
    
    public function acciones($tabla){
        $modifica = array('productos', 'precios', 'inventario');
        $desactiva_bd = array('productos', 'inventario');
        $activa_bd = array('productos');
        $elimina_bd = array('productos', 'precios');
        
        $this->acciones = [];
        if(in_array($tabla, $modifica)){
            $this->acciones[] = 'modifica';
        }
        if(in_array($tabla, $desactiva_bd)){
            $this->acciones[] = 'desactiva_bd';
        }
        if(in_array($tabla, $activa_bd)){
            $this->acciones[] = 'activa_bd';
        }
        if(in_array($tabla, $elimina_bd)){
            $this->acciones[] = 'elimina_bd';
        }
        
    }
    
    public function modifica($controlador, $seccion){                               
        $directiva = new Directivas();
        $template = new templates();
        $html = $this->directiva->encabezado_form_modifica($controlador->registro_id);
        $html .= $controlador->breadcrumbs;
        $html .= "<div class='col-md-8 col-md-offset-2 modifica'>";
        $html .= $template->genera_campos_modificables($seccion, $controlador->registro);  //echo"<pre>"; print_r($template->genera_campos_modificables($seccion, $controlador->registro)); echo"</pre>";
        $html .= $this->directiva->btn_enviar();
        $html .= "</div>";
        $html .= "</form>";
        return $html;
    }
    
    public function campos_modifica($tabla){
        
        if($tabla == 'producto'){            
            $imagen = array('campo'=>'imagen', 'file'=>'1');
            $status = array('campo'=>'visible','checkbox'=>'1', 'name'=>'Visible', 'length'=>12);            
            $this->campos_modificables_text = array('nombre', 'proveedor', 'unidad', 'orden', $imagen, $status);
        }
        if($tabla == 'precios'){         
            $this->campos_modificables_text = array('precio');
        }
        if($tabla == 'inventario'){         
            $this->campos_modificables_text = array('cantidad', 'precio_compra', 'costo_unitario', 'stock');
        }
        if($tabla == 'cat_sis_grupo'){
            $this->campos_modificables_text = array('nombre');
        }
        if($tabla == 'cat_sis_menu'){
            $this->campos_modificables_text = array('nombre', 'icono');
        }       
        if($tabla == 'sis_usuario'){
            $grupo = array('campo'=>'grupo_id', 'tabla'=>'cat_sis_grupo', 'name'=>'Grupo_id');
            $this->campos_modificables_text = array('nombre', 'usuario', 'email', 'password', $grupo);
        }
        if($tabla == 'cat_sis_submenu'){
            $menu = array('campo'=>'menu_id', 'tabla'=>'cat_sis_menu', 'name'=>'Menu_id');
            $this->campos_modificables_text = array(
                'nombre','icono',$menu);
        }
        if($tabla == 'cat_sis_accion'){
            $submenu = array('campo'=>'submenu_id', 'tabla'=>'cat_sis_submenu', 'name'=>'Submenu_id');
            $visible = array('campo'=>'visible','checkbox'=>'1', 'name'=>'Visible', 'length'=>12);
            $this->campos_modificables_text = array(
                'nombre',$submenu,$visible);
        }
        
    }
    
    public function genera_campos_modificables($tabla, $registro){
        $tabla = $tabla == 'productos' ? 'producto' : $tabla;
        $directiva = new Directivas();
        $html = "";
        $checked = "";
        $status = '';
        $this->campos_modifica($tabla);
        $n_campos = count($this->campos_modificables_text);
    
        foreach ($this->campos_modificables_text as $campo){
            if($n_campos == 2){
                $n_cols = 12;
            }else{
                $n_cols = 6;
            }            
                        
            if(is_array($campo)){
                if(array_key_exists('checkbox', $campo)){                           
                    if($registro[$tabla.'_'.$campo['campo']] == 1){
                        $checked = 'checked';                    
                    }else{
                        $checked = '';
                    }
    
                    $html = $html.$directiva->input_check($campo['name'], $tabla.'_'.$campo['campo'], $registro[$tabla.'_'.$campo['campo']], $checked );
                    continue;
                }else if(array_key_exists('radio', $campo)){                                //echo"--++-><pre>";   print_r($campo); echo"---></pre> <br>";
                    $check = array();
              
                    $checked = $registro[$tabla.'_pago_a'];
                    
                    for($i=0; $i<count($campo)-1; $i++){
                        $check[$i]['label'] = $campo[$i]['label'];
                        $check[$i]['name'] = $campo[$i]['name'];
                        $check[$i]['value'] = $campo[$i]['value'];
                        if($campo[$i]['value'] == $checked){
                            $check[$i]['checked'] = 'checked';
                        }
                    }
    
                    $html = $html.$directiva->input_radio_button( $check);
                }else if(array_key_exists('date', $campo)){
                    $html = $html.$directiva->input_fecha($registro[$tabla.'_'.$campo['campo']], $n_cols, $campo['campo'], '');
                    continue;
                }else if(array_key_exists('textarea', $campo)){
                    $html = $html.$directiva->input_textarea($registro[$tabla.'_'.$campo['campo']],12, $campo['campo'], $campo['name']);
                    continue;
                }else if(array_key_exists('etiqueta', $campo)){
                    $html = $html.$directiva->div_label($campo['etiqueta'].$registro[$tabla.'_id']);
                    continue;
                }else if(array_key_exists('hidden', $campo)){
                    $html = $html.$directiva->input_hidden($registro[$tabla.'_'.$campo], $n_cols, $campo);//-------------------------------------revisar cuando modifica inventario
                }else if(array_key_exists('file', $campo)){
                    $html = $html.$directiva->input_file();
                }else{                                                    
                    $html = $html.$directiva->input_select($campo['tabla'], $registro[$tabla.'_'.$campo['campo']],$n_cols, $campo['name']);
                    continue;
                    
                }
            }else{
                $html = $html.$directiva->input_texto($registro[$tabla.'_'.$campo], 12, $campo);
            }
        }

        return $html;
    }
    
}

