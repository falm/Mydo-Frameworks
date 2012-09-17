<?php

	class Loader{

		
		public function __construct(){
			
		}
		public function LoadCore(){
			
			$this->setPath();
			//$error_reporting = error_reporting(0); 
			
			
			//spl_autoload_register(array('Load','loadTemplates'));
		
			spl_autoload_register(array('Loader','autoCore'));
			spl_autoload_register(array('Loader','loadController'));
			//spl_autoload_register(array('Load','loadModel'));
			//spl_autoload_register(array('Load','loadBee'));
			//spl_autoload_register(array('Load','loadPlugin'));
			//spl_autoload_register(array('Load','loadInterface'));
			//spl_autoload_call();
			//error_reporting($error_reporting);
			Debug::addLoadFile($_SERVER['DOCUMENT_ROOT'].MYDO_PATH.'Core/Loader.class.php');
		}

		public function Define(){}

		//设置include加载路径
		public function setPath(){
			//定义setpath 字符串多项间分隔符常量 WIN系统 ； Unix :
			
			define('PATH_PRI',PATH_SEPARATOR);
			set_include_path(MYDO_PATH.'Core/'.PATH_PRI.MYDO_PATH.'Smarty/libs/'.PATH_PRI.
			APP_PATH.'/lib/Model/'.PATH_PRI.MYDO_PATH.'Bee/'.PATH_PRI.
			MYDO_PATH.'interface/'.PATH_PRI.MYDO_PATH.'Plugin/'.PATH_PRI.
			MYDO_PATH.'Components/');
			
		}
		//自动加载框架核心类
		static function autoCore($className){
			
			
				
				
				 
				//$error_reporting = error_reporting(0); 
				//include_once(MYDO_PATH."Core/$className.class.php");
				//echo $className,get_include_path();
				//if(is_readable(get_include_path().$className.'class.php')){
					
					include_once($className.'.class.php');
					
					Debug::addLoadFile($_SERVER['DOCUMENT_ROOT'].MYDO_PATH."Core/$className.class.php");
				//}
				//error_reporting($error_reporting);  
				
				
			
		}
		/*
		static function loadTemplates($className){
			include_once(MYDO_PATH.'Smarty/libs/Smarty.class.php');
			Debug::addLoadFile($_SERVER['DOCUMENT_ROOT'].MYDO_PATH.'Smarty/libs/Smarty.class.php');
		}
		*/
		static function loadController($className){
			//$error_reporting = error_reporting(0); 
			if(defined('GROUP_NAME')){
				$file = APP_PATH.'/lib/Controller/'.GROUP_NAME.'/'.$className.'.class.php';
			}else{
				$file = APP_PATH.'/lib/Controller/'.$className.'.class.php';		
			}
			//if(is_readable($file)){
				include_once($file);
				$path = $_SERVER['DOCUMENT_ROOT'].$file;
			//.APP_PATH.'/lib/Controller/'.GROUP_NAME.'/'.$className.'.class.php';
				//Debug::addLoadFile($path);
			//}
			//error_reporting($error_reporting); 
		}
		/*
		static function	loadModel($className){
			include_once(APP_PATH.'/lib/Model/'.$className.'.class.php');
			Debug::addLoadFile($_SERVER['DOCUMENT_ROOT'].APP_PATH.'/lib/Model/'.$className.'.class.php');			
		}

		static function loadBee($className){
			include_once(MYDO_PATH.'Bee/'.$className.'.class.php');
			Debug::addLoadFile(MYDO_PATH.'/Bee/'.$className.'.class.php');				
		}
		static function loadPlugin($className){
			include_once(MYDO_PATH.'Plugin/'.$className.'.class.php');
			Debug::addLoadFile(MYDO_PATH.'/Plugin/'.$className.'.class.php');	
		}
		static function loadInterface($className){
			
			include_once(MYDO_PATH.'interface/'.$className.'.class.php');
			Debug::addLoadFile(MYDO_PATH.'/interface/'.$className.'.class.php');	
		}
		*/
		static function setLoadPath($path){
			set_include_path(get_include_path().PATH_PRI.$path);
		}
	}

?>
