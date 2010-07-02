<?php 

  class AuthenticationHelper{
  	
  	/**
  	 * Check if the current Joomla user is logged into the system.
  	 * If they are a guest, throw an AuthenticationException to remind 
     * them to log in.  
     * 
     * @return array("VALID","ERROR")
  	 */
  	public static function isLoggedIn(){
  	  $oReturnArray = array("VALID"=>true, "ERROR"=>"");
  	  
  	  $oUser =& JFactory::getUser();
  	  try{
        if($oUser->guest){
      	  throw new AuthenticationException("Login to see the curation content.");
        }
      }catch(AuthenticationException $oAuthenticationException){
        $oReturnArray["VALID"] = false;
        $oReturnArray["ERROR"] = $oAuthenticationException->getError();
      }
      return $oReturnArray;
  	}
  	
  	/**
  	 * Check if the current Joomla user is a member of the specified group.
  	 *
  	 * @return array("VALID","ERROR")
  	 */
  	public static function isMember($p_strGroupName){
  	  $oReturnArray = array("VALID"=>true, "ERROR"=>"");
  	  try{
  	    if(!YGroupHelper::is_member($p_strGroupName)){
      	  throw new AuthenticationException("You are not a member of the $p_strGroupName group.");
        }
      }catch(AuthenticationException $oAuthenticationException){
        $oReturnArray["VALID"] = false;
        $oReturnArray["ERROR"] = $oAuthenticationException->getError();
      }	
      return $oReturnArray;
  	}
  	
  }

?>