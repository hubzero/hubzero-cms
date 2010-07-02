<?php
require_once 'lib/render/SDORenderer.php';

class EquipmentSDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->Equipment[0];

    $this->addData($das, $obj, $objxml, 'EquipmentDocumentation', 'findByEquipment');

    return array($das, $doc);
  }

}

?>
