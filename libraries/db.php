<?php

class dynamicPDO extends PDO {
	private $tablePrefix;
	public $sessionPrefix;
	private $sqlType;
	private $queries;
	private $qSearch=array('!prefix!','!table!');
	private $forbiddenStructureChars=array(',',';');

	public static function exceptionHandler($exception) {
		die('Uncaught Exception:'.$exception->getMessage());
	}

	public function __construct($dsn,$username,$password,$tablePrefix) {
		/*
			The exceptionHandler connection details from being revealed
			in the case of a connection failure
		*/
		set_exception_handler(array(__CLASS__,'exceptionHandler'));
		parent::__construct($dsn,$username,$password);
		restore_exception_handler();

		$this->sqlType=strstr($dsn,':',true);
		$this->tablePrefix=$tablePrefix;
		/* should implement a better session prefix method */
		$this->sessionPrefix=$tablePrefix;
		$this->loadModuleQueries('common',true);
	}

	public function loadModuleQueries($moduleName,$dieOnError=false) {
		$target='queries/'.$this->sqlType.'/'.$moduleName.'.queries.php';
		if (file_exists($target)) {
			require_once($target);
			$loader=$moduleName.'_addQueries';
			$this->queries[$moduleName]=$loader();
			return true;
		} else if ($dieOnError) {
			die('Fatal Error - '.$moduleName.' Queries Library File not found!<br>'.$target);
		} else return false;
	}

	private function prepQuery($queryName,$module,$tableName) {
		if(!isset($this->queries[$module])){
			$this->loadModuleQueries($module);
		}
		if (isset($this->queries[$module][$queryName])) {
			return str_replace(
				$this->qSearch,
				array(
					$this->tablePrefix,
					$tableName
				),
				$this->queries[$module][$queryName]
			);
		} else return false;
	}

	public function query($queryName,$module='common',$tableName='') {
		if ($query=$this->prepQuery($queryName,$module,$tableName)) {
			return parent::query($query);
		} else {
			return false;
		}
	}

	public function exec($queryName,$module='common',$tableName='') {
		if ($query=$this->prepQuery($queryName,$module,$tableName)) {
			return parent::exec($query);
		} else return false;
	}

	public function prepare($queryName,$module='common',$tableName='') {
		if ($query=$this->prepQuery($queryName,$module,$tableName)) {
			return parent::prepare($query);
		} else return false;
	}

	public function tableExists($tableName) {
		try {
			$statement=$this->query('tableExists','common',$tableName);
	  	$result=$statement->fetchAll();
  		return (count($result)>0);
		} catch (PDOException $e) {
  		return false;
  	}
	}

	public function countRows($tableName) {
		$result=$this->query('countRows','common',$tableName);
		return $result->fetchColumn();
	}

	/*
		structure is an array of field names and definitions
	*/
	public function createTable($tableName,$structure,$verbose=false) {
		if ($this->tableExists($tableName)) {
			return false;
		} else {
			$query='CREATE TABLE `'.$this->tablePrefix.$tableName.'` (';
			$qList=array();
			foreach ($structure as $field => $struct) {
				if(is_int($field)){ //no field name, so it's a command - e.g. INDEX(`blogId`) etc
					$qList[].="\n\t" . str_replace(';','',$struct);
				}else{
					$qList[].="\n\t`".str_replace(';','',$field).'` '.str_replace(';','',$struct);
				}
			}
			$query.=implode(', ',$qList)."\n)";
			if ($verbose) echo '<pre>',$query,'</pre>';
			parent::exec($query);
			$errors=$this->errorInfo();
			return $errors[0]==0;
		}
	}
}

function db_init($db) {
	try {
		$PDO=new dynamicPDO($db['dsn'],$db['username'],$db['password'],$db['tablePrefix']);
	} catch (PDOException $e) {
		die('
			SQL PDO Object Failed to Initialize<br />
			Error reported: '.$e->getMessage().'
		');
	}
	return $PDO;
}

?>