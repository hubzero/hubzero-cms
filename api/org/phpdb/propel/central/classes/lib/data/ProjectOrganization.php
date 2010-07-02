<?php

require_once 'lib/data/om/BaseProjectOrganization.php';


/**
 * ProjectOrganization
 *
 * M2M Join Table for Project <--> Organization relationship
 *
 * @package    lib.data
 *
 * @uses Project
 * @uses Organization
 */
class ProjectOrganization extends BaseProjectOrganization {

  /**
   * Initializes internal state of ProjectOrganization object.
   */
  public function __construct(Project $project=null,
                              Organization $organization=null) {
    $this->setProject($project);
    $this->setOrganization($organization);
  }

  /**
   * Return the Web-Services URL that this instance is accessible at
   *
   * @return String RESTURI
   */
  function getRESTURI() {
    return "/ProjectOrganization/{$this->getId()}";
  }

} // ProjectOrganization
?>
