<?php

require_once 'lib/data/om/BaseSimilitudeLaw.php';


/**
 * SimilitudeLaw
 *
 * Fields:
 * name: Similitude Law name
 * symbol: Symbol representing the similitude law
 * Description.
 * SystemName: Variable name to use in computed equations.
 * UnitDescription
 * DisplayEquation: Similitude law equation suitable for HTML display
 * ComputeEquation: Similitude law equation to eval() to get value.
 * Dependence: Is this a dependent or independent similitude law?
 * SimilitudeLawGroup: Group that this sim law is a member of.
 *
 * @todo document this better
 *
 * @package    lib.data
 *
 * @uses SimilitudeLawGroup
 *
 */
class SimilitudeLaw extends BaseSimilitudeLaw {

  /**
   * Constructs a new SimilitudeLaw Object
   */
  public function __construct( $name = null,
                               $symbol = null,
                               $description = null,
                               $systemName = null,
                               $unitDescription = null,
                               $displayEquation = null,
                               $computeEquation = null,
                               $dependence = 'dependent',
                               SimilitudeLawGroup $similitudeLawGroup = null )
  {
    $this->setName($name);
    $this->setSymbol($symbol);
    $this->setDescription($description);
    $this->setSystemName($systemName);
    $this->setUnitDescription($unitDescription);
    $this->setDisplayEquation($displayEquation);
    $this->setComputeEquation($computeEquation);
    $this->setDependence($dependence);
    $this->setSimilitudeLawGroup($similitudeLawGroup);
  }

  public function compute($c) {

    $value = 0;
    $equation = $this->getComputeEquation();

    foreach( $c as $val ) {
      $sysname = $val->getSimilitudeLaw()->getSystemName();
      $equation = preg_replace("/\[$sysname\]/", $val->getValue(), $equation);
    }


    //Kevin, help me
    /*
    eval("\$value = $equation;");
    return $value;
    */

    return $equation;
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    $grp = $this->getSimilitudeLawGroup();
    return $grp->getRESTURI() . "/SimilitudeLaw/{$this->getId()}";
  }


} // SimilitudeLaw
?>
