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
 
class sitesViewEducationAndOutreach extends JView
{
	
    function display($tpl = null)
    {
        // Grab facility from Oracle
		$facilityID = JRequest::getVar('id');
    	$facility = FacilityPeer::find($facilityID);
		
		//echo(print_r($facility));
		
  		$fac_name = $facility->getName();
		$fac_shortname = $facility->getShortName();
        $this->assignRef( 'FacilityName', $fac_name);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name );             

        // Add facility name to breadcrumb
        $pathway->addItem( $fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));
        
        // Add Sensor tab info to breadcrumb
        $pathway->addItem( "Education and Outreach",  JRoute::_('index.php?option=com_sites&view=education-outreach&id=' . $facilityID));
        
  		
        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(6, $facilityID);
        $this->assignRef('tabs', $tabs); 
        
        $fileBrowserObj = new DataFileBrowserSimple($facility);
        $this->assignRef('fileBrowserObj', $fileBrowserObj); 
		$infotype = "EducationAndOutreach";
        
        
		$fds = FacilityDataFilePeer::findByDetails($facilityID, "EducationOutreach", "Education and Outreach", "");
  		$datafiles = array();

		foreach($fds as $fd) {
	    	/* @var $fd FacilityDataFile */
	    	$datafiles[] = $fd->getDataFile();
		}

		// Lets pass these to the template
		$this->assignRef('datafiles', $datafiles ); 
		
        
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

//		print_r($facDataFiles);
		
		if (count($facDataFiles) > 0 && $ff = $facDataFiles[0]) {
			$df = $ff->getDataFile();

			if ( !$df->getDeleted())
			{
//				print_r($df);
				return $df;
			} 
		}

		return null;
	}    
    
}
