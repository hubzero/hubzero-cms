<?php

require_once 'lib/data/Project.php';

require_once 'lib/data/om/BaseProject.php';


/**
 * Skeleton subclass for representing a row from one of the subclasses of the 'PROJECT' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class SuperProject extends Project {

	/**
	 * Constructs a new SuperProject class, setting the PROJECT_TYPE_ID column to ProjectPeer::CLASSKEY_1.
	 */
	public function __construct(
                      $title = "",
                      $description = "",
                      $contactName = "",
                      $contactEmail = "",
                      $sysadminName = "",
                      $sysadminEmail = "",
                      $startDate = null,
                      $endDate = null,
                      $ack = "",
                      $view = "PUBLIC",
                      $projectTypeId = 3,
                      $nees = TRUE,
                      $nickname = "",
                      $fundorg = "",
                      $fundorgprojid = "",
                      $projectName = "",
                      $creatorId = null) {
	  parent::__construct(
                $title,
                $description,
                $contactName,
                $contactEmail,
                $sysadminName,
                $sysadminEmail,
                $startDate,
                $endDate,
                $ack,
                $view,
                $projectTypeId,
                $nees,
                $nickname,
                $fundorg,
                $fundorgprojid,
                $projectName,
                $creatorId = null);

	  $this->setProjectTypeId(ProjectPeer::CLASSKEY_SUPERPROJECT );
	}

	/**
	 * Get all sub-projects that have this project as a supper project.
	 *
	 * @param Criteria $criteria
	 * @param Connection $con
	 * @return array <Projects>
	 */
	public function getSubProjects($criteria = null, $con = null)
	{
	  return $this->getProjectsRelatedBySuperProjectId($criteria,$con);
	}


	/**
	 * Add a project into a list of sub-projects.
	 *
	 * @param Project $project
	 */
	public function addSubProject(Project $project) {
	  $this->addProjectRelatedBySuperProjectId($project);
	}

} // SuperProject
