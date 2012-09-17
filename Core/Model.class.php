<?php

/**
 +------------------------------------------------------------------------------
 * 模型 业务类
 +------------------------------------------------------------------------------
 * @category   Core
 * @package  Core
 * @author    Falm <Mydo.Master@gmail.com>,hjj <hjj1992@gmail.com>
 * @version   $Id$
 +------------------------------------------------------------------------------
 */
 
	class Model{
	
		protected $db;		//DB类实例
		protected $errorInfo	= '';	//错误信息
		protected $_id = NULL;
		public $tableName;	//表名
		public $modelName;	//模型名称
		
		
		private $childInstance; //子类实例
		//protected $_validate  = array('验证字段','验证规则','错误提示','验证条件','附加规则','验证时间');
		

	    /**
	     +----------------------------------------------------------
	     * 构造函数
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $tableName 数据表名
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */
		public function __construct($tableName){

			
			$this->tableName = $tableName;
			$this->modelName = $tableName.'Model';
			//$this->db = DB::getInstance()->init($tableName);
			$this->db = new DB($tableName);
			$this->define();
		}
		
	    /**
	     +----------------------------------------------------------
	     * 定义常量方法
	     +----------------------------------------------------------
	     * @access protected
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */		
		protected function define(){
			if(!defined('__TABLE__')){ define('__TABLE__',$this->tableName);}
			if(!defined('__MODEL__')){ define('__MODEL__',$this->modelName);}
		}
		
		
	    /**
	     +----------------------------------------------------------
	     * CRUD自动完成方法
	     +----------------------------------------------------------
	     * @access protected
	     +----------------------------------------------------------
	     * @param array $data 要完成的数据
	     * @param int $method 
	     +----------------------------------------------------------
	     * @return array
	     +----------------------------------------------------------
	     */		
		protected function autoComplete(&$data=array(),$method=3){
			
			
			$modelName=$this->modelName;
			if(!property_exists($modelName,'_auto')) return true;
			foreach($modelName::$_auto as $key => $val){
				switch($val['3']){
					case 'function': $data[$val['0']] = $val['1']($data[$val['0']]) ;break;
					case 'callback': $data[$val['0']] = $modelName::$val['1']() ;break;
					case 'field': $data[$val['0']] = $data[$val['1']] ;break;					
					default: $data[$val['0']]=$val['1'] ;break;					
										
				}
			}

			//return $data;
			
		}
		
		
	    /**
	     +----------------------------------------------------------
	     * CRUD自动验证方法
	     +----------------------------------------------------------
	     * @access protected
	     +----------------------------------------------------------
	     * @param string $data 要验证的数据
	     * @param string $method 验证方法名
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */		
		protected function autoValidate(&$data,$method){
		 	$modelName=$this->modelName;
			$datas = true;
		 	//判断是否需要进行自动验证
			if(!property_exists($modelName,'_validate')) return true;
			foreach($modelName::$_validate as  $val){
       			 //if($val['5']!=$method || $val['5'] !=NULL)  continue;
				if(!(@$val['5']==3 || !array_key_exists(5,$val)) && $val['5']!=$method) continue;
				switch($val['4']){
					case 'regex':;break;
					case 'function':if( !$val['1']($data[$val['0']]) ){
								$this->errorInfo=$val['2'];
								$datas=false;							
							};break;
					case 'callback':if(!$modelName::$val['1']($data[$val['0']])){
								$this->errorInfo=$val['2'];
								$datas = false;
							};break;
					case 'confirm':if($data[$val['0']]!=$data[$val['1']]){
								$this->errorInfo=$val['2'];
								$datas=false;							
							};break;
					case 'equal':if( $data[$val['0']]!=$val['1'] ){
								$this->errorInfo=$val['2'];
								$datas=false;
							};break;		
					case 'in':if(!in_array($data[$val['0']],$val['1'])){
								$this->errorInfo=$val['2'];
								$datas=false;						
							};break;
					case 'unique':if(!unique($val['0'],$data[$val['0']]) ){
								$this->errorInfo=$val['2'];
								$datas=false;
							};break;
					default :$this->errorInfo = '没有此验证条件！';
							$datas=false;
							;break;										
				}
			}
			return $datas;
			
		}


		public function unique($field,$value){
			$result =  $this->db->select('*',array('WHERE'=>"$field = $value"));
			if($result->errorCode()!='00000')
				return false;
			return true;
			
		}
		
		
	    /**
	     +----------------------------------------------------------
	     * 获取错误信息
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return string 
	     +----------------------------------------------------------
	     */		
		public function getError(){
			return $this->errorInfo;
		}
		
		/*====================<Page 方法>==================*/
		
	    /**
	     +----------------------------------------------------------
	     * 分页方法
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $key $_GET 关于Page的参数名
	     * @param int $length 显示页数
	     +----------------------------------------------------------
	     * @return Model
	     +----------------------------------------------------------
	     */		
		public function page($key,$length)
		{
			# code...
			
			$page = !empty($_GET[$key]) ? $_GET[$key] : 1 ; 
			
		//	if($page == 1){
		//		$start = $page;
		//	}else{
				
				$start = ($page - 1) * $length;
				
		//	}
			
			return $this->limit("$start,$length");
			
			
		}
		/*====================<CRUD 方法>==================*/
		
		
	    /**
	     +----------------------------------------------------------
	     * 创建数据
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param array $data 数据
	     +----------------------------------------------------------
	     * @return bool
	     +----------------------------------------------------------
	     */		
		public function create(array $data=array()){
			if(empty($data)){
				$data=$_POST;
			}elseif(!is_array($data)){
				return false;
			}
			try{
				//$this->resolvePost($data);
			}catch(Exception $e){
				$this->errorInfo=$e->getMessage();
				return false;
			}
			//$this->autoComplete($data);
			//$this->autoVaildate($data);
			
			$this->db->columnValue=$data;
			
			return true;
		}
		
		
	    /**
	     +----------------------------------------------------------
	     * 解析POST数据
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param array $data 数据
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */		
		private function resolvePost(&$data){
			$column = $this->db->getColumn();
			//$count = count($column);
			$i=0;
			foreach($data as $key => $val){
				if($key != $column[$i++]['Field']){
					unset($data[$key]);
				}			
			}
			//if(!$this->checkType($data)){
			//	throw new Exception('POST提交 数据类型错误!');	
			//}
		}

		
	    /**
	     +----------------------------------------------------------
	     * 过滤POST中的多余字段
	     +----------------------------------------------------------
	     * @access protected
	     +----------------------------------------------------------
	     * @param array $data 数据
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */		
		protected function filterPost(&$data){
			//$column = array();			
			foreach($this->db->getColumn() as $val){
				$column[]=$val['Field'];		
			}
			foreach($data as $key => $val){
				if(!in_array($key,$column)){
					unset($data[$key]);			
				}
			}	
							
		}

		
	    /**
	     +----------------------------------------------------------
	     * 检查POST提交数据类型
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param array $data 数据
	     +----------------------------------------------------------
	     * @return bool
	     +----------------------------------------------------------
	     */		
		private function checkType(&$data){
			$i=0;
			foreach($data as $key => $val){
				$type = $this->db->getColumn('Type',$i++);
			
				if($type != gettype($val)){
					//throw new Exception('POST提交 数据类型错误!');
					return false;				
				}
			}
			return true;

					
		}
		
		
	    /**
	     +----------------------------------------------------------
	     * 添加方法
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param array $data 数据
	     +----------------------------------------------------------
	     * @return bool
	     +----------------------------------------------------------
	     */		
		public function add($data=array()){
			
 			if(empty($data) && !empty($this->db->columnValue)){
				$data=$this->db->columnValue;
			}else{
				$this->errorInfo="无数据";
				
				return false;
			}
						
			$PK = $this->db->getIndexName();
			if(isset($data[$PK])){
				unset($data[$PK]);
			
			}
			
			if( !$this->autoValidate($data,1)){
				$this->errorInfo="没有通过验证";
				return false;
			}
			$this->autoComplete($data);	
			//print_r($data);
			$this->filterPost($data);
			return $this->db->add($data);
		}
		
	    /**
	     +----------------------------------------------------------
	     * 修改方法
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param array $data 数据
	     +----------------------------------------------------------
	     * @return bool
	     +----------------------------------------------------------
	     */			
		public function save($data=array()){
			
 			if(empty($data) && !empty($this->db->columnValue)){
				$data=$this->db->columnValue;
				
			}
			$this->filterPost($data);
			
			//判断Model类是否存在
			if(class_exists($this->modelName)){

				$this->autoComplete($data,2);
				if( !$this->autoValidate($data,2) ){
					echo $this->errorInfo="没有通过验证";
					
					throw new Exception($this->errorInfo);
					return false;
				}

			}
			var_dump($data);
			return $this->db->save($data);
				
		}

		/*======================<END>=======================*/
		public function loadAuto(){
	      
		}
		/*====================<魔术方法>==================*/
		
		
	    /**
	     +----------------------------------------------------------
	     * 魔术方法get用于实现ORM
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $name 字段名
	     +----------------------------------------------------------
	     * @return array
	     +----------------------------------------------------------
	     */			
		public function __get($name){
			return $this->db->columnValue[$name];
			
		}
		
	    /**
	     +----------------------------------------------------------
	     * 魔术方法set用于实现ORM
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param array $data 数据
	     +----------------------------------------------------------
	     * @return bool
	     +----------------------------------------------------------
	     */			
		public function __set($name,$value){
			$this->db->columnValue[$name]=$value;	
			return true;		
		}		 
		
		
	    /**
	     +----------------------------------------------------------
	     * 魔术方法实现未定义方法的转向
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $method 方法名
	     * @param array $arg 参数列表
	     +----------------------------------------------------------
	     * @return mixn
	     +----------------------------------------------------------
	     */			
		public function __call($method,$arg){
			if(in_array(strtolower($method),array('field','table','where','order','limit','page','having','group','lock','distinct','mode'),true)) {
            			// 连贯操作的实现
            			$this->db->options[strtolower($method)] =   $arg[0];
            			//$this->db->$method($arg[0]);
            			return $this;
        		}
			$result = $this->createMethod($method,$arg);
        		return $result;

      	
		}
		
		
		
	    /**
	     +----------------------------------------------------------
	     * 远程调用其他对象的方法
	     +----------------------------------------------------------
	     * @access protected
	     +----------------------------------------------------------
	     * @param string $method 方法名
	     * @param array $arg 参数列表
	     +----------------------------------------------------------
	     * @return mixn
	     +----------------------------------------------------------
	     */			
		protected function createMethod($method,$arg){
	     		$rs = new ReflectionClass("DB");
	     		$class = $modelName=$this->modelName.'::';
	     		if($rs->hasMethod($method)){
	     			$class =  '$this->db->';	
	     		}
			for($i=0,$tmp = array();$i<count($arg);$i++) 
				$tmp[] = '$arg['.$i.']';
        		return eval('return '.$class.$method.'('.implode(",",$tmp).');');
		}
		/*======================<END>=======================*/
		
		
	}

	class M {

		public static function getIns($tableName){
			$modelName=$tableName."Model";
			return new $modelName($tableName);	
		}
		public static function newIns($tableName){
			$modelName=$tableName."Model";
			return new $modelName($tableName);	
		}

	}



	
?>
