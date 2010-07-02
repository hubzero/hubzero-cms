<?php 
  
  class ValidationException extends Exception{
  	
    public function getError(){
      $strReturn = "<h2 class='contentheading'>NEES Validation Error</h2>" .
                       "<div style='padding-top:20px;'>".$this->getMessage()."</div>";
      return $strReturn;
    }

    public function getEntityMessage($p_strEntity){
      return $p_strEntity." - ".$this->getMessage();
    }
  	
  }

?>