<?php
include_once(str_replace('/class', 'config.php', __DIR__));
class db
{
    private static $instance = null;
    private $pdo;
	  private $query;
	  private $parametros;

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct(){
  		try {
  			$this->pdo = new PDO('mysql:host='.SERVIDOR.';dbname='.BBDD, USUARIO, CONTRASENA);
  			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  			$this->pdo->setAttribute(PDO::ATTR_PERSISTENT, false);
  			$this->pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES UTF8");
  		}
  		catch (PDOException $e) {
  			die('Se ha producido un error: ' . $e->getMessage());
  		}
    }

	private function bind($array){
		foreach($array as $param=>$value)	{
			$this->parametros[] = ':'.$param . "\x7F" . $value;
		}
	}


	private function exec($query, $parametros=array()){
		try {
			$this->query = $this->pdo->prepare($query);
			if(count($parametros)>0){
                            $this->bind($parametros);
                        }
			if(count($this->parametros)>0) {
				foreach($this->parametros as $param){
					$trocea = explode("\x7F",$param);
					$this->query->bindParam($trocea[0],$trocea[1]);
				}
			}
			$this->succes = $this->query->execute();
		}
		catch(PDOException $e){
			die('Error en la consulta ' .$this->query->queryString .'<br>'.$e->getMessage());
		}
		$this->parametros = array();
	}


	public static function query($query,$parametros = null,$fetchmode = PDO::FETCH_OBJ){
		$db = self::GetInstance();
		$query = trim($query);
		$db->exec($query,$parametros);

		if (stripos($query, 'select') === 0){
			return $db->query->fetchAll($fetchmode);
		}elseif (stripos($query, 'insert') === 0){
			return $db->pdo->lastInsertId();
		}elseif	(stripos($query, 'update') === 0 || stripos($query, 'delete') === 0){
			return $db->query->rowCount();
		}else {
			return NULL;
		}
	}

	public static function insert($tabla, $valores){
		$db = self::GetInstance();
		$campos = array(implode(",",array_keys($valores)),":" . implode(",:",array_keys($valores)));
		$query = "INSERT INTO ".$tabla." (".$campos[0].") VALUES (".$campos[1].")";
		return $db->query($query, $valores);
	}


	public static function update($tabla, $valores, $condicion=null){
		$db = self::GetInstance();
		$campos = array_keys($valores);
		$query = "UPDATE " . $tabla .  " SET ";
		foreach($campos as $index=>$campo){
			$query .= (($index>0)?',':''). $campo . " = :". $campo;
		}
		if($condicion !== null){
                    $query .= " WHERE " . $condicion;
                }
		return $db->query($query, $valores);
	}

	public static function delete($tabla, $condicion=null){
		$db = self::GetInstance();
		$query = "DELETE FROM $tabla" . (($condicion !== null)?" WHERE $condicion":"");
		return $db->query($query);
	}


	// convierte una fecha (dd/mm/yyyy) a formato valido para insertar en una base de datos
	public static function fechaAdb($fecha){
		if (($timestamp = strtotime(str_replace("/", "-", $fecha))) === false) {
		   return null;
		} else {
		   return date('Ymd', $timestamp) ;
		}
	}

}
