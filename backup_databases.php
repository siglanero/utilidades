<?php

//INFORME SEUS DADOS -----------------

$host 	= 'localhost';
$user 	= 'root';
$pass 	= '';
$folder = 'dbs/';

//------------------------------------

//retorna todas as databases
$dbh = new PDO( "mysql:host=$host", $user, $pass );
$dbs = $dbh->query( 'SHOW DATABASES' );

foreach ($dbs as  $value) {
	execBackup($host, $user, $pass, $folder,$value['Database']);
}

function execBackup($host, $user, $pass, $folder, $dbName){
	if( $dbName !== 'information_schema'){

		$db = new DBBackup(array(
			'driver' => 'mysql',
			'host' => $host,
			'user' => $user,
			'password' => $pass,
			'database' => $dbName
		));

		$backup = $db->backup();
		$fp = fopen($folder.$dbName.'.sql', 'a+');
		fwrite($fp, $backup['msg']);
		fclose($fp);
	}
}

Class DBBackup {

	private $host;
	private $driver;
	private $user;
	private $password;
	private $dbName;
	private $dsn;
	private $tables = array();
	private $handler;
	private $error = array();
	private $final;


	public function DBBackup($args){
		if(!$args['host']) $this->error[] = 'Parameter host missing';
		if(!$args['user']) $this->error[] = 'Parameter user missing';
		if(!isset($args['password'])) $this->error[] = 'Parameter password missing';
		if(!$args['database']) $this->error[] = 'Parameter database missing';
		if(!$args['driver']) $this->error[] = 'Parameter driver missing';

		if(count($this->error)>0){
			return;
		}

		$this->host = $args['host'];
		$this->driver = $args['driver'];
		$this->user = $args['user'];
		$this->password = $args['password'];
		$this->dbName = $args['database'];

		$this->final = 'CREATE DATABASE ' . $this->dbName.";\n\n"; 

		if($this->host=='localhost'){
            // We have a little issue in unix systems when you set the host as localhost
			$this->host = '127.0.0.1';
		}
		$this->dsn = $this->driver.':host='.$this->host.';dbname='.$this->dbName;

		$this->connect();
		$this->getTables();
		$this->generate();
	}

	public function backup(){
        //return $this->final;
		if(count($this->error)>0){
			return array('error'=>true, 'msg'=>$this->error);
		}
		return array('error'=>false, 'msg'=>$this->final);
	}

	private function generate(){
		foreach ($this->tables as $tbl) {
			$this->final .= '--CREATING TABLE '.$tbl['name']."\n";
			$this->final .= $tbl['create'] . ";\n\n";
			$this->final .= '--INSERTING DATA INTO '.$tbl['name']."\n";
			$this->final .= $tbl['data']."\n\n\n";
		}
		$this->final .= '-- THE END'."\n\n";
	}

	private function connect(){
		try {
			$this->handler = new PDO($this->dsn, $this->user, $this->password); 
		} catch (PDOException $e) {
			$this->handler = null;
			$this->error[] = $e->getMessage();
			return false;
		}
	}

	private function getTables(){
		try {
			$stmt = $this->handler->query('SHOW TABLES');
			$tbs = $stmt->fetchAll();
			$i=0;
			foreach($tbs as $table){
				$this->tables[$i]['name'] = $table[0];
				$this->tables[$i]['create'] = $this->getColumns($table[0]);
				$this->tables[$i]['data'] = $this->getData($table[0]);
				$i++;
			}
			unset($stmt);
			unset($tbs);
			unset($i);

			return true;
		} catch (PDOException $e) {
			$this->handler = null;
			$this->error[] = $e->getMessage();
			return false;
		}
	}

	private function getColumns($tableName){
		try {
			$stmt = $this->handler->query('SHOW CREATE TABLE '.$tableName); 
			$q = $stmt->fetchAll();
			$q[0][1] = preg_replace("/AUTO_INCREMENT=[\w]*./", '', $q[0][1]); 
			return $q[0][1];
		} catch (PDOException $e){
			$this->handler = null;
			$this->error[] = $e->getMessage();
			return false;
		}
	}

	private function getData($tableName){
		try {
			$stmt = $this->handler->query('SELECT * FROM '.$tableName);
			$q = $stmt->fetchAll(PDO::FETCH_NUM);
			$data = '';
			foreach ($q as $pieces){
				foreach($pieces as &$value){
					$value = htmlentities(addslashes($value));
				}
				$data .= 'INSERT INTO '. $tableName .' VALUES (\'' . implode('\',\'', $pieces) . '\');'."\n"; 
			}
			return $data;
		} catch (PDOException $e){
			$this->handler = null;
			$this->error[] = $e->getMessage();
			return false;
		}
	}
}

?>
