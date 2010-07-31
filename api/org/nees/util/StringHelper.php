<?php 

  class StringHelper{

    const EMPTY_STRING = "";
    
  	/**
  	 * Checks if a given string begins with a specified search term.
  	 * @param $p_strInputString - String to examine
  	 * @param $p_strSearchString - Query string
  	 * @see http://snippets.dzone.com/posts/show/2644
  	 */
	public static function beginsWith($p_strInputString, $p_strSearchString){
      return (strncmp($p_strInputString, $p_strSearchString, strlen($p_strSearchString)) == 0);
	}
	
	/**
	 * http://www.geekpedia.com/code50_Check-if-a-string-ends-with-another-string.html
	 */
    function endsWith($p_strFullParam, $p_strEndStrParam){
      $p_strFull = strtolower($p_strFullParam);
      $p_strEndStr = strtolower($p_strEndStrParam);

      // Get the length of the end string
      $strLength = strlen($p_strEndStr);
   
      // Look at the end of FullStr for the substring the size of EndStr
      $strFullEnd = substr($p_strFull, strlen($p_strFull) - $strLength);
   
      // If it matches, it does end with EndStr
      return $strFullEnd == $p_strEndStr;
    }
    
	/**
	 * Checks if a given string contains a specified search term.
	 * @param $p_strSubject - String to examine
  	 * @param $p_strSearchString - Query string
	 */
	public static function contains($p_strSubject, $p_strSearchString){
	  $strPattern = "/$p_strSearchString/";
	  return preg_match($strPattern, $p_strSubject);
	}
	
    public static function hasString($p_strInputString, $p_strSearchString){
      $strPattern = "/$p_strSearchString/";
	  return preg_match($strPattern, $p_strInputString);
	}
	
  /** 
   * Cut string to n symbols and add delim but do not break words. 
   * 
   * Example: 
   * <code> 
   *  $string = 'this sentence is way too long'; 
   *  echo neat_trim($string, 16); 
   * </code> 
   * 
   * Output: 'this sentence is...' 
   * 
   * @access public 
   * @param string string we are operating with 
   * @param integer character count to cut to 
   * @param string|NULL delimiter. Default: '...' 
   * @return string processed string 
   * @see http://www.justin-cook.com/wp/2006/06/27/php-trim-a-string-without-cutting-any-words/
   **/ 
    public static function neat_trim($strInput, $iCharacterCount, $strDelimiter='...') {
      $iLength = strlen($strInput);
      if ($iLength > $iCharacterCount) {
        $strPattern = '/(.{' . $iCharacterCount . '}.*?)\b/';
        preg_match($strPattern, $strInput, $matches);
        if(empty($matches)){
          $strReturn = self::EMPTY_STRING;
          $strInputArray = explode(" ", $strInput);
          $iWord = 0;
          while($iWord < 25){
            $strReturn .= $strInputArray[$iWord]." ";
            ++$iWord;
          }
          if(sizeof($strInputArray) > 25){
            $strReturn .= $strDelimiter;
          }
          return $strReturn;
        }
        return rtrim($matches[1]) . $strDelimiter; 
      }else { 
        return $strInput; 
      } 
    }
  	
    /**
     * @return true if string has text.  false otherwise.
     *
     */
    public static function hasText($p_strInputString){
      if(!$p_strInputString || $p_strInputString==""){
      	return false;
      }
      
      return true;
    }
    
  }

?>