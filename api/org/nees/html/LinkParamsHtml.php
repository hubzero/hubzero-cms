<?php 

/**
 * LinkParamsHtml is a utility class for passing long 
 * parameters in an html anchor.  For example, rather 
 * than typing a=1&b=2&c=3&d=4...&z=n, one can simply 
 * append the key/value pairs into an array.  When 
 * it's time to concatenate the parameters to a URL, 
 * simply call the toHtml() function.
 * 
 * @author gemezmarshall
 *
 */
class LinkParamsHtml{

  private $m_oParamArray; 
  private $m_oKeyValueArray; 
	
  /**
   * Default constructor
   */
  public function __construct() {
    $this->m_oParamArray = array();
    $this->m_oKeyValueArray = array();
  }  
  
  /**
   * Append the key/value pairs of a link to the member 
   * variable $m_oParamArray.  The array will have the following:
   * 
   * a=1, b=2, c=3, ... z=n
   */
  public function append($p_strKey, $p_strValue){
  	array_push($this->m_oParamArray, $p_strKey."=".$p_strValue);
  	
  }
  
  /**
   * 
   *
   */
  public function store($p_strKey, $p_strValue){
  	$this->m_oKeyValueArray[$p_strKey] = $p_strValue;
  }
  
  /**
   * Returns the key/value pairs.
   *
   */
  public function getLinkParams(){
  	return $this->m_oParamArray;
  }
  
  /**
   * Returns the value of the specified key.
   *
   */
  public function get($p_strKey){
  	return $this->m_oKeyValueArray[$p_strKey];
  }
  
  /**
   * Returns the params formated for a link if 
   * member variable $m_oParamArray is not emtpy.
   * Otherwise, return a hash. 
   * 
   * a=1&b=2&c=3 ... &z=n
   */
  public function toHtml(){
  	$strReturn = "#";
  	if(!empty($this->m_oParamArray)){
  	  $strReturn = implode("&", $this->m_oParamArray);
  	}
  	return $strReturn;
  }
}

?>