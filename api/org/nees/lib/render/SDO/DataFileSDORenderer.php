<?php
require_once 'lib/render/SDORenderer.php';

class DataFileSDORenderer extends SDORenderer {

  function render( BaseObject $obj, $title = null ) {
    $objClass = get_class($obj);

    $das = SDO_DAS_XML::create('../htdocs/api/nees_central.xsd');
    $doc = $das->createDocument();
    $root = $doc->getRootDataObject();
    $xml = $root->{$objClass}[] = $das->createDataObject('http://central.nees.org/api', $objClass);
    $xml->path = dirname($obj->getFriendlyPath());
    $xml->name = $obj->getName();
    $xml->contentLink = '/REST' . $obj->getContentsLink();
    if( $obj->getAuthors() ) {
      $xml->Authors = $obj->getAuthors();
    }
    if( $obj->getAuthorEmails() ) {
      $xml->AuthorEmails = $obj->getAuthorEmails();
    }
    if( $obj->getDescription() ) {
      $xml->Description = $obj->getDescription();
    }
    if( $obj->getHowToCite() ) {
      $xml->HowToCite = $obj->getHowToCite();
    }
    if( $obj->getTitle() ) {
      $xml->Title = $obj->getTitle();
    }
    if( $obj->getPageCount() ) {
      $xml->PageCount = $obj->getPageCount();
    }
    if( $obj->getDirectory() ) {
      $xml->isDirectory = $obj->getDirectory();
    }
    if( $obj->getChecksum() ) {
      $xml->checksum = $obj->getChecksum();
    }
    if( $obj->getCreated() ) {
      $xml->created = $obj->getCreated();
    }

    // Find subdirs!
    $path = $obj->getFullPath();

    // This is not pretty, but it gets us the the timestamp.
    $xml->timestamp = ( file_exists($path) ? date("Y-m-d H:i", filemtime($path)) : "" );


    if( is_dir($path) ) {
      $childrens = DataFilePeer::findByDirectory($path);
      if( $childrens ) {
        foreach( $childrens as $weelass ) {
          $progeny = $das->createDataObject('http://central.nees.org/api', 'DataFile');
          $progeny->link = '/REST' . $weelass->getRESTURI();
          $xml->DataFile[] = $progeny;
        }
      }
    }

    return array($das, $doc);
  }

}

?>
