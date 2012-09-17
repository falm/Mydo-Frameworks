<?php
  
	class Route {
  
		private $path;	//存放URL地址
		public $controller; //控制器名称
		public $method;	//方法名称
		private $indexLocation;	//入口文件在URL中的位置
		private $routeUrlArray = array();	//数组形式URL地址
		private $objectDir;	//项目目录
		private $group; //模块分组名称
    
		public function __construct($objectDir,$piexl='/'){
			//Debug::startErrorCover();
			$this->path = empty($_SERVER['PATH_INFO']) ? null : $_SERVER['PATH_INFO'];
			$this->routeUrlArray = $this->getRouteUrlArray($this->path,$piexl);
			//$this->indexLocation = $this->getIndexLocation();//$this->routeUrlArray
			$this->indexLocation = $_SERVER['SCRIPT_NAME'];
			$this->objectDir = $objectDir;
	      	//Debug::stopErrorCover();
      
	    }
	    //加载指定控制器方法
	    public function load(){
			try{
		
				$this->setGET();
				$this->setUrl();
		
				$this->plugin();
	    	    $this->setDefault();
				// 如果没有设置 项目分组 $this->group = NULL 
				// 引入控制器类
				//$this->setRouteUrl();
				
	    	    //import("Controller.$this->group.$this->controller");
				Get::start();

				$run = new $this->controller();
				$method = $this->method;
				$run->$method();
	        
			}catch(Exception $e){
				echo $e->getMessage(),BR;
				die("NOt find this web page !");
	      }
	    }
	
		private function setRouteUrl(){
			//Debug::startErrorCover();
	    	@$result = include(APP_PATH.'/config/routes.php');
			if(!getConfItem('ROUTE_ON') || !$result){
				return ;
			}


		
			foreach($result as $val){
			
				if(isset($val['CTRL'])){
					$group = explode('.',$val[0]);
					
					$val[0] =	$this->group == $group[0] ? $group[1] : $val[0] ; 
				
					$_GET[$val[2]] = CTRL_NAME;
					$this->controller = $val[0].'Controller';
					$this->method = $val[1];
					break;
				}
				if(in_array(CTRL_NAME,$val)){
					
					$_GET[$val[2]] = $this->method;
					$this->method = $val[1];
					break;
				}
			}
    	
	    }
	    //设置 控制器 和 方法 的值	返回:Void 
	    private function setUrl(){
			//$group 是 分组 在 URL数组中的偏移值 为0时表示 使用默认分组或不分组 
			$group = $this->setGroup();
			$this->controller = !empty($this->routeUrlArray[1+$group])?ucwords(strtolower($this->routeUrlArray[1+	$group]).'Controller'):'IndexController';
			$this->method = !empty($this->routeUrlArray[2+$group]) ? strtolower($this->routeUrlArray[2+$group]) : 	'Index';
	      	
	    }
		//设置项目分组
		private function setGroup(){
			$s=getConfItem('APP_GROUP_ARRAY');
			if(!$s){
				return 0;		
			}
			$t = !empty($this->routeUrlArray[1]);
			if(  $t && in_array(ucwords(strtolower($this->routeUrlArray[1])),$s)){
				$this->group = ucwords(strtolower($this->routeUrlArray[1]));
				return 1;
			}else{
				$this->group = $s['DEFAULT'];
				return 0;		
			}
				
		}
	    //设置URL中的$_GET参数	返回:Void
	    private function setGET(){
	
			if(!isset($this->routeUrlArray[3]))
				return ;
			$len = count($this->routeUrlArray);
	
			for($i=3;$i<$len;$i+=2){
				@$_GET[$this->routeUrlArray[$i]] = $this->routeUrlArray[$i+1]; 	 
			}      
	    }
	    //将URL解析成数组形式		返回:数组
	    private function getRouteUrlArray($url,$piexl='/'){
			$str = explode($piexl,$url);
			return $str;    
	    }
		//获取入口文件在URL中的位置	返回:整数
	#    private function getIndexLocation($route=array()){
	#      foreach($route as $key => $val){
	#        $ss=substr($val,-4,4);
	#        if($ss=='.php'){
	#          unset($route);
	#          return $key;
	#        }
	#      }      
	#    }

	    private function getIndexLocation(){
    
			$count = strrpos($_SERVER['SCRIPT_NAME'],'/');
			$route = substr($_SERVER['SCRIPT_NAME'],0,$count);
	
			return $route;
	    }
		//设置系统默认值		返回:Void
		public function setDefault(){
			if($s = getConfItem('TMPL_DEFINE')){
				foreach($s as $key => $val){
					define($key,$val);			
				}
			}
			
			define('__APP__',$this->getIndexLocation());
			//define('__APP__',$this->indexLocation);
			define('__CTRL__',$this->indexLocation.'/'.substr($this->controller,0,-10));
			define('__METHODS__',__CTRL__.'/'.$this->method);
			define('__TMPL__',APP_PATH."/templates/default");
			define('CTRL_NAME',substr($this->controller,0,-10));
			define('METHOD_NAME',$this->method);
			define('GROUP_NAME',$this->group);
			
			define('__PUBLIC__',__APP__.'/public');
			
		}
	
		private function plugin(){
	
			if(getConfItem('PLUGIN_ON')){
				
				$array = getConfItem('PLUGIN_REGISTER');
				foreach($array as $key => $val){

					$class = new ReflectionClass($val);
					$this->pluginRun($class->newinstance());
	
				}
			}
		}
		private function pluginRun(Routers $class){
			
			$array = $class->run();
			
			$this->setControl($array[0]);
			$this->setMehtod($array[1]);
		}
		public function setControl($c){
			if(!empty($c)){
				$this->controller = $c.'Controller';
			}
		}
		public function setMehtod($m){	
			if(!empty($m)){
				$this->method = $m;
			}
		}
		function __destruct(){
			Debug::stopErrorCover();
		} 
	}
	  
	
?>
