<?php

require_once 'lib/data/om/BaseFacilityDataFile.php';


/**
 * FacilityDataFile
 *
 * Represents a data file associated with a given Facility.
 *
 * @todo improve documentation of this class
 *
 * @package    lib.data
 *
 * @uses Facility
 * @uses DataFile
 * @uses DocumentType
 * @uses DocumentFormat
 *
 */
class FacilityDataFile extends BaseFacilityDataFile {

  /**
   * Initializes internal state of FacilityDataFile object.

   */
  function __construct( Facility $facility = null,
                        DataFile $datafile = null,
                        $infotype = "",
                        $subinfo ="",
                        $groupby ="",
                        DocumentType $doctype = null,
                        DocumentFormat $docformat = null)
  {
    $this->setFacility($facility);
    $this->setDataFile($datafile);
    $this->setInfoType($infotype);
    $this->setSubinfoType($subinfo);
    $this->setGroupby($groupby);
    $this->setDocumentType($doctype);
    $this->setDocumentFormat($docformat);
  }

  /**
   * Set the facility object for this FacilityDataFile
   *
   * @param Facility $facility
   */
  public function setFacility($facility) {
    $this->setOrganization($facility);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/FacilityDataFile/{$this->getId()}";
  }

} // FacilityDataFile
?>
