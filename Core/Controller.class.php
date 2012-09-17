<?php

	class Controller {

		protected $tpl ;
		
    
	   public function __construct(){
			//加载Smarty
			if(getConfItem('SMARTY_ON')){
	    		$this->loadSmarty();
			}
	   }
		//向模板输出变量
		public function assign($name,$value){
			
			$this->tpl->assign($name,$value);
		}
		//显示模板
		public function display($url=''){
			if(empty($url))
				$url=METHOD_NAME.'.html';
			try{
				$url = strstr($url,'/') ? $url : CTRL_NAME."/$url";
				$this->tpl->display(GROUP_NAME.'/'.$url);
			}catch(SmartyException $e){
				die($e->getMessage());			
			}
		}
		//显示错误信息
		protected function error($value){
		      $this->tpl->assgin('Error',$value);
		      $this->tpl->display('/public/error.html');
		}
		//显示成功信息
		protected function success($value){
		      $this->tpl->assgin('success',$value);
		      $this->tpl->display('/public/success.html');
		}
		//URL重定向
		protected function redirect($url){
			
			//echo "<SCRIPT language=\"javascript\" >Location.href='$url'</SCRIPT>";
			echo 'ss';
			echo CTRL_NAME;
			sleep(5);	
			$url = $_SERVER['SCRIPT_NAME'].$url;
			header("Location:$url");
		}

		//实例化Smarty模板引擎
		protected function loadSmarty(){
			
			//include_once(MYDO_PATH.'Smarty/libs/Smarty.class.php');
			try{
				$this->tpl = new Smarty();
				$this->tpl->template_dir = __TMPL__;
				$this->tpl->compile_dir = APP_PATH.'/templates_c/';
				$this->tpl->config_dir = APP_PATH.'/config/';
				$this->tpl->cache_dir = APP_PATH.'/cache/';
				$this->tpl->left_delimiter = '{';
				$this->tpl->right_delimiter = '}';	
				
			}catch(Exception $e){
				exit($e->getMessage());		
			}									
		}
		public function __call($method,$arg){
			
		}
				

#		public function __call($method){
#			$this->_empty($method);
#		}
#		
#		protected function _empty($method){
#			echo "ss";
#		}
#		
		
	}

?>
