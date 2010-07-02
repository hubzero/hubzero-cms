<?php
require_once 'lib/render/SDORenderer.php';

class FacilitySDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->Facility[0];

    $this->addData($das, $obj, $objxml, 'Equipment', 'findAllByOrganization');

    return array($das, $doc);
  }

}

?>
