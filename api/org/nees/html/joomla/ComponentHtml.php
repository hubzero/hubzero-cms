<?php 

  class ComponentHtml{
  	
    /**
     * Returns a rendered view of a module.
     */
    public static function getModule($p_oModuleName){
	  $oModule = &JModuleHelper::getModule($p_oModuleName);
	  return JModuleHelper::renderModule($oModule);
    }
  	
  }

?>