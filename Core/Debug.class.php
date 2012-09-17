<?php

	class Debug{
		
		public $runtime ; 
		public static $loadFileArray = array();	//加载文件列表
		public static $errorMsgList  = array();	 //错误信息列表
		private static $sqlCommandList = array(); //执行命令列表
		private static $old = null;
		private $status = true;
		
		function __construct(){
			$this->power();
			$this->runtime = new Runtime();
			$this->runtime->start();
			set_error_handler("setError");
		}
		
		public  function start(){
		
		}

		public function power(){

			$conf = getConf();
			if(array_key_exists('DEBUG_ON',$conf)){

				$this->status = $conf['DEBUG_ON'];
			}

			if(empty($this->status)){
				$this->__destruct();
			}

			
		}
		
		public  function showUseMemory(){
			 printf(' 内存使用量: %01.2f MB', memory_get_usage()/1024/1024);
		}

		public  function showDebugInfo(){

			if(!$this->status){
				return ;
			}
		
			$this->runtime->stop();
			if(!defined('WML_WORKS')){
			echo '<div id="trace" style="background:#E8E8E8;
										border:1px dashed  #000;
										width:500px;
										margin:10px;
										padding:10px;">';
			}else{
			echo '<card id="debug" title="调试信息" > <p>';
			}
			echo '<br/><h4>调试信息：</h4>';
			echo $this->showUseMemory(),'<br/>
			运行时间：',$this->runtime->spent(),'(秒).<br/>',
			'加载文件',count(self::$loadFileArray),'个<br/>',
			self::foreachLoadFile(),'<br/>',
			'<h5>错误信息：</h5>',
			self::eachErrorMsg();
			echo '</div>';

		}


		public function showDebugInfoWml(){
			
			if(!$this->status){
				return ;
			}
		
			$this->runtime->stop();
			echo '<card id="debug" title="调试信息" > <p>';
			echo '<br/><b>调试信息：</b>';
			echo $this->showUseMemory(),'<br/>
			运行时间：',$this->runtime->spent(),'(秒).<br/>',
			'加载文件',count(self::$loadFileArray),'个<br/>',
			self::foreachLoadFile(),'<br/>',
			'<b>错误信息：</b>',
			self::eachErrorMsg(),BR,
			'<b>执行的命令：</b>',BR,
			self::eachCommandList();
			echo '</p> </card>';			
		}

		static function startErrorCover(){
			
			self::$old = error_reporting();  
			error_reporting(0);  
		}

		static function stopErrorCover(){
			error_reporting(self::$old);
		}

		static function addLoadFile($lfa){
			
			array_push(self::$loadFileArray,$lfa);
		}
		
		//添加执行的SQL语句到  执行命令列表
		static function addSqlCommand($sql='')
		{
			array_push(self::$sqlCommandList,$sql);
		}
		
		//添加错误信息到 错误信息列表
		static function addErrorMsg($msg=''){
			# code...
			array_push(self::$errorMsgList,$msg);
		}
		//遍历输出框架所加载的文件
		static function foreachLoadFile(){
			foreach(self::$loadFileArray as $key => $val){
				echo "[$key] =>",$val,BR;
			}
		}
		
		static function eachCommandList(){
			
			foreach (self::$sqlCommandList as $key => $value) {
				
				echo "[$key]=>",$value,BR;
			}			
			
		}
		
		static function eachErrorMsg(){
			
			foreach (self::$errorMsgList as $key => $value) {
				
				echo "[$key]=>",$value,BR;
			}
		}
		public function __destruct(){
			
			return;
		}
	}
	
	function setError($errno ,$errstr,$errfile,$errline ){
		$msg = "错误号：$errno , 提示信息：$errstr , 发生错误的文件：$errfile , 行数：$errline";
		Debug::addErrorMsg($msg);
			
	}	

?>
