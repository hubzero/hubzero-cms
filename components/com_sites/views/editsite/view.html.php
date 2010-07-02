<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright	Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * 
 * 
 */
 
class sitesVieweditsite extends JView
{
	
    function display($tpl = null)
    {
        // Grab facility from Oracle
		$facilityID = JRequest::getVar('id');
    	$facility = FacilityPeer::find($facilityID);
		
  		$fac_name = $facility->getName();
		$fac_shortname = $facility->getShortName();
        $this->assignRef( 'FacilityName', $fac_name);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name );             
        $pathway->addItem( $fac_name,  JRoute::_('/index.php?option=com_sites&view=site&id=' . $facilityID));
                
        // Get url for facility image
		$imgFacilityURL = "/components/com_sites/images/facility_" . strtolower($fac_shortname) . ".jpg";
        $this->assignRef('imgFacilityURL', $imgFacilityURL);
  		
        // Get other facility info
        $this->assignRef('SiteName', $facility->getSiteName()); 
        $this->assignRef('Department', $facility->getDepartment());
        $this->assignRef('Laboratory', $facility->getLaboratory());
        $this->assignRef('NsfAwardUrl', $facility->getNsfAwardUrl()); 
        $this->assignRef('SiteUrl', $facility->getUrl());
        $this->assignRef('SiteDescription', $facility->getDescription());
        $this->assignRef('NsfAcknowledgement', $facility->getNsfAcknowledgement()); 
        $this->assignRef('facility', $facility); 
        
        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(0, $facilityID);
        $this->assignRef('tabs', $tabs); 
		        
        parent::display($tpl);
	}
    
    
  /**
   * Get a single DataFile for a facility by infoType, subInfoType and GroypBy
   *
   * @param String $info
   * @param String $sub
   * @param String $groupby
   * @return DataFile
   */
	function getFacilityDataFile( $facilityid, $info, $sub, $groupby='' ){
		$facDataFiles = FacilityDataFilePeer::findByDetails($facilityid, $info, $sub, $groupby);

		if (count($facDataFiles) > 0 && $ff = $facDataFiles[0]) {
			$df = $ff->getDataFile();

			if ( !$df->getDeleted())
			{
				return $df;
			} 
		}

		return null;
	}    
    
}
