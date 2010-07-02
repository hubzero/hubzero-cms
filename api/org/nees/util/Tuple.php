<?php 

class Tuple {
	
  private $m_strName = "";
  private $m_strValue = "";
  private $m_strField1 = "";
  private $m_strField2 = "";
  
  /**
   * Constructor
   *
   */
  function __construct($p_strName, $p_strValue, $p_strField1, $p_strField2){
    $this->m_strName = $p_strName;
    $this->m_strValue = $p_strValue;
    $this->m_strField1 = $p_strField1;
    $this->m_strField2 = $p_strField2;
  }
  
  public function setName($p_strName){
  	$this->m_strName = $p_strName;
  }
	
  public function getName(){
  	return $this->m_strName;
  }
  
  public function setValue($p_strValue){
  	$this->m_strValue = $p_strValue;
  }
	
  public function getValue(){
  	return $this->m_strValue;
  }
  
  public function setField1($p_strField1){
  	$this->m_strField1 = $p_strField1;
  }
	
  public function getField1(){
  	return $this->m_strField1;
  }
  
  public function setField2($p_strField2){
  	$this->m_strField2 = $p_strField2;
  }
	
  public function getField2(){
  	return $this->m_strField2;
  }
}

?>