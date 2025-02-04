<?php
class Conexion{
	public $nombre_base_datos = 'request';
	private $host = 'localhost';
	private $user = 'root';
	private $pass = '';
	public $link;
	function __construct(){
		$this->link = mysqli_connect($this->host, $this->user, $this->pass);
                mysqli_set_charset($this->link, "utf8");
	}
	public function selecciona_base_datos($nombre_base_datos=false){
		if($nombre_base_datos){
			$this->nombre_base_datos = $nombre_base_datos;
		}
		$consulta = "USE ".$this->nombre_base_datos;
		$this->link->query($consulta);
		if($this->link->error){
			return array('mensaje' => $this->link->error, 'error' => True );
		}
		else{
			return true;
		}
	}
}