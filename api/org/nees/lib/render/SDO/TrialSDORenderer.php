<?php
require_once 'lib/render/SDORenderer.php';

class TrialSDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->Trial[0];
exit("\$objxml = " . $objxml);
    $e = $obj->getExperiment();
    $p = $e->getProject();

    $dir = '/nees/home/' . $p->getName() . ".groups/" . $e->getName() . "/" . $obj->getName();

    $this->addFiles($das, $objxml, ($dir . '/Analysis'));
    $this->addFiles($das, $objxml, ($dir . '/Documentation'));
    $this->addFiles($das, $objxml, ($dir . '/Models'));
    $this->addFiles($das, $objxml, ($dir . '/Setup'));

    // These are for SimulationRuns
    $this->addFiles($das, $objxml, ($dir . '/InMain'));
    $this->addFiles($das, $objxml, ($dir . '/Input'));
    $this->addFiles($das, $objxml, ($dir . '/Output'));

    return array($das, $doc);
  }

}

?>
