<?php

require_once 'lib/data/om/BaseSimilitudeLawGroup.php';
require_once 'lib/data/SimilitudeLaw.php';


/**
 * SimilitudeLawGroup
 *
 * fields:
 * ExperimentDomain: Experiment Domain that this group belongs to.
 * Name: Name of this group
 * SystemName: Internal name of the group.
 *
 * @todo document this better
 *
 * @package    lib.data
 *
 * @uses ExperimentDomain
 *
 */
class SimilitudeLawGroup extends BaseSimilitudeLawGroup {

  /**
   * Constructs a new SimilitudeLawGroup object
   */

  public function __construct(ExperimentDomain $experimentDomain = null,
                              $name = null,
                              $systemName = null )
  {
    $this->setExperimentDomain($experimentDomain);
    $this->setName($name);
    $this->setSystemName($systemName);
  }


  /**
   * Get similitude laws associated with this group.
   *
   * @return array <SimilitudeLaw>
   */
  function getSimilitudeLaws() {
    return SimilitudeLawPeer::findByGroup($this->getId());
  }


  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/SimilitudeLawGroup/{$this->getId()}";
  }

} // SimilitudeLawGroup
?>
