<?php

require_once 'lib/data/om/BaseMaterialFile.php';


/**
 * MaterialFile
 *
 * Experimental {@link Material}s can be described with {@link DataFile}s
 *
 * @todo document this puppy
 *
 * @package    lib.data
 *
 * @uses DataFile
 * @uses Material
 *
 */
class MaterialFile extends BaseMaterialFile {

  /**
   * Initializes internal state of MaterialFile object.
   */
  function __construct( Material $material = null,
                        DataFile $datafile = null )
  {
    $this->setMaterial($material);
    $this->setDataFile($datafile);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/MaterialFile/{$this->getId()}";
  }

} // MaterialFile
?>
