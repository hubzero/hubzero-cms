<?php

/**
 *  XMLRendererFactory -- a factory class responsible for the creation of domain object Renderers.
 *  It encapsulates creation logic, to allow us to load the right class to serialize the domain
 *  object in question, into the desired format.
 */

require_once "lib/render/Renderer.php";
require_once "lib/render/XMLRenderer.php";
require_once "lib/render/TextRenderer.php";
require_once "lib/render/TerseHTMLRenderer.php";

class RendererFactory {

  static function getRenderer( $format, $type ) {

    $suffix = null;
    $subdir = null;
    if ((strcasecmp($format, "xml")) == 0) {
      $suffix = "XMLRenderer";
      $subdir = "xml";
    } else if ((strcasecmp($format, "tersehtml")) == 0) {
      $suffix = "TerseHTMLRenderer";
      $subdir = "terseHtml";
    } elseif ( strtolower($format) == 'sdo' ) {
      $subdir = 'SDO';
      $suffix = "SDORenderer";
    }


    if ($suffix) {
      $theclass = "{$type}{$suffix}";

      if (@include_once "lib/domain/render/$subdir/$theclass.php") {
        if (class_exists($theclass)) {
          return new $theclass(null);
        } else if (class_exists($suffix)) {
          return new $suffix(null);
        }
      }
    }

    $arrayRenderer = "{$type}ArrayRenderer";
    @include_once "lib/domain/render/array/" . $arrayRenderer . ".php";
/*    if (!class_exists($arrayRenderer)) {
      throw new Exception("Unknown renderer class: $arrayRenderer");
    } */

    if (strcasecmp($format, "array") == 0) {
      return new $arrayRenderer();
    } else if (strcasecmp($format, "xml")== 0) {
      return new XMLRenderer(new $arrayRenderer());
    } else if (strcasecmp($format, "terseHtml") == 0) {
      return new TerseHTMLRenderer(null);
    } elseif ( strtolower($format) == 'sdo') {
      require_once "lib/render/SDORenderer.php";

      return new SDORenderer($type);
    } else {
      return new TextRenderer(new $arrayRenderer());
    }
  }
}
?>
