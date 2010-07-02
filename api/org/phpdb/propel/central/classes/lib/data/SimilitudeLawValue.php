<?php

require_once 'lib/data/om/BaseSimilitudeLawValue.php';


/**
 * SimilitudeLawValue
 *
 * fields:
 * Experiment: Experiment that this value belongs to.
 * SimilitudeLaw: Similitude law that this value overrides.
 * Value: Value to override similitude law equation with.
 * Comments: User's text re: this value
 *
 *  @todo document this
 *
 * @package    lib.data
 *
 * @uses Experiment
 * @uses SimilitudeLaw
 *
 */
class SimilitudeLawValue extends BaseSimilitudeLawValue {

  /**
   * Constructs a new SimilitudeLawValue
   */
  public function __construct( Experiment $experiment = null,
                               SimilitudeLaw $SimilitudeLaw = null,
                               $value = null,
                               $comment = null )
  {
    $this->setExperiment($experiment);
    $this->setSimilitudeLaw($SimilitudeLaw);
    $this->setValue($value);
    $this->setComments($comment);
  }


  /**
   * Make a default SimilitudeLawValues
   *
   * @param Experiment $e
   */
  public static function createDefaultSimilitudeLawValues(Experiment $e) {
    $indgroups = SimilitudeLawGroupPeer::findByDependence($e->getExperimentDomain(), 'independent');

    foreach( $indgroups as $grp ) {
      $laws = $grp->getSimilitudeLaws();

      foreach( $laws as $law ) {
        $lawval = new SimilitudeLawValue($e, $law, 1, null);
        $lawval->save();
      }
    }
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $exp = $this->getExperiment();
    return $exp->getRESTURI() . "/SimilitudeLawValue/{$this->getId()}";
  }


  /**
   * Get Comments from this SimilitudeLawValue
   * Backward compatible with NEEScentral 1.7
   *
   * @return String
   */
  public function getComment() {
    return $this->getComments();
  }

  /**
   * Set Comments for this SimilitudeLawValue
   * Backward compatible with NEEScentral 1.7
   *
   * @param String
   */
  public function setComment($c) {
    return $this->setComments($c);
  }


} // SimilitudeLawValue
?>
