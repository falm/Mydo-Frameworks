<?php

	class Get extends Request{
		public static function setType(){
			
			self::$data = &$_GET;
			
		}
	}
?>
