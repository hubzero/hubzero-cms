<?php
/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright           Copyright 2010 by NEES
*/
 
// no direct access
 
defined( '_JEXEC' ) or die( 'Restricted access' );
 
jimport( 'joomla.application.component.view');
 
/**
 * 
 * 
 */
 
class sitesVieweditequipmentmodel extends JView
{

    function display($tpl = null)
    {

        // Used to redirect here after a documentation file addition
        $uri = & JURI::getInstance();
        $redirectURL = $uri->toString(array('path', 'query'));
        $redirectURL = base64_encode($redirectURL);
        $redirectURL = $redirectURL;
        $this->assignRef('redirectURL', $redirectURL);


        // Grab facility from Oracle
        $facilityID = JRequest::getVar('id');
    	$facility = FacilityPeer::find($facilityID);

        $errorMsg = JRequest::getVar('errorMsg', '');
        $equipmentModelID = Jrequest::getVar('equipmentmodelid');
        $equipmentModel = null;

        if($equipmentModelID <> -1)
        {
            $equipmentModel = EquipmentModelPeer::find($equipmentModelID);
        }

  	$fac_name = $facility->getName();
	$fac_shortname = $facility->getShortName();
        $this->assignRef('FacilityName', $fac_name);
        $this->assignRef('facility', $facility);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name );             

        // add or edit? (-1 will mean new)
        $equipmentModelFields = array();
        if($equipmentModelID == -1)
        {
            $equipmentModelFields['name'] = '';
            $equipmentModelFields['manufacturer'] = '';
            $equipmentModelFields['supplier'] = '';
            $equipmentModelFields['modelnumber'] = '';
        }
        else
        {
            $equipmentModel = EquipmentModelPeer::find($equipmentModelID);
            $equipmentModelFields['name'] = $equipmentModel->getName();
            $equipmentModelFields['manufacturer'] = $equipmentModel->getManufacturer();
            $equipmentModelFields['supplier'] = $equipmentModel->getSupplier();
            $equipmentModelFields['modelnumber'] = $equipmentModel->getModelNumber();
        }

        // Add facility name to breadcrumb
        //$pathway->addItem($fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));

        // Add Equipment tab to breadcrumb
        //$pathway->addItem("Equipment", JRoute::_('index.php?option=com_sites&view=majorequipment&id=' . $facilityID ));

        // Add current page to breadcrumb
        $pathway->addItem((($equipmentModelID == -1) ? 'Add' : 'Edit'). ' Equipment Model' , JRoute::_('index.php?option=com_sites&view=editequipmentmodel&id=' . $facilityID) . '&equipmentmodelid=' . $equipmentModelID);

        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(3, $facilityID);
        $this->assignRef('tabs', $tabs); 


        // The equipment class dropdown
        $equipmentclassddl = $this->getEquipmentClassDropDownList($equipmentModel);

        $this->assignRef('equipmentclassddl', $equipmentclassddl);
        $this->assignRef('equipmentmodelid', $equipmentModelID);
        $this->assignRef('equipmentModelFields', $equipmentModelFields);

        // Files
        /* @var $specfile DataFile */
        $AdditionalSpecFile = $equipmentModel->getAdditionalSpecFile();
        $ManufacturerDocFile = $equipmentModel->getManufacturerDocFile();
        $DesignConsiderationFile = $equipmentModel->getDesignConsiderationFile();
        $SubcomponentsDocFile = $equipmentModel->getSubcomponentsDocFile();
        $InterfaceDocFile = $equipmentModel->getInterfaceDocFile();

        $equipmentModelFields['AdditionalSpecFile'] = 
            !empty($AdditionalSpecFile) ? '<a style="margin-right:20px" href="' . $AdditionalSpecFile->get_url() . '">' . $AdditionalSpecFile->getName() . '</a>'  : '';

        $equipmentModelFields['ManufacturerDocFile'] =
            !empty($ManufacturerDocFile) ? '<a  style="margin-right:20px" href="' . $ManufacturerDocFile->get_url() . '">' . $ManufacturerDocFile->getName() . '</a>'  : '';

        $equipmentModelFields['DesignConsiderationFile'] =
            !empty($DesignConsiderationFile) ? '<a style="margin-right:20px" href="' . $DesignConsiderationFile->get_url() . '">' . $DesignConsiderationFile->getName() . '</a>'  : '';

