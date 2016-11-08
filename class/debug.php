<?php

class debug{
	private static $instance = null;

	public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new self;
        }
        return self::$instance;
    }

	// mostrar campos llegan a la pagina
	public static function campos($output=true, $fichero="log.txt"){
		$datos = "\r\n$_SERVER[REQUEST_URI] ------> ".date("d/m/Y H:i:s", time())."\r\n";
 		foreach($_POST as $nombre_campo => $valor){	$datos .= "POST['$nombre_campo'] = $valor	\r\n"; }
		foreach($_GET as $nombre_campo => $valor){	$datos .= "GET['$nombre_campo'] = $valor	\r\n"; }
		if($output){
			echo "<pre>$datos</pre>";
		}else{
			$debug = self::GetInstance();
			$debug->log($datos, $fichero);
		}
	}

	public static function log($texto, $fichero="log.txt"){
		$fp = fopen($fichero,"a+");
		fwrite($fp, $texto."\r\n", 1024);
		fclose($fp);
	}
}
 
