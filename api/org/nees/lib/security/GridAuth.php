<?php
################################################################################
## GridAuth.php
##   by Tim Warnock (c) 2005
##
##   GridAuth localhost short circuit - based on the
##   PHP implementation of the GridAuth API specification
##   http://gridauth.sourceforge.net
##
##   Basically, this implemented the GridAuth API spec except it assumes
##   local access to the private gridauth database using the following
##
################################################################################
//require_once 'lib/db/database.php';
//global $ini_array;

class GridAuth {

  /**
  ##############################################################################
  ## CONSTRUCTOR
  ##############################################################################
   *
   * @return GridAuth
   */
  function GridAuth()
  {
    //global $ini_array;

    $this->VERSION = '0.3 short circuit';
	  $this->serviceHost = 'https://neesws.neeshub.org:9443/GRIDAUTH/drbgridauth.cgi';
    $this->self = array();
  }



  /**
   ##############################################################################
   ## set service handler
   ##############################################################################
   *
   * @param String $url
   */
  function setServiceHandler( $url ) {
    $this->serviceHost = $url;
  }



  /**
   ##############################################################################
   ## User Agent
   ##############################################################################
   *
   * @param array $data
   * @return String $result
   */
  private function gridauth_ua( &$data ) {
    $ch = curl_init();

    //var_dump($data);

    curl_setopt ($ch, CURLOPT_URL, $this->serviceHost);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt ($ch, CURLOPT_POST,1);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER,1);

    $result = curl_exec( $ch );
    curl_close ($ch);

    return $result;
  }



  /**
   ##############################################################################
   ## login(username, password)
   ## login(session)
   ##############################################################################
   *
   * @return boolean 0/1
   */
  function login() {
    $postData = array();
    $postData['service'] = "PHP GridAuth API $this->VERSION";
    $resxml = "";
    if (func_num_args() == 1) {
      $session = func_get_arg(0);
      $postData['session'] = $session;
      $resxml = $this->gridauth_ua( $postData );
    }
    elseif (func_num_args() == 2) {
      $postData['command'] = 'login';
      $postData['username'] = func_get_arg(0);
      $postData['password'] = func_get_arg(1);

      $resxml = $this->gridauth_ua( $postData );
    }
    else {
      return 0;
    }
    ## parse XML response
    $ret = 0;

    //echo '$resxml=..'.$resxml . '..';
    //echo '<br/>';

    $xml = preg_split( '/<\/key>/im', $resxml );
    foreach ($xml as $matcher) {
      //if ( preg_match( '/<key name[ ]*=[" ]*([^>"]+)[" ]*>(.*)/is', $matcher, $match) ) {
      if ( preg_match( '/<key name[ ]*=["\' ]*([^>"\']+)["\' ]*>(.*)/is', $matcher, $match) ) {
        $ret = 1;
        $key = $match[1];
        $this->self[$key] = $match[2];
      }
    }

    return $ret;
  }


    public function getUsernameFromSession($GASession)
    {
        $rv = "";
        $resxml = "";
        $postData = array();
        $session = func_get_arg(0);
        $postData['session'] = $session;
        $resxml = $this->gridauth_ua( $postData );

        if(!empty($resxml))
        {
            /* @var $xml SimpleXMLElement */
            $xml = simplexml_load_string($resxml);

            $XMLQueryResults = $xml->xpath("key[@name='username']");

            /* @var $e SimpleXMLElement */
            if(count($XMLQueryResults) == 1)
                $rv = (string)$XMLQueryResults[0];
            else
                $rv = "";
        }
        else
            $rv = "";

        return $rv;
    }

  /**
   ##############################################################################
   ## logout()
   ##############################################################################
   *
   * @return boolean 0/1
   */
  function logout() {
    if ( isset( $this->self['session'] ) ) {
      $postData = array();
      $postData['service'] = "PHP GridAuth API $this->VERSION";
      $postData['command'] = 'logout';
      $postData['session'] = $this->self['session'];
      return $this->gridauth_ua( $postData );
    }
    return 0;
  }



  /**
   ##############################################################################
   ## get(key)
   ##############################################################################
   *
   * @param String $key
   * @return String $value
   */
  function get($key) {
    return $this->self[$key];
  }


  ##############################################################################
  ## getkeys()
  ##############################################################################
  function getkeys() {
    $ret = "";
    foreach ($this->self as $key => $value) {
      $ret .= " $key";
    }
    return $ret;
  }



  /**
   * Run the GridAuth service
   *
   * @param array $userdata
   * @return XML return
   */
  function GACommand($userdata) {
    return $this->gridauth_ua( $userdata );
  }



}
?>
