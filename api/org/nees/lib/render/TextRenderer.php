<?php

/**
 * @title ProjectArrayRenderer
 *
 * @abstract
 *    A class that knows how to convert a domain object into a text format
 * consisting of key/value pairs in 2 columns.
 *
 * @author
 *    Adam Birnbaum
 *
 */
class TextRenderer implements Renderer {
  private $arrayRenderer = null;

  function __construct($arrayRenderer) {
    $this->arrayRenderer = $arrayRenderer;
  }

  function render( BaseObject $obj, $title = null ) {
    if (is_null($this->arrayRenderer)) {
      throw new Exception("Null array renderer");
    }
    $values = $this->arrayRenderer->render($obj);

    foreach ( $values as $k => $e) {
      $retval .= "$k $e\n";
    }

    return $retval;
  }

}


?>
