<?php 

require_once 'libraries/joomla/application/module/helper.php';

  class ComponentHtml{
  	
    /**
     * Returns a rendered view of a module.
     */
    public static function getModule($p_oModuleName){
      $oModule = &JModuleHelper::getModule($p_oModuleName);
      return JModuleHelper::renderModule($oModule);
    }

    public static function showError($p_strMessage){
      return "<p class='error'>$p_strMessage</p>";
    }
  	
  }

?>