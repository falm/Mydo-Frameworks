<?php

	function import($path){

			if(strstr($path,'$')){
				$path = str_replace('$',MYDO_PATH,$path);
				$path=strtr($path,'.','/');
				require_once($path.'.class.php');
				Debug::addLoadFile($path.'.class.php'); // 将加载的文件路径 添加到文件加载数组中
			}else{
				$path=strtr($path,'.','/');
				$paths=APP_PATH.'/lib/'.$path.'.class.php';
				//清除文件状态缓存
				//clearstatcache();
				$error_reporting = error_reporting(0); 
				
				$bool=include_once($paths);
				Debug::addLoadFile($paths); // 将加载的文件路径 添加到文件加载数组中
				if(!$bool){
					throw new Exception("Not fond this file");
				}

				error_reporting($error_reporting); 
			}

	
	}
	

	//快捷创建模型函数	返回:对象实例	
	function M($tableName){
		$modelName=$tableName."Model";
		if(!file_exists(APP_PATH.'/lib/Model/'.$modelName.'.class.php')){//!class_exists($modelName)){//
				$modelName='Model';
		}
		return new $modelName($tableName);
	}
	//调用配置文件函数	返回:数组
	function getConf(){
		
		return include(APP_PATH.'/config/config.php');				
	}
	//获取配置文件 的配置项
	function getConfItem($key){
		$data = getConf();
		if(!isset($data[$key]))
			return false;
		return $data[$key];
	}
	//显示内存使用量函数
	function showUseMemory(){
		 printf(' memory usage: %01.2f MB', memory_get_usage()/1024/1024);		
	}
	//显示页面运行时间函数
	function getmicrotime(){ 
		list($usec, $sec) = explode(" ",microtime()); 
		return ((float)$usec + (float)$sec); 
    }
	
	

	
?>
