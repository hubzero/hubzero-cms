<?php
require_once "lib/render/TerseHTMLRenderer.php";

class OrganizationTerseHTMLRenderer extends TerseHTMLRenderer {

  function getObjectUrl(BaseObject $obj) {
    $coll = $obj->getProjects();

    if( count($coll) > 0 ) {
      $proj = $coll[0];
    }
    if( $proj ) {
      return "/?action=ViewProjectOrganizationMain&projid=" . $proj->getId() . "&orgid=" . $obj->getId();
    }
  }

}
?>
