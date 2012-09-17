<?php 
	

	class DB {

		private static $_instance = NULL;		
		protected $tableName;  //表名
		protected $indexName; //主键的名称
		protected $instance	= null;  //存储POD对象实例	
		protected $dsn;		//PDO 数据库连接字符串
		protected $user;	
		protected $pwd;
		protected $sqlFields= '*';	//查询字段			
		protected $fetchMode= null; 	
		private $configPath;	//XML配置文档路径
		public $columnValue	= array();	//数据表字段值 数组
		public $options		= array();	//连贯操作查询项 
		public $sqlString	= '';	//查询字符串	
		protected $limitOneStatus = false;
		
		const GET_ROW = true;	//取一行数据
		const GET_ROWS = false;		//去多行数据
			
		public function __construct($tableName){
			
			
			$this->getPDO();
			$this->init($tableName);
			//设置 数据库默认字符集
			$this->instance->exec('SET NAMES utf8');
		}

		public function init($tableName){
			if($tableName == $this->tableName){
				return self::$_instance;		
			}
			$this->tableName = getConfItem("db_prefix").$tableName;
			$this->indexName = $this->getIndexName();			
			$this->configPath= APP_PATH.'/config/config.xml';
			return self::$_instance;
		}
		public function __destruct(){
			unset($this->instance);
		}

		public static function getInstance($tableName=''){
			
			if(!self::$_instance instanceof self){
				self::$_instance = new DB($tableName);
			}
			
			return self::$_instance;
		}
	
		//获取PDO对象实例
		private function getPDO(){

			if(!defined('CONF_METHOD')){ define('CONF_METHOD','array');}
			switch(CONF_METHOD){
				case 'XML':$this->getDsn($this->configPath);break;
				default :$this->getDsnArray();break;			
			}
			
			try{
				$this->instance = new PDO($this->dsn,$this->user,$this->pwd);
			}catch(PDOException $e){
				die("ERROR:".$e->getMessage()."<br/>");
				Debug::addErrorMsg($e->getMessage());
			}
			

		}	//=(APP_PATH.'/connections/config.xml')
		//获取PDO的DSN和 用户名，密码
		private function getDsn($path){ 
			$xml = simplexml_load_file($path);
			$dsn = $this->dsn = $xml->xpath('dsn');
			$user = $xml->xpath('user');
			$pwd = $xml->xpath('pwd');
			$this->dsn = $dsn[0];$this->user = $user[0];$this->pwd = $pwd[0];
			
		}
		//获取PDO配置信息（数组形式）
		private function getDsnArray($path=APP_PATH){
			//$dsn=include "$path/config/config.php";
			$dsn = getConf();
			$this->dsn = $dsn['db'].':host='.$dsn['host'].';dbname='.$dsn['dbname'];
			$this->user= $dsn['username'];
			$this->pwd = $dsn['pwd'];
		}
		//取得主键的名字
		public function getIndexName(){
			
			$result = $this->instance->query("DESC $this->tableName");
			
			$fieldName = $result->fetch();
			return $fieldName['Field'];
			//return 'id';
		}
		//获取表的列信息	  参数：$attrName 为空时表示取字段所有属性	反之取指定属性
		//$columnName 为-1是表示取表中首字段信息 ；为正数或0 表示 第几个字段的信息
		public function getColumn($attrName='',$columnName=-2){
			$result = $this->instance->query("DESC $this->tableName");
			switch($columnName){
				case -1:$fieldName = $result->fetch();break;
				default:$fieldName = $result->fetchAll();break;
			}
			
			if(!empty($attrName)){
				$fieldName= $columnName<0 ? $fieldName["$attrName"]:$fieldName[$columnName][$attrName];			
			}
			return $fieldName;					
		}
	
		/*============<PDO 方法>===========*/
		
		public function query($sqlString){
			$result = $this->instance->query($sqlString);
			return $result;		
		}
		
		public function exec($sqlString){
			$result = $this->instance->exec($sqlString);
			return $result;			
		}
		public function lastInsertId(){
			$result = $this->instance->lastInsertId();
			return $result;					
		}
		/*============<获取数据集>===========*/
		public function select($fields='*',$sqlWhere=array(),$sin=self::GET_ROWS){
			if(!empty($this->options)){
				$sqlString = $this->resolveSQL();

				$sql = "SELECT $this->sqlFields FROM $this->tableName $sqlString ";
				Debug::addSqlCommand($sql);
				$result = $this->instance->query($sql);
#				$emd = $result->fetch();
#				echo empty($emd[0]);
#				if(empty($emd)){
#					print_r($result->fetchAll());
#					return $result->fetchAll();
#				}else{
#					print_r($result->fetch());
#					return $result->fetch();				
#				}		
				
				//return $result->fetchAll();	
				
				if(!$this->limitOneStatus ){//$sin === false){
					//echo self::GET_ROWS;
					return $result->fetchAll($this->fetchMode);
				}else{
					
					return $result->fetch($this->fetchMode);				
				}
				
			}
			$string='where 1=1 and';
			foreach($sqlWhere as $key => $val){
				$string.=' '.$key.' '.$val;
			}
			
			$result = $this->instance->query("SELECT $fields FROM $this->tableName $string ");
			
			return $result->fetchAll();
			
			
			
			
		}
		//获取所有数据		
		public function findAll($fetchMode=NULL){
			
			$sql = "SELECT * FROM $this->tableName";
			Debug::addSqlCommand($sql);
			$result = $this->instance->query($sql);
			return $result->fetchAll($fetchMode);			
			
		}
		//获取指定ID数据
		public function find($id){
			$sql = "SELECT * FROM $this->tableName WHERE $this->indexName = $id ";
			Debug::addSqlCommand($sql);
			$result = $this->instance->query($sql);
			
			while($row = $result->fetch()){
				$rs = $row;
			}
			//将获取的数据 传给 数据表字段值
			$this->columnValue=$rs;
			$this->getArray($this->columnValue);
			return $rs;
			
			
		}
		//从数组中提取关联数组
		public function getArray(&$data){
			foreach($data as $key =>$val){
				if(is_numeric($key)){
					unset($data[$key]);
				}
			}
			
			
		}
		//解析连贯操作
		protected function resolveSQL(){
			$sqlString='';
			foreach($this->options as $key => $val){
				
				switch($key){
					case 'field':$this->sqlFields = $val ;break;
					case 'mode' :$this->fetchMode = $val ;break;
					case 'page' : 
						$la = explode($val,',');
						$start= --$la[0]*$la[1];
						$sqlString.=" LIMIT $start,$la[1]";
						break;
					case 'order':$sqlString.=" $key by $val";break;
					case 'group':$sqlString.=" $key by $val";break;
					case 'limit':
						$la = explode($val,',');
						$flog = isset($la[1]) ? $la[1] : '2';
						if($val == '1' or $flog == '1')
							$this->limitOneStatus = true;
						//echo $this->limitOneStatus ;
					default :$sqlString.=" $key $val";break;
				}
			}
			return $sqlString;
			
		}
		protected function postFilter(){
			
		}
		
		public function count(){
			$rs = $this->instance->query("SELECT count(*) FROM $this->tableName");
			$rt = $rs->fetch();
			return $rt[0];
			
		}
		/*================<连贯操作 方法>=================*/
								
		/*============<DATA  ADD>===========*/
		
		public function add($data=array()){
			if(empty($data)){
				
				throw new Exception("ERROR!!");
			} 
			$ins ='NULL';$col = $this->indexName ;
			foreach($data as $key => $val){
				$ins .= ','."'$val'";
				$col .= ','.$key;
			}
			
			$strins = "INSERT INTO `$this->tableName`($col) VALUES($ins)";	
			return $this->exec($strins);
		}
		
		/*=================<UPDATE SAVE>==================*/
		
		public function save($data=array()){

			if(empty($data)){
			
				throw new Exception("ERROR!!");
			}
			
			$up ='';
			foreach($data as $key => $val){
			
				if($key == $this->indexName){
					$flog = true;
					$conditions = "`$key` = $val";
					continue;
				}
				if(is_string($val)){
					$up .= "`$key`='$val',";
				}else{
					$up .= "`$key`=$val,";	
				}
			}
			
			if(!isset($flog)) return false;
			
			$up = substr($up,0,-1);
			$strup = "UPDATE `$this->tableName` set $up WHERE $conditions";
			//echo $strup;
			return $this->exec($strup);
		}
		
		
		/*====================<DELETE>====================*/
		public function delete($conditions){
			if(is_numeric($conditions)){
				$conditions = "$this->indexName = $conditions";
			}
			$strdel = "DELETE FROM $this->tableName WHERE $conditions";
			
			return $this->instance->exec($strdel);
		}

		public function __call($method,$arg){

	     		$class = '$this->instance->';
			$count = count($arg);	
			for($i=0,$tmp = array(); $i < $count ;$i++)
				$tmp[] = '$arg['.$i.']';
        	return eval('return '.$class.$method.'('.implode(",",$tmp).');');			
			//return call_user_func(array($this->instance,$method,$arg));
		}
		

		
	}
	


	
	

?>
