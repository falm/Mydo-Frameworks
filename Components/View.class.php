<?php

/**
 +----------------------------------------------
 * View 模板类
 +----------------------------------------------
 * @package Components
 * @category Components
 * @author Falm,hjj
 +----------------------------------------------
 **/

	class View{
		                                                                                                                                                                                                                                                                            
		const IS_PUBLIC = true;
		private static $view = array();
		
	    /**
	     +----------------------------------------------------------
	     * 向模板添加变量
	     *
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $key 变量名
	     * @param mixn $val 变量值
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */				
		public static function assign($key,$val){
			self::$view[$key] = $val; 
		}
		
	    /**
	     +----------------------------------------------------------
	     * 向模板添加变量
	     *
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $key 变量名
	     * @param mixn $val 变量值
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public static function show($path,$isPublic = false){
			ob_start();
			//$view = &self::$view;
			extract(self::$view);
			//self::$view = array();
			
			$paths = empty($isPublic) ?  __TMPL__.'/'.GROUP_NAME.'/'.$path.'.php' :  __TMPL__.'/public/'.$path.'.php' ;
					
			include $paths;
			$content = ob_get_clean();
			echo $content;
		}

	    /**
	     +----------------------------------------------------------
	     * 网页重定向
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $url 地址
	     * @param string $info 提示信息
	     * @param int $time 跳转时间
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */	
		public static function redirect($url,$info,$time=4){

			$error_reporting = error_reporting(0);
			$url = $_SERVER[SCRIPT_NAME].'/'.$url;
			error_reporting($error_reporting);
			//header("Location:$url");
			header("refresh:$time;url=$url");
			echo $info,BR,$time,'秒后跳转';
		}

	    /**
	     +----------------------------------------------------------
	     * 生成成功页面
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $info 提示信息
	     * @param string $target 跳转目标
	     * @param int $wait 等待时间
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */	
		public static function success($info,$target,$wait=5){
			
			self::assign('info',$info);
			self::assign('target',$target);
			self::assign('wait',$wait);
			self::show('success',self::IS_PUBLIC);
			
		}
		
	    /**
	     +----------------------------------------------------------
	     * 生成失败页面
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $info 提示信息
	     * @param string $target 跳转目标
	     * @param string $errorCode 错误代码
	     * @param int $wait 等待时间
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public static function error($info,$target,$errorCode,$wait=5){
			self::assign('info',$info);
			self::show('error',self::IS_PUBLIC);
		}
	}
?>
