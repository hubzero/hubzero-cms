<?php
require_once 'lib/render/SDORenderer.php';

class SensorManifestSDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->SensorManifest[0];

    $this->addData($das, $obj, $objxml, 'SensorSensorManifest', 'findByManifest');

    return array($das, $doc);
  }

}

?>
