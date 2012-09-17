<?php

	class Post extends Request{
		
		private static function setType(){
			
			self::$data = &$_POST;
			
		}
	}
?>

