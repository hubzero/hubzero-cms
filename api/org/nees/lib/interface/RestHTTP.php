<?php

class RestHTTP {
  // Alias for send401
  public static function authFailed() {
    RestHTTP::send401();
  }

  public static function send401() {
    header("HTTP/1.0 401 Unauthorized");
    print <<<ENDHTML
<html>
  <head><title>Authorization Failed</title></head>
  <body>
    <h1>401 Authorization Failed</h1>
      <p>Without a valid username and password,
         access to this page cannot be granted.
         Please click "reload" and enter a
         username and password when prompted.</p>
      <p>You may not have access to the resource
         you requested.</p>
  </body>
</html>
ENDHTML;
    exit;
  }

  public static function send404() {
    header("HTTP/1.0 404 Not Found");
    print <<<ENDHTML
<html>
  <head><title>Not Found</title></head>
  <body>
    <h1>404 Not Found</h1>
    <p>The resource you requested is invalid or has been removed.</p>
  </body>
</html>
ENDHTML;
    exit;
  }

  public static function send410() {
    header("HTTP/1.0 410 Gone");
    print <<<ENDHTML
<html>
  <head><title>Gone</title></head>
  <body>
    <h1>410 Gone</h1>
    <p>The resource you requested has been removed.</p>
  </body>
</html>
ENDHTML;
    exit;
  }

  public static function send201($uri) {
    header("HTTP/1.0 201 Success No Content");
    if( !preg_match('@^/REST/@', $uri) ) {
      $uri = '/REST' . $uri;
    }
    header("Location: $uri");
  }
  public static function send204() {
    header("HTTP/1.0 204 Success No Content");
    exit;
  }
  public static function send400($msg = null) {
    header("HTTP/1.0 400 Bad Request");
    if( !$msg ) {
      $msg = 'Please be sure to send valid XML that conforms to the schema at <a href="http://central.nees.org/api/nees_central.xsd">http://central.nees.org/api/nees_central.xsd</a>';
    }
    print <<<ENDHTML
<html>
  <head><title>Bad Request</title></head>
  <body>
    <h1>400 Bad Request</h1>
    <p>$msg</p>
   </body>
</html>
ENDHTML;
    exit;
  }

  public static function login() {
    if (!$_SERVER['PHP_AUTH_USER']) {
       // No username.  Send auth headers to make
       // the browser prompt the user.
       header("WWW-Authenticate: " .
              "Basic realm=\"Protected Page: " .
              "Enter your username and password " .
              "for access.\"");
       // Display message if user cancels dialog
       print("Authorization Required");
       exit;
    }
    // If they sent in a username, try to authenticate.
    if( $_SERVER['PHP_AUTH_USER'] ) {
      $authenticator = Authenticator::getInstance();
      if ( !$authenticator->login($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) ) {
        // Invalid login.
        RestHTTP::authFailed();
      }
    }
    else {
      // Should never happen... but ya never know.
      RestHTTP::authFailed();
    }
  }

  
  public static function getData() {
    $xmldata = "";
    if( isset($_POST['xmldata']) ) {
      $xmldata = $_POST['xmldata'];
    } else {
      $httpContent = fopen('php://input', 'r');
      while( $data = fread( $httpContent, 1024 ) ){
        $xmldata .= $data;
        
      }
    }


    if( $xmldata ) {
      try {
        $xmldas = SDO_DAS_XML::create("../htdocs/api/nees_central.xsd");
        $document = $xmldas->loadString($xmldata);
      }
      catch( Exception $e ) {
        return $xmldata;
      }
      return $document;
    }
    return $_REQUEST;
  }
}

?>
