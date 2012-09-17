<?php
	class Runtime
	{ 
    	private $StartTime = 0; 
    	private $StopTime = 0; 
 
	    function get_microtime() 
	    { 
	        list($usec, $sec) = explode(' ', microtime()); 
	        return ((float)$usec + (float)$sec); 
	    } 
	 
	    function start() 
	    { 
	        $this->StartTime = $this->get_microtime(); 
	    } 
	 
	    function stop() 
    	{ 
    	    $this->StopTime = $this->get_microtime(); 
    	} 
 	
    	function spent($seconds=1) 
    	{ 		
			switch($seconds)
			{
				case 1:$info = $this->StopTime - $this->StartTime;break;
				case 0:$info = round(($this->StopTime - $this->StartTime) * 1000, 1); break;
			}
    	    
			return $info;
    	} 
 
	}

?>
