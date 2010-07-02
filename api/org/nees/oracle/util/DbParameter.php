<?php 

  class DbParameter{
  	
    public $m_sName; 
    public $m_oValue;
  	
        
    public function __construct($p_sName, $p_oValue) {
      $this->m_sName = $p_sName;
      $this->m_oValue = $p_oValue;
    }
    
    public function setName($p_sName){
      $this->m_sName = $p_sName; 
    }
    
    public function getName(){
      #echo "getName=".$this->m_sName." ";
      return $this->m_sName;
    }
    
    public function setValue($p_oValue){
      $this->m_oValue = $p_oValue; 
    }
    
    public function getValue(){
      #echo "getValue=".$this->m_oValue." ";
      return $this->m_oValue;
    }
    
  }

?>
