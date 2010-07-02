<?php
require_once 'lib/render/SDORenderer.php';

class RepetitionSDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->Repetition[0];

    $t = $obj->getTrial();
    $e = $t->getExperiment();
    $p = $e->getProject();

    $dir = '/nees/home/' . $p->getName() . ".groups/" . $e->getName() . "/" . $t->getName() . "/" . $obj->getName();
    //$this->addFiles($das, $objxml, $dir);

    $this->addFiles($das, $objxml, ($dir . '/Unprocessed_Data'));
    $this->addFiles($das, $objxml, ($dir . '/Converted_Data'));
    $this->addFiles($das, $objxml, ($dir . '/Corrected_Data'));
    $this->addFiles($das, $objxml, ($dir . '/Derived_Data'));

    return array($das, $doc);
  }

}

?>
