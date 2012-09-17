<?php


define('CTRL_OFFSET', 1);


/**
 +---------------------------------------------------------
 * Mydo Framework 路由类
 +---------------------------------------------------------
 * @package core
 * @category core
 * @author Falm <Mydo.Master@gmail.com>,hjj <hjj1992@gmail.com>
 +---------------------------------------------------------
 **/
 
	class Router implements SplSubject{
	
		
		private $path ='';	//PATH_INFO 路径
		private $rule = array(); //array('index'=>array('index','orm','year,month,day')); // 路由规则数组
		private $routeUrlArray	= array();	//通过解析后的 数组形式 PATH_INFO 路径
		public  $cmgArray = array();	//要路由到的 控制器，方法和分组 数组
		private $observers= array();	//观察者对象
		private $groupStatus = 0;	//分组状态
		
		const GET_OFFSET = 3;	//GET参数 在 PATH_INFO 数组中的偏移值
		const Control_OFFSET = 1;
		const Method_OFFSET = 2;
		
	    /**
	     +----------------------------------------------------------
	     * 构造函数
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public function __construct(){
			//获取PATH_INFO
			$this->path = empty($_SERVER['PATH_INFO']) ? null : $_SERVER['PATH_INFO'];
			//将PATH_INFO解析成数组
			$this->routeUrlArray = $this->getRouteUrlArray($this->path);
			
			//$this->rule = $this->getRule();
			//获取路由规则
			$this->getRule();
			
		}
		
	    /**
	     +----------------------------------------------------------
	     * 运行
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public function run(){
			
			//判断是否启用路由功能
			if(empty($this->rule)) return ;
			
			//设置分组状态
			$this->setGroupStatus();
			
			//循环匹配URL 和 路由规则
			foreach($this->rule as $key => $val){
				
				// 判断URL 是否 是默认控制器访问 
			//	if( !isset($this->routeUrlArray[GROUP_STATUS+self::Control_OFFSET]) ){
			//		continue;
			//	}
				
				
				if( strstr($key,'/') ){
					$domain = $this->routeUrlArray[GROUP_STATUS+self::Control_OFFSET].'/'.
					$this->routeUrlArray[GROUP_STATUS+self::Method_OFFSET];
					
					if($key == $domain){
						//echo $val[0],$val[1],BR;
						$this->setGet($val[2]);
						$this->setGroup($val[0]);
						
					
						$this->setControlMethod($val[0],$val[1]);
						$this->notify();break;
												
					}
					continue;
				}
				
				//判断路由是否和 访问路径向匹配
				if($key == $this->routeUrlArray[GROUP_STATUS + self::Control_OFFSET]){
					$this->setGet($val[2]);
					$this->setGroup($val[0]);
					$this->setControlMethod($val[0],$val[1]);
					//触发观察者事件
					$this->notify();break;
				}		
			}
		}
			
	    /**
	     +----------------------------------------------------------
	     * 设置GET参数
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $param GET参数字符串
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public function setGet($param){
			$param = explode(',',$param);
			foreach($param as $key => $val){
				
				if(strstr($val,':')){
					$get = explode(':',$val);
					$_GET[$get[0]] =$get[1]; 
				}else{
					$_GET[$val] = $this->routeUrlArray[GROUP_STATUS+self::GET_OFFSET+$key];
					

				}
			}
			
		}

	    /**
	     +----------------------------------------------------------
	     * 设置分组
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $param 分组字符串
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */	
		public function setGroup(&$param){
			$url = explode('.',$param);
			//$url 数组 中元素geshu 大于1 时 说明存在 分组路由 
			if(count($url) > 1){
				$this->cmgArray['group']=$url[0];
				$param = $url[1];
			}
		}

	    /**
	     +----------------------------------------------------------
	     * 设置 控制器名 和 方法名
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $c 控制器名
	     * @param string $m 方法名
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */	
		public function setControlMethod($c,$m){
			$this->cmgArray=array('controller'=>$c.'Controller','method'=>$m);
			//self::$cmgArray;
		}

	    /**
	     +----------------------------------------------------------
	     * 获取路由信息
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */	
		private function getRule(){
			@$rule = include(APP_PATH.'/config/routes.php');
			
			
			
			if(is_array($rule)){
				$this->rule = $rule;
				
			}elseif(is_callable($rule)){
				$rule($this);
			}else{
				$this->rule = false;
			}
			//return $rule;
		}

   		
   		 
	    /**
	     +----------------------------------------------------------
	     * 将URL解析成数组形式
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param string $url PATH_INFO 字符串
	     * @param string $piexl 分隔符
	     +----------------------------------------------------------
	     * @return array
	     +----------------------------------------------------------
	     */	   		 
		private function getRouteUrlArray($url,$piexl='/'){
			$str = explode($piexl,$url);
			return $str;    
	    }	
	    
	    
	    /**
	     +----------------------------------------------------------
	     * 设置分组状态
	     +----------------------------------------------------------
	     * @access private
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */		
	    private function setGroupStatus(){
	    	# code...
			$s=getConfItem('APP_GROUP_ARRAY');
			if(!$s){
				return 0;		
			}
			
			$t = !empty($this->routeUrlArray[1]);
			if(  $t && in_array(ucwords(strtolower($this->routeUrlArray[1])),$s)){
				//$this->group = ucwords(strtolower($this->urlArray[1]));
				$this->groupStatus = 1;
				define('GROUP_STATUS', 1);
				
			}else{
				//$this->group = $s['DEFAULT'];
				$this->groupStatus = 0;
				define('GROUP_STATUS', 0);
			}	    	
	    }


	    /**
	     +----------------------------------------------------------
	     * 添加观察者
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */	
		public function attach(SplObserver $ob){
			$this->observers[] = $ob;
		}
		
	    /**
	     +----------------------------------------------------------
	     * 删除观察者
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public function detach(SplObserver $ob){
			if($id = array_serach($this->observers,$ob,true)){
				unset($this->observers[$id]);
			}
		}
		
	    /**
	     +----------------------------------------------------------
	     * 触发观察者事件
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param 
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public function notify(){
			foreach($this->observers as $ob){
				$ob->update($this);
			}
		}	
		
	    /**
	     +----------------------------------------------------------
	     * 添加路由规则
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $key 路由关键字
	     * @param string $value 规则
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public function add($key,$rule){
				# code...
				$this->rule[$key] = $rule;		
		}
		
	    /**
	     +----------------------------------------------------------
	     * 添加RESTful路由规则
	     +----------------------------------------------------------
	     * @access public
	     +----------------------------------------------------------
	     * @param string $module RESETful 模块名
	     * @param string $controller 控制器名
	     * @param string $params GET参数控制列表
	     * @param string $group 分组名
	     +----------------------------------------------------------
	     * @return void
	     +----------------------------------------------------------
	     */			
		public function resource($module,$controller=null,$params='id',$group=''){
			# code...
			$controller = empty($controller) ? $module : $controller;
			
			foreach (array('index','show','edit','add','update','create','destroy' ) as $value) {
				
				$this->rule[$module.'/'.$value] = array($group.$controller,$value,$params);
			}
			$this->rule[$module] = array($group.$controller,'index',$params);
		}
		/*
		public static function resources($module,$controller=null,$params='id',&$array=NUll,$group=''){
			
			$controller = empty($controller) ? $module : $controller;
			$string = '';
			foreach (array('index','show','edit','add','update','create','destroy' ) as $value) {
				
				$rule[$module.'/'.$value] = array($group.$controller,$value,$params);
				//$string .= "'$module/$value' => array($group.$controller,$value,$params),"; 
			}
			$rule[$module] = array($group.$controller,'index',$params);
			//return $module=>array($group.$controller,'index',$params);
			//return $rule;
			$array = add_array($array,$rule);
		}
		*/
	}
?>