        $equipmentModelFields['SubcomponentsDocFile'] =
            !empty($SubcomponentsDocFile) ? '<a  style="margin-right:20px" href="' . $SubcomponentsDocFile->get_url() . '">' . $SubcomponentsDocFile->getName() . '</a>'  : '';

        $equipmentModelFields['InterfaceDocFile'] =
            !empty($InterfaceDocFile) ? '<a  style="margin-right:20px" href="' . $InterfaceDocFile->get_url() . '">' . $InterfaceDocFile->getName() . '</a>'  : '';

        /*
        $equipmentModelFields['AdditionalSpecFile'] .=
        $equipmentModelFields['ManufacturerDocFile'] .=
        $equipmentModelFields['DesignConsiderationFile'] .=
        $equipmentModelFields['SubcomponentsDocFile'] .=
        $equipmentModelFields['InterfaceDocFile'] .=
        */

        $equipmentModelFields['AdditionalSpecFile'] .= '<a style="padding-left: 0px;" href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $facilityID . '&equipmentmodelid=' . $equipmentModelID . '&equipmentmodelfiletype=0' . '&redirectURL=' . $redirectURL) . '">' . (empty($AdditionalSpecFile) ? '[add]' : '[replace]' ) . '</a>';
        $equipmentModelFields['ManufacturerDocFile'] .= '<a style="padding-left: 0px;" href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $facilityID . '&equipmentmodelid=' . $equipmentModelID . '&equipmentmodelfiletype=1' . '&redirectURL=' . $redirectURL) . '">' . (empty($ManufacturerDocFile) ? '[add]' : '[replace]' ) . '</a>';
        $equipmentModelFields['DesignConsiderationFile'] .= '<a style="padding-left: 0px;" href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $facilityID . '&equipmentmodelid=' . $equipmentModelID . '&equipmentmodelfiletype=2' . '&redirectURL=' . $redirectURL) . '">' . (empty($DesignConsiderationFile) ? '[add]' : '[replace]' ) . '</a>';
        $equipmentModelFields['SubcomponentsDocFile'] .= '<a style="padding-left: 0px;" href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $facilityID . '&equipmentmodelid=' . $equipmentModelID . '&equipmentmodelfiletype=3' . '&redirectURL=' . $redirectURL) . '">' . (empty($SubcomponentsDocFile) ? '[add]' : '[replace]' ) . '</a>';
        $equipmentModelFields['InterfaceDocFile'] .= '<a style="padding-left: 0px;" href="' . JRoute::_('index.php?option=com_sites&view=editsitefile&id=' . $facilityID . '&equipmentmodelid=' . $equipmentModelID . '&equipmentmodelfiletype=4' . '&redirectURL=' . $redirectURL) . '">' . (empty($InterfaceDocFile) ? '[add]' : '[replace]' ) . '</a>';








        $allowCreate = FacilityHelper::canCreate($facility);
        $allowEdit = FacilityHelper::canEdit($facility);
        // Check rights to be here, even though the referring page won't display a link here
        // without rights, we need to stop the hackers too
        if($equipmentModelID == -1) // we're creating a new equipment model
        {
            if($allowCreate)
                parent::display($tpl);
            else
                echo 'You are not authorized to create equipment models';
        }
        else // we're doing an edit
        {
            if($allowEdit)
                parent::display($tpl);
            else
                echo 'You are not authorized to edit equipment models';
        }

    }

    
  // Generate the choose-equipment-class drop-down list.
    function getEquipmentClassDropDownList($equipmentModel) {

        // Get the experiment's currently-selected EquipmentClass (if one is selected).
        $equipmentClassId = null;
        if ($equipmentModel) {
            $equipmentClass = $equipmentModel->getEquipmentClass();
            $equipmentClassId = $equipmentClass ? $equipmentClass->getId() : null;
        }

        $classes = EquipmentClassPeer::findAll();
        $s = "";

        foreach ($classes as $class) {
            $s .= '<option value="' . $class->getId() . '"';

            if ($class->getId() == $equipmentClassId) {
                $s .= " selected";
            }
            $s .= '>' . $class->getClassName() . "</option>\n";
        }

        //    $s .= '<option value="new" style="background-color: #ccffcc;">New...</option>' . "\n";

        return $s;
    }








        
}// end class
