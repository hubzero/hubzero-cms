<?php
require_once 'lib/render/SDORenderer.php';

class ProjectSDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    list($das, $doc) = parent::render($obj, $title);

    $objxml = $doc->getRootDataObject("central")->Project[0];

    //$dir = '/nees/home/' . $obj->getName() . ".groups";
    $dir = $obj->getPathname();
    //$this->addFiles($das, $objxml, $dir);
    $this->addFiles($das, $objxml, $dir . "/Analysis");
    $this->addFiles($das, $objxml, $dir . "/Documentation");
    $this->addFiles($das, $objxml, $dir . "/Public");

    return array($das, $doc);
  }

}

?>
