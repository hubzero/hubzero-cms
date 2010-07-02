<?php
################################################################################
## helpers.inc
##   by Tim Warnock, Daniel Frysinger (c) 2005
##
##   Helper functions for SOA
##
################################################################################


################################################################################
## set request variable
################################################################################

function set_request( $reqvar, $reqval ) {
  if (strlen($reqval) > 0) {
    return $reqval;
  }
  return isset($_REQUEST[$reqvar]) ? $_REQUEST[$reqvar] : null;
}


################################################################################
## return path iff source is not set
################################################################################
function set_from_path( $source,
                        $pindex = -1 ) {
  if (strlen($source) < 1) {
    if ( $pindex >= 0 ) {
      $path = preg_split('/\//', $_SERVER['PATH_INFO'], 0, PREG_SPLIT_NO_EMPTY);
      $source = $path[ $pindex ];
    } else {
      $source = $_SERVER['PATH_INFO'];
    }
  }
  return $source;
}


################################################################################
## error wrapper
################################################################################
function error( $error = "", $FUNCT = 1 ) {
  if (!empty($error)) {
    // Service call
    if (! $FUNCT) {
      header("Content-Type: text/xml");
      ob_clean();

      $date = date("Y-m-d H:i:s");
      $retval = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
      $retval .= "<NEES>\n\t<NEESTime>$date</NEESTime>\n\t<Errors>\n";
      if(is_array($error)) {
        foreach ( $error as $e) {
          $retval .= "\t\t<Error>$e</Error>\n";
        }
      } else {
        $retval .= "\t\t<Error>$error</Error>\n";
      }
      $retval .= "\t</Errors>\n</NEES>";
      return $retval;
    }
    // Webapp
    else {
      return $error;
    }
  }
  return 0;
}


################################################################################
## output wrapper (supports XML and TEXT)
################################################################################
function output( $title = "", $values = "", $type = "TEXT" ) {
  $date = date("Y-m-d H:i:s");

  if ($type == "XML") {
    header("Content-Type: text/xml");
    ob_clean();

    $retval = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
    $retval .= "<NEES>\n\t<NEESTime>$date</NEESTime>\n\t";
    $retval .= "<Output name=\"$title\">\n";
    if(is_array($values)) {
      foreach ( $values as $k => $e) {
        if(is_array($e)){
          $retval .= "\t\t<key name=\"$k\">\n";
          foreach( $e as $k2 => $e2 ){
            $retval .= "\t\t\t<key name=\"$k2\">$e2</key>\n";
          }
          $retval .= "\t\t</key>\n";
        } else {
          $retval .= "\t\t<key name=\"$k\">$e</key>\n";
        }
      }
    } else {
      $retval .= "\t\t<key name=\"default\">$values</key>\n";
    }
    $retval .= "\t</Output>\n</NEES>";
    return $retval;
  } else {
    $retval = "Output $title ($date)\n";
    if(is_array($values)) {
      foreach ( $values as $k => $e) {
        $retval .= "$k $e\n";
      }
    } else {
      $retval .= "$values\n";
    }
    return $retval;
  }
}

?>