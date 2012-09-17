<?php
/**
 +----------------------------------------------
 * Mydo Framework 调度器类
 +----------------------------------------------
 * @package Core
 * @category Core
 * @author Falm <Mydo.Master@gmail.com>,hjj <hjj1992@gmail.com>
 +----------------------------------------------
 **/
	class Dispatcher implements SplObserver{
		private $path;	//存放URL地址
		private $controller; //控制器名称
		private $method;	//方法名称
		private $indexLocation;	//入口文件在URL中的位置
		private $urlArray = array();	//数组形式URL地址
		private $group; //模块分组名称
		private $routeStatus = false; //路由状态
		
	    /**
	     +----------------------------------------------------------
	     * 构造方法
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $piexl URL 分隔符
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public function __construct($piexl='/'){
			
			$this->path = empty($_SERVER['PATH_INFO']) ? null : $_SERVER['PATH_INFO'];
			$this->urlArray = $this->getUrlArray($this->path,$piexl);
			$this->indexLocation = $_SERVER['SCRIPT_NAME'];
			
	      	
      
	    }
	    
	    /**
	     +----------------------------------------------------------
	     * 运行
	     +----------------------------------------------------------
	     *@static
	     * @access public
	     +----------------------------------------------------------
	     * @param array $cmgarray 调度路径数组
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			    
		public static function run($CMGArray=array()){
			$dispatch = new Dispatcher();
			$dispatch->dispatch($CMGArray);
		}
		
	    /**
	     +----------------------------------------------------------
	     * 调度方法
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */				
	    public function dispatch(){
			try{
				$groupStatus = $this->setGroup();
				if(empty($this->routeStatus)){
				
					$this->setGET($groupStatus);
					$this->setControlMethod($groupStatus);
							
				}/*
				else{
					foreach($CMGArray as $key => $val){
						$this->$key = $val;	
					}		
				}*/
	    	    $this->setDefault();
				// 如果没有设置 项目分组 $this->group = NULL 
				// 引入控制器类
				
				
	    	    //import("Controller.$this->group.$this->controller");
				//Get::start();
				$run = new $this->controller();
				$method = $this->method;
				//$method = 'Index';
				
				$run->$method();
	        
			}catch(Exception $e){
				echo $e->getMessage(),BR;
				die("Not find this web page !");
	      }
	    }
		
	    /**
	     +----------------------------------------------------------
	     * 观察者模式更新
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param SplSubject $r 获取被观察者的实例
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			    
		public function update(SplSubject $r){
			foreach($r->cmgArray as $key => $val){
				$this->$key = $val;
			}
			
			$this->routeStatus = true;
		}
	     
	    
	    /**
	     +----------------------------------------------------------
	     * 设置 控制器 和 方法 的值
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param int $group 是 分组 在 URL数组中的偏移值 为0时表示 使用默认分组或不分组
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			    
	    private function setControlMethod($group){
			//$group 是 分组 在 URL数组中的偏移值 为0时表示 使用默认分组或不分组 
			//$group = $this->setGroup();
			
			$this->controller = !empty($this->urlArray[1+$group])?ucwords(strtolower($this->urlArray[1+	$group]).'Controller'):'IndexController';
			$this->method = !empty($this->urlArray[2+$group]) ? strtolower($this->getExtends($this->urlArray[2+$group])) : 	'Index';
	      	
	    }

		
		
	    /**
	     +----------------------------------------------------------
	     * 设置项目分组
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return int 
	     +----------------------------------------------------------
	     */			
		private function setGroup(){
			$s=getConfItem('APP_GROUP_ARRAY');
			if(!$s){
				return 0;		
			}
			
			if(isset($this->group)){
				return 0;
			}
			
			$t = !empty($this->urlArray[1]);
			if(  $t && in_array(ucwords(strtolower($this->urlArray[1])),$s)){
				$this->group = ucwords(strtolower($this->urlArray[1]));
				return 1;
			}else{
				$this->group = $s['DEFAULT'];
				return 0;		
			}
				
		}

	   
	    /**
	     +----------------------------------------------------------
	     * 设置URL中的$_GET参数	
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param int $group 是 分组 在 URL数组中的偏移值 为0时表示 使用默认分组或不分组
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */		    
	    private function setGET($groupOffset){
	
			if(!isset($this->urlArray[3+$groupOffset]))
				return ;
			$len = count($this->urlArray);
			
			//++$group;
			for($i=3+$groupOffset ; $i<$len ; $i+=2){
				
				$_GET[$this->urlArray[$i]] = $this->getExtends($this->urlArray[$i+1]); 	 
			}      
	    }
	
	    	
	    /**
	     +----------------------------------------------------------
	     * //将URL解析成数组形式
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param string $url PATH_INFO信息
	     * @param string $piexl URL 分隔符
	     +----------------------------------------------------------
	     * @return array
	     +----------------------------------------------------------
	     */		    
	    private function getUrlArray($url,$piexl='/'){
			$str = explode($piexl,$url);
			return $str;    
	    }
	    
	    	    /**
	     +----------------------------------------------------------
	     * 获取URL上的扩展名
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param string $value PATH_INFO 中的一部分
	     +----------------------------------------------------------
	     * @return string
	     +----------------------------------------------------------
	     */	
	    private function getExtends($value='')
	    {
	    	# code...
	    	$params = explode('.',$value);
	    	
	    	return $params[0];
	    }

	    /**
	     +----------------------------------------------------------
	     * 获取index.php的本地位置
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param i
	     +----------------------------------------------------------
	     * @return string
	     +----------------------------------------------------------
	     */	
	    private function getIndexLocation(){
    

			$location = $_SERVER['SCRIPT_NAME'];
			
			if(getConfItem('REWRITE_ON')){
				$count = strrpos($_SERVER['SCRIPT_NAME'],'/');
				$location = substr($_SERVER['SCRIPT_NAME'],0,$count);
			}
			//return $_SERVER['SCRIPT_NAME'];
			return $location;
	    }
		
		
	    /**
	     +----------------------------------------------------------
	     * 设置系统默认值 以及 常量
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public function setDefault(){
			if($s = getConfItem('TMPL_DEFINE')){
				foreach($s as $key => $val){
					define($key,$val);			
				}
			}
			//设置Controller 加载路径
			Loader::setLoadPath(APP_PATH.'/lib/controller/'.$this->group.'/');
			
			define('__APP__',$this->getIndexLocation());
			//define('__APP__',$this->indexLocation);
			define('__CTRL__',__APP__.'/'.substr($this->controller,0,-10));
			define('__METHODS__',__CTRL__.'/'.$this->method);
			define('__TMPL__',APP_PATH."/templates/default");
			define('__GROUP__',__APP__.'/'.$this->group);
			define('CTRL_NAME',substr($this->controller,0,-10));
			define('METHOD_NAME',$this->method);
			define('GROUP_NAME',$this->group);
			
			define('__PUBLIC__',__APP__.'/public');
			
		}		
	}
?>
