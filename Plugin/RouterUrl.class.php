<?php

	class RouterUrl implements Routers{


		
		private $path ='';
		private $rule = array('index'=>array('index','orm','year,month,day'));
		private $routeUrlArray = array();
		public function __construct(){
			$this->path = empty($_SERVER['PATH_INFO']) ? null : $_SERVER['PATH_INFO'];
			$this->routeUrlArray = $this->getRouteUrlArray($this->path);
			
			$this->rule = $this->getRule();
			
		}

		public function run(){
			if(empty($this->rule)) return ;
			foreach($this->rule as $key => $val){
				if($key == $this->routeUrlArray[1]){
					$this->setGet($val[2]);
					
					return $this->setControlMethod($val[0],$val[1]);break;
				}		
			}
		}
			
		public function setGet($param){
			$param = explode(',',$param);
			foreach($param as $key => $val){
				//echo $key,':',$val,BR;
				$_GET[$val] = $this->routeUrlArray[2+$key];	
			}
			
		}

		public function setGroup(){}

		public function setControlMethod($c,$m){
		
			return array($c,$m);
		}

		private function getRule(){
			@$rule = include(APP_PATH.'/config/routes.php');
			if(!is_array($rule)){
				$rule = false;
			}
			
			return $rule;
		}

   		 //将URL解析成数组形式		返回:数组
		private function getRouteUrlArray($url,$piexl='/'){
			$str = explode($piexl,$url);
			return $str;    
	    }
	
	}
	

?>
