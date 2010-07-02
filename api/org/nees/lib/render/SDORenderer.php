<?php
require_once 'lib/util/PeerMap.php';
require_once 'lib/render/Renderer.php';

class SDORenderer implements Renderer {
  private $type;
  function __construct($type) {
    $this->type = $type;
  }

  function render( BaseObject $obj, $title = null ) {
    $objClass = get_class($obj);

    $das = SDO_DAS_XML::create('/opt/central/htdocs/api/nees_central.xsd');
    $doc = $das->createDocument();
    $root = $doc->getRootDataObject("central");
    //$xml = $das->createDataObject('http://central.nees.org/api', $objClass);
    $xml = $root->{$objClass}[] = $das->createDataObject('http://central.nees.org/api', $objClass);
    //$xml = $root->$objClass;


    $props = $obj->getProperties();
    foreach( $props as $prop ) {
      // If a property doesn't have a mutator suffix, odds
      // are that it's defunct / not exposed for a reason.
      if( !$prop->getMutatorSuffix() ) {
        continue;
      }
      $propName = $prop->getColumn();
      $getter = $prop->getGetter();
      $value = $obj->$getter();

      // libXML2 is NOT GOOD at handling empty
      // attributes.
      if( null == $value ) {
        continue;
      }

      // Skip further checking if this element
      // isn't officially part of the domain
      // object with a mutator suffix, set the
      // attribute to empty.
      if( !$prop->getMutatorSuffix() ) {
        try {
          $xml->$propName = '';
        }
        catch( Exception $e ) {
          // Do nothing.
        }
        continue;
      }
      // Loop over collection items...
      if( get_class($prop) == 'CollectionProperty' ) {
        foreach( $value as $item ) {
          // TODO - needs fixing along with XSD
          $propType = get_class($item);
          try {
            $xprop = $das->createDataObject('http://central.nees.org/api', $propType);
            $xprop->link = '/REST' . $item->getRESTURI();
            $xml->{$propType}[] = $xprop;
          }
          catch( Exception $e ) {
            // do nothing.
          }
        }
      }
      // Link to related objects...
      elseif( get_class($prop) == 'ObjectProperty') {
        // TODO - needs fixing to print as value, or print as valid uri.
        $propName = $prop->getMutatorSuffix();
        try {
          $xprop = $das->createDataObject('http://central.nees.org/api', $prop->getType());
          $xprop->link = '/REST' . $value->getRESTURI();
          $xml->$propName = $xprop;
        }
        catch( Exception $e ) {
          // Do nothing.
        }
      }
      else {
        try {
          $xml->$propName = $value;
        }
        catch( Exception $e ) {
          // Do nothing.
        }
      }
    }

    return array($das, $doc);

  }

  protected function addData($das, $obj, $dataobj, $className, $finder) {
    $items = PeerMap::getPeer($className)->$finder($obj);
    if( $items ) {
      foreach( $items as $item ) {
        $itemxml = $das->createDataObject('http://central.nees.org/api', $className);
        $itemxml->link = '/REST' . $item->getRESTURI();
        $dataobj->{$className}[] = $itemxml;
      }
    }
  }

  protected function addFiles($das, $dataobj, $file) {
    $items = DataFilePeer::findByFullPath($file);
    if( $items ) {
      foreach( $items as $item ) {
        $itemxml = $das->createDataObject('http://central.nees.org/api', 'DataFile');
        $itemxml->link = '/REST' . $item->getRESTURI();
        $dataobj->DataFile[] = $itemxml;
      }
    }
  }

}
?>
