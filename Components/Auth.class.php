<?php

	class Auth{
		protected static $_mode = 'SESSION';
		public static function isLoginIn(){
			$mas = "$_{$_mode}['user']";
			if(isset($$mas){
				return true;
			}
			return false;
		}
		
		public static function loginOut(){
			$mas = "$_{$_mode}['user']";
			unset($$mas);
		}
		
		public static function loginMode(){}
		
		public static function mode($mode){
			self::$_mode=$mode;
			return self;
		} 
	}
?>