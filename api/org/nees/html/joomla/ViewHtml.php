<?php 

  class ViewHtml{
  	
  	 /**
      * For some reason, Joomla drops the request uri.
      * Use the function below to insert the uri into the page links.
      * 
      * @param $p_strComponentAlias - the component alias
      * @param $p_strRequestUri - $_SERVER['REQUEST_URI'] 
      * @param $p_strSubject - the string to manipulate
      * @return the cleaned links
      */
  	 public static function fixPaginationLinks($p_strComponentAlias, $p_strRequestUri, $p_strSubject){

  	   if(strpos($p_strRequestUri, "?limit") > 0){
  	     $iLimit = strpos($p_strRequestUri, "?limit");
  	   }elseif(strpos($p_strRequestUri, "&limit") > 0){
  	   	 $iLimit = strpos($p_strRequestUri, "&limit");
  	   }
  	   
  	   $strFind = "/".$p_strComponentAlias."/?";
  	   
//  	   echo "<br>find0: ".$strFind;
//  	   echo "<br>uri0: ".$p_strRequestUri;
  	   $iStart = strpos($p_strRequestUri, "&start");
  	   $strRequestUri = substr($p_strRequestUri, 0, $iStart);
//  	   echo "<br>uri1: ".$strRequestUri;
  	   
  	   $iLimit = strpos($p_strRequestUri, "&limit");
  	   $strRequestUri = substr($p_strRequestUri, 0, $iLimit);
//  	   echo "<br>uri2: ".$strRequestUri;
  	   
//  	   if($iStart === true){
//  	     $strRequestUri = substr($p_strRequestUri, 0, $iStart);
//  	   }elseif($iLimit===true && $iStart==false){
//  	     $strRequestUri = substr($p_strRequestUri, 0, $iLimit);
//  	   }else{
//  	   	 $strRequestUri = $p_strRequestUri;
//  	   }
  	   //echo "<br>uri: ".$strRequestUri;
  	   
  	   if(strlen($strRequestUri)==0){
  	   	 $strRequestUri = $p_strRequestUri;
  	   }
  	   
	   $strReplace = $strRequestUri."&";
	   $strPageHtml = str_replace($strFind, $strReplace, $p_strSubject); 	
	   //return $strPageHtml;
  	 }
  }

?>