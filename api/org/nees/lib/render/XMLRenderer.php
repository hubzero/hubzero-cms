<?php

/**
 * @title ProjectXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a domain object into a rather
 * oddball XML format, compatible with "the old code".
 *
 * @author
 *    Adam Birnbaum
 *
 */
class XMLRenderer implements Renderer {
  private $arrayRenderer = null;

  function __construct($arrayRenderer) {
    $this->arrayRenderer = $arrayRenderer;
  }

  function render( BaseObject $obj, $title = null) {
    $values = $this->arrayRenderer->render($obj);

    $date = date("Y-m-d H:i:s");
    header("Content-Type: text/xml");
    $retval = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n";
    $retval .= "<NEES>\n\t<NEESTime>$date</NEESTime>\n\t";
    $retval .= "<Output name=\"$title\">\n";
    foreach ( $values as $k => $e) {
      if(is_array($e)){
        $retval .= "\t\t<key name=\"$k\">\n";
        foreach( $e as $k2 => $e2 ){
          $retval .= "\t\t\t<key name=\"$k2\">$e2</key>\n";
        }
        $retval .= "\t\t</key>\n";
      } else {
        if (is_bool($e) && !$e) {
          $retval .= "\t\t<key name=\"$k\">0</key>\n";
        } else {
          $retval .= "\t\t<key name=\"$k\">$e</key>\n";
        }
      }
    }
    $retval .= "\t</Output>\n</NEES>";

    return $retval;
  }

}


?>
