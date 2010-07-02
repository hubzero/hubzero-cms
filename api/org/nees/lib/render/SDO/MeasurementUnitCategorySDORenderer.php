<?php
require_once 'lib/render/SDORenderer.php';

class MeasurementUnitCategorySDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->MeasurementUnitCategory[0];

    $this->addData($das, $obj, $objxml, 'MeasurementUnit', 'findByCategory');

    return array($das, $doc);
  }

}

?>
