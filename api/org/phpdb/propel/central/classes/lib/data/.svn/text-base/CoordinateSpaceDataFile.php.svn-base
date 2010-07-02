<?php

require_once 'lib/data/om/BaseCoordinateSpaceDataFile.php';


/**
 * CoordinateSpaceDataFile
 *
 * DataFiles describing the coordinate space in greater detail or
 * in a different format than is possible in the web ui; e.g., a picture
 *
 * Encapsulates relationship between a {@see CoordinateSpace} and its
 * {@link DataFiles}. Allows for a comment.
 *
 * @package    lib.data
 * @uses CoordinateSpace
 * @uses DataFile
 * @todo determine if we really use the comment field
 */
class CoordinateSpaceDataFile extends BaseCoordinateSpaceDataFile {


  function __construct(
    $coordinateSpace = null,
    $dataFile = null,
    $comment  = "Comments field cannot be null." )
  {
    $this->setCoordinateSpace($coordinateSpace);
    $this->setDataFile($dataFile);
    $this->setComment($comment);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $cs = $this->getCoordinateSpace();
    return $cs->getRESTURI() . "/CoordinateSpaceDataFile/{$this->getId()}";
  }


} // CoordinateSpaceDataFile
?>
