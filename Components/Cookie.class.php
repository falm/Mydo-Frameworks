<?php
	class Cookie extends Request{
		
		public static function setType(){
			self::$data = & $_COOKIE;
		}
		public static function set($name,$value){
			setcookie($name,$value);
		}
		
		public static function del($name){
			unset(self::$data[$name]);
		}
	}
?>