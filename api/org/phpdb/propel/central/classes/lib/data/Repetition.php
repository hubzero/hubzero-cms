<?php

require_once 'lib/data/om/BaseRepetition.php';


/**
 * Repetition
 *
 * A given {@link Trial} may be repeated several times, each is a Repetition
 *
 * @package    lib.data
 *
 * @uses Trial
 */
class Repetition extends BaseRepetition {

  /**
   * Initializes internal state of Repetition object.
   */
  function __construct(Trial $trial = null,
                       $name = "",
                       $startDate = null,
                       $endDate = null,
                       $status = "",
                       $curationstatus = "Uncurated",
                       $deleted = false)
  {
    $this->setName($name);
    $this->setStartDate($startDate);
    $this->setEndDate($endDate);
    $this->setStatus($status);
    $this->setTrial($trial);
    $this->setCurationStatus($curationstatus);
    $this->setDeleted($deleted);
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/Repetition/{$this->getId()}";
  }



  /**
   * Check if Repetition is in curation status or not
   *
   * @return boolean value: true if YES, false if NO
   */
  public function isInCuration(){
    if (($this->getCurationStatus() == "Curated") || ($this->getCurationStatus()=="Submitted")) {
      return true;
    }
  }



  /**
   * Each repetion is associated with a directory on disk.
   * This function returns the path of that directory for
   * this repetition.
   *
   * @return String $path
   */
  public function getPathname() {
    return $this->getTrial()->getPathname() . "/" . $this->getName();
  }

  /**
   * Check if this Repetition is published or not
   *
   * @return boolean value
   */
  public function isPublished(){
    return $this->getTrial()->getExperiment()->isPublished();
  }

} // Repetition
?>
