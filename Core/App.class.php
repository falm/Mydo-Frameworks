<?php

/**
 +---------------------------------------------------------
 * Mydo Framework 应用程序类 执行应用过程管理
 +---------------------------------------------------------
 * @package Core
 * @category Core
 * @author Falm,hjj
 +---------------------------------------------------------
 **/
	class App{

		
		public static $de;

		
	    /**
	     +----------------------------------------------------------
	     * 启动框架 运行项目
	     *
	     +----------------------------------------------------------
	     * @static
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */		
		public static function run(){

			try{
				
							
				self::loadSysClass();	//加载系统类文件	
				self::$de = new Debug();		
				self::createDir();	//创建项目目录			
				//$R = self::getRoute();	//获取Route类实例

				$r = new Router();	//实例化路由类
				$d = new Dispatcher();	//实例化调度器类
				$r->attach($d);
				$r->run();
				$d->dispatch();
				
				//Dispatcher::run($R->run());
				self::destruct();
			}catch(Exception $e){
				die($e->getMessage());			
			}
			
		}

		
	    /**
	     +----------------------------------------------------------
	     * 创建项目目录
	     *
	     +----------------------------------------------------------
	     * @static
	     * @access private
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		private static function createDir(){

			
			self::autoCreateDir(APP_PATH.'/lib/Model');
			self::autoCreateDir(APP_PATH.'/lib/Controller');
			self::autoCreateDir(APP_PATH.'/Command');
			self::autoCreateDir(APP_PATH.'/templates/default');
			self::autoCreateDir(APP_PATH.'/templates_c');
			self::autoCreateDir(APP_PATH.'/cache');
			self::autoCreateDir(APP_PATH.'/config');
			


			self::copyTemp("temp/config.php","\config\config.php");
			
			
			$group = getConfItem('APP_GROUP_ARRAY');
			
			if($group){
				foreach($group as $key => $val){
					self::autoCreateDir(APP_PATH.'/lib/Controller/'.$val);		
				}
				$def = $group['DEFAULT'];
			}
			
			$cpath = empty($group) ? "\lib\Controller\IndexController.class.php" : "\lib\Controller\\$def\IndexController.class.php";
				


			self::copyTemp("temp/IndexController.class.php",$cpath);
			

		}
	    /**
	     +----------------------------------------------------------
	     * 拷贝临时文件
	     *
	     +----------------------------------------------------------
	     * @static
	     * @access private
	     +----------------------------------------------------------
	     * @param string $source 源地址
	     * @param string $target 目的地址
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */	
		private static function copyTemp($source,$target){
		
			if(!file_exists(APP_PATH.$target)){
				
				
				$result = copy(MYDO_PATH.$source,APP_PATH.$target);//"\lib\Controller\IndexController.class.php");
				if(!$result){
					throw new Exception('ERROR ! file not success copy');
				}
			}			
		}
		
	    /**
	     +----------------------------------------------------------
	     * 自动创建目录
	     *
	     +----------------------------------------------------------
	     * @static
	     * @access private
	     +----------------------------------------------------------
	     * @param string $dir 目录名
	     +----------------------------------------------------------
	     * @return Bool
	     +----------------------------------------------------------
	     */			
		private static function autoCreateDir($dir=null){
			if (! $dir) {
  				echo '目录名称不能为空!';
   				return false;
			}
			$arrDir = explode ( '/', $dir );
			$dirPath = '.';
			foreach ( $arrDir as $value ) {
   				if ($value) {
    					$dirPath .= '/' . $value;
    					if (! is_dir ( $dirPath )) {
     					mkdir ( $dirPath );
   					}
   				}
			}
			return true;
		}


		
	    /**
	     +----------------------------------------------------------
	     * 加载函数库文件
	     *
	     +----------------------------------------------------------
	     * @static
	     * @access private
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		private static function loadSysClass(){
			
			if(file_exists(APP_PATH."/Command/functions.php"))
				include(APP_PATH."/Command/functions.php");
			include(MYDO_PATH.'/function/functions.php');
							
			
		}
	    /**
	     +----------------------------------------------------------
	     * 框架析构方法
	     *
	     +----------------------------------------------------------
	     * @static
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		static function destruct(){
			if(!defined('WML_WORKS')){
				self::$de->showDebugInfo();
			}else{
				self::$de->showDebugInfoWml();
			}

		}
			
	}

?>
