<?php

class consultas_base{
    public $link;
    
    public function genera_consulta_base($tabla){  
        $columnas = $this->obten_columnas_completas($tabla);
        $tablas = $this->obten_tablas_completas($tabla);        //print_r($columnas);
        $consulta = "SELECT $columnas FROM $tablas";
        return $consulta;
    }
    private function obten_columnas_completas($tabla){//echo"--> " . $tabla . "<--- <br><br>";
        $columnas = "";
        if($tabla == 'cat_sis_accion'){
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_submenu')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_menu')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_accion');
        }
        if($tabla == 'producto'){
            $columnas = $columnas.$this->genera_columnas_consulta('producto');
        }
        if($tabla == 'precios'){
            $columnas = $columnas.$this->genera_columnas_consulta('precios');
        }
        if($tabla == 'inventario'){
            $columnas = $columnas.$this->genera_columnas_consulta('inventario');
        }
        if($tabla == 'cat_turno'){
            $columnas = $columnas.$this->genera_columnas_consulta('cat_turno');
        }
        if($tabla == 'cat_sis_grupo'){
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_grupo');
        }
                
        if($tabla == 'cat_sis_submenu'){
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_submenu')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_menu');
        }
        if($tabla == 'sis_usuario'){
            $columnas = $columnas.$this->genera_columnas_consulta('sis_usuario')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_grupo');
        }
        if($tabla == 'cat_sis_accion_grupo'){
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_accion_grupo')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_accion')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_grupo')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_submenu')." , ";
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_menu');
        }
        if($tabla == 'cat_sis_menu'){
            $columnas = $columnas.$this->genera_columnas_consulta('cat_sis_menu');
        }    
        

        return $columnas;
    }
    private function obten_tablas_completas($tabla){
        $tablas = "";
        if($tabla == 'cat_sis_accion'){
            $tablas = $tablas.' cat_sis_accion AS cat_sis_accion';
            $tablas = $tablas.' INNER JOIN cat_sis_submenu AS cat_sis_submenu ON cat_sis_submenu.id = cat_sis_accion.submenu_id';
            $tablas = $tablas.' INNER JOIN cat_sis_menu AS cat_sis_menu ON cat_sis_menu.id = cat_sis_submenu.menu_id';
        }
        if($tabla == 'cat_sis_grupo'){
            $tablas = $tablas.' cat_sis_grupo AS cat_sis_grupo';
        }        
        if($tabla == 'cat_sis_submenu'){
            $tablas = $tablas.' cat_sis_submenu AS cat_sis_submenu';
            $tablas = $tablas.' INNER JOIN cat_sis_menu AS cat_sis_menu ON cat_sis_menu.id = cat_sis_submenu.menu_id';
        }
        if($tabla == 'cat_sis_menu'){
            $tablas = $tablas.' cat_sis_menu AS cat_sis_menu';
        }
        if($tabla == 'sis_usuario'){
            $tablas = $tablas.' sis_usuario AS sis_usuario';
            $tablas = $tablas.' INNER JOIN cat_sis_grupo AS cat_sis_grupo ON cat_sis_grupo.id = sis_usuario.grupo_id';
        }
        if($tabla == 'cat_sis_accion_grupo'){
            $tablas = $tablas.' cat_sis_accion_grupo AS cat_sis_accion_grupo';
            $tablas = $tablas.' INNER JOIN cat_sis_accion AS cat_sis_accion ON cat_sis_accion.id = cat_sis_accion_grupo.accion_id';
            $tablas = $tablas.' INNER JOIN cat_sis_grupo AS cat_sis_grupo ON cat_sis_grupo.id = cat_sis_accion_grupo.grupo_id';
            $tablas = $tablas.' INNER JOIN cat_sis_submenu AS cat_sis_submenu ON cat_sis_submenu.id = cat_sis_accion.submenu_id';
            $tablas = $tablas.' INNER JOIN cat_sis_menu AS cat_sis_menu ON cat_sis_menu.id = cat_sis_submenu.menu_id';
        }
        if($tabla == 'producto'){
            $tablas = $tablas.' producto AS producto';
        }
        if($tabla == 'precios'){
            $tablas = $tablas.' precios AS precios';
        }
        if($tabla == 'inventario'){
            $tablas = $tablas.' inventario AS inventario';
        }
        
        return $tablas;
    }
    
    private function genera_columnas_consulta($tabla){
        $columnas_parseadas = $this->obten_columnas($tabla);
        $columnas_sql = "";
        
//print_r($columnas_parseadas); echo")<br>"; die;
        foreach($columnas_parseadas as $columna_parseada){
            $columnas_sql .= $columnas_sql == ""?"$tabla.$columna_parseada AS $tabla"."_$columna_parseada":",$tabla.$columna_parseada AS $tabla"."_$columna_parseada";
            //print_r($columnas_sql); //echo")))))))<br>"; //die;
        }
                                                                      // print_r($columnas_sql); echo")))))))<br>"; //die;
        return $columnas_sql;
    }
    private function obten_columnas($tabla){
        $modelos = new modelos();
        $consulta = "DESCRIBE $tabla";  
        $result = $modelos->ejecuta_consulta($consulta);
        $columnas = $result['registros'];
        $columnas_parseadas = [];
        foreach($columnas as $columna ){
            foreach($columna as $campo=>$atributo){
                if($campo == 'Field'){
                    $columnas_parseadas[] = $atributo;
                }
            }
        } //echo"</pre>"; print_r($columnas_parseadas); echo"</pre>"; die;
        return $columnas_parseadas;
    }
}