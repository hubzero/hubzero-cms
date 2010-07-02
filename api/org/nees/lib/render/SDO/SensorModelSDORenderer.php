<?php
require_once 'lib/render/SDORenderer.php';

class SensorModelSDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->SensorModel[0];

    $this->addData($das, $obj, $objxml, 'Sensor', 'findBySensorModel');

    return array($das, $doc);
  }

}

?>
