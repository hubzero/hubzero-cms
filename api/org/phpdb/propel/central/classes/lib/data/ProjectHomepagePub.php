<?php

require_once 'lib/data/ProjectHomepage.php';

require_once 'lib/data/om/BaseProjectHomepage.php';


/**
 * Skeleton subclass for representing a row from one of the subclasses of the 'PROJECT_HOMEPAGE' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.data
 */
class ProjectHomepagePub extends ProjectHomepage {

	/**
	 * Constructs a new ProjectHomepagePub class, setting the PROJECT_HOMEPAGE_TYPE_ID column to ProjectHomepagePeer::CLASSKEY_5.
	 */
	public function __construct()
	{

		$this->setProjectHomepageTypeId(ProjectHomepagePeer::CLASSKEY_5);
	}

} // ProjectHomepagePub
