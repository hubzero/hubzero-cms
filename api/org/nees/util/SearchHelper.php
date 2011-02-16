<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SearchHelper{

  static $m_strArticleArray = array(0=>"a", 1=>"an", 2=>"the");
  static $m_strPrepositionArray = array(0=>"a", 1=>"at", 2=>"in", 3=>"by", 4=>"but", 5=>"for", 6=>"of", 7=>"on", 8=>"so", 9=>"to", 10=>"with");

  public static function isArticle($p_strKeyword){
    $iArrayIndex = array_search($p_strKeyword, self::$m_strArticleArray);
    if($iArrayIndex > 0 || $iArrayIndex===0){
      return true;    
    }
    return false;
  }

  public static function isPreposition($p_strKeyword){
    $iArrayIndex = array_search($p_strKeyword, self::$m_strPrepositionArray);
    if($iArrayIndex > 0 || $iArrayIndex===0){
      return true;
    }
    return false;
  }
}

?>
