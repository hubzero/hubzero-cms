<?php
require_once 'lib/render/SDORenderer.php';

class ExperimentSDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->Experiment[0];

    $this->addData($das, $obj, $objxml, 'Material', 'findByExperiment');
    $this->addData($das, $obj, $objxml, 'SensorPool', 'findByExperiment');
    $this->addData($das, $obj, $objxml, 'SimilitudeLawValue', 'findByExperiment');
    $this->addData($das, $obj, $objxml, 'ExperimentEquipment', 'findByExperiment');

    $dir = '/nees/home/' . $obj->getProject()->getName() . ".groups/" . $obj->getName();
    //$this->addFiles($das, $objxml, $dir);
    $this->addFiles($das, $objxml, $dir . "/Analysis");
    $this->addFiles($das, $objxml, $dir . "/Documentation");
    $this->addFiles($das, $objxml, $dir . "/Model");

    return array($das, $doc);
  }

}

?>
