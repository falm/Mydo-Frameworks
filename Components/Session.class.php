<?php
	class Session extends Request{
	
		public static setType(){
			self::$data = &$_SESSION;
		}
	}
?>