<?php

	interface Routers{
		
		
		
		public function run();

		public function setGet($param);

		public function setGroup();
	
		public function setControlMethod($controller,$method);
	}


?>
