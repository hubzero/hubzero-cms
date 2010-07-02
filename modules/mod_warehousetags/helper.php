<?php
/**
* @version		$Id: helper.php 11668 2009-03-08 20:33:38Z willebil $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

include_once 'lib/data/curation/NCCuratedObjectCatalogEntryPeer.php';
include_once 'lib/data/ResearcherKeywordPeer.php';

class modWarehouseTagsHelper{
	
  public function getOntologyByProjectId($p_iProjectId){
  	return NCCuratedObjectCatalogEntryPeer::getOntologyByProjectId($p_iProjectId);
  }

  public function getOntologyByExperimentId($p_iExperimentId){
  	return NCCuratedObjectCatalogEntryPeer::getOntologyByExperimentId($p_iExperimentId);
  }
  
  public function getTagsByEntity($p_iEntityId, $p_iEntityTypeId){
  	return ResearcherKeywordPeer::getTagsByEntity($p_iEntityId, $p_iEntityTypeId);
  }
  	
}
