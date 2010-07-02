<?php
require_once 'lib/render/SDORenderer.php';

class SimilitudeLawGroupSDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->SimilitudeLawGroup[0];

    $this->addData($das, $obj, $objxml, 'SimilitudeLaw', 'findByGroup');

    return array($das, $doc);
  }

}

?>
