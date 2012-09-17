<?php


	abstract class Request {
		protected static $data = array();
		
		protected static function setType(){}
		public static function load(){
			if(empty(static::$data)){
				
				static::setType();
			}
		}
		public static function get($name){
			self::load();
			if(array_key_exists($name,static::$data)){
				return static::$data[$name];
			}
		}
		public static function set($name,$value){
			self::load();
			static::$data[$name] = $value;
		}
		public static function exists($name){
			self::load();
			if(array_key_exists($name,static::$data)){		

				return true;	
			}
			return false;
		}
	}

?>
