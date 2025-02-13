<?php

class Accion extends Modelos{
    
    public function obten_accion_permitida_session($seccion, $accion){
        $consulta = "SELECT COUNT(*) AS n_registros
                    FROM sis_accion_grupo accion_grupo
                        INNER JOIN sis_accion accion ON accion.id = accion_grupo.accion_id
                        INNER JOIN sis_submenu seccion_menu ON seccion_menu.id = accion.submenu_id
                        INNER JOIN sis_grupo grupo ON grupo.id = accion_grupo.grupo_id
                        INNER JOIN sis_menu menu ON menu.id = seccion_menu.menu_id";
                        
        $grupo_id = $_SESSION['grupo_id'];
        $where = " WHERE menu.nombre = '$seccion' AND grupo_id = $grupo_id ";
        $where = $where." AND accion.visible = 0 AND accion.nombre = '$accion'";
        $where = $where." AND accion.status = 1 AND seccion_menu.status = 1 AND grupo.status = 1";
 
        $consulta = $consulta.$where;
//echo"<pre>"; print_r($consulta); echo"</pre>";//die;        
        $resultado = $this->ejecuta_consulta($consulta);
        return $resultado;
    }
    
    public function obten_accion_permitida($seccion_menu_id){
        $grupo_id = $_SESSION['grupo_id'];
        $sql = new consultas_base();
        $consulta = $sql->genera_consulta_base('sis_accion_grupo'); //echo"1---" . $consulta . "<br>";
        $where = "
             WHERE
                sis_accion.status = 1 
                AND sis_grupo.status = 1 
                AND sis_accion_grupo.sis_grupo_id = $grupo_id 
                AND sis_accion.sis_submenu_id = $seccion_menu_id
                AND sis_accion.visible = 1 
                ";
        $consulta = $consulta.$where;
        $group_by = " GROUP BY sis_accion.id_sis_accion ";
        $consulta = $consulta.$group_by;
//echo"<pre>"; print_r($consulta); echo"</pre>";die; 
//echo"--->" . $consulta . "---+++";
        $resultado = $this->ejecuta_consulta($consulta);
        return $resultado;
	}
}
