<?php 
  
  class AuthenticationException extends Exception{
  	
  	public function getError(){
  	  $strReturn = "<h2 class='contentheading'>NEES Authentication Error</h2>" .
  	    		   "<div style='padding-top:20px;'>".$this->getMessage()."</div>";
  	  return $strReturn;
  	} 
  	
  }

?>