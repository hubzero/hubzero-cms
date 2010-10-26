<?php

/**
 * @package		NEEShub 
 * @author		David Benham (dbenha@purdue.edu)
 * @copyright           Copyright 2010 by NEES
 */
// no direct access

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

require_once "lib/data/EquipmentDocumentation.php";
require_once "lib/data/Equipment.php";
require_once "lib/data/EquipmentAttributeClass.php";
require_once "lib/data/DataFile.php";
require_once "lib/data/EquipmentAttributeClass.php";
require_once "lib/data/EquipmentAttribute.php";
require_once "lib/data/EquipmentAttributeValue.php";

/**
 * 
 * 
 */
class sitesViewequipment extends JView {

    function display($tpl = null) {
        $facilityID = JRequest::getVar('id');
        $equipmentID = JRequest::getVar('equipmentid');
        $facility = FacilityPeer::find($facilityID);
        $fac_name = $facility->getName();
        $fac_shortname = $facility->getShortName();

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document = &JFactory::getDocument();
        $pathway = & $mainframe->getPathway();
        $document->setTitle($fac_name);
        $pathway->addItem($fac_name, JRoute::_('/index.php?option=com_sites&view=site&id=' . $facilityID));

        // Add to breadcrumb
        $pathway->addItem("Equipment", JRoute::_('index.php?option=com_sites&view=majorequipment&id=' . $facilityID));

        // Pass the facility to the template, just let it grab what it needs
        $this->assignRef('facility', $facility);

        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(3, $facilityID);
        $this->assignRef('tabs', $tabs);

        // Other equipment related logic
        $equipment = EquipmentPeer::find($equipmentID);
        $isMajor = $equipment->getParent() ? "Sub-Component" : "Major Equipment";
        $equipName = $equipment->getName();
        $subequipList = EquipmentPeer::findAllByParent($equipmentID);

        $canedit = FacilityHelper::canEdit($facility);
        $this->assignRef('canedit', $canedit);

        $allowCreate = FacilityHelper::canCreate($facility);
        $this->assignRef('allowCreate', $allowCreate);

        $this->assignRef('equipment', $equipment);
        $this->assignRef('isMajor', $isMajor);
        $this->assignRef('equipName', $equipName);
        $this->assignRef('subequipList', $subequipList);
        $this->assignRef('equipmentID', $equipmentID);
        $this->assignRef('facilityID', $facilityID);
        $this->assignRef('facility', $facility);

        // This gets all documentation, both for the model and for the actual piece of equipment
        $fileSection = $this->getDocList($equipment, $facility);
        $this->assignRef('fileSection', $fileSection);

        $pathway->addItem($isMajor . ': ' . $equipName, JRoute::_('index.php?option=com_sites&view=equipment&id=' . $facilityID . '&equipmentid=' . $equipmentID));

        // Used to redirect here after a documentation file addition
        $uri = & JURI::getInstance();
        $redirectURL = $uri->toString(array('path', 'query'));
        $redirectURL = base64_encode($redirectURL);
        $redirectURL = $redirectURL;
        $this->assignRef('redirectURL', $redirectURL);



        parent::display($tpl);
    }

    function showEquipInfo($equip) {

        $model = $equip->getEquipmentModel();
        $info = $this->getSubPropertyRow("Equipment Model", $model->getName());
        $info .= $this->getSubPropertyRow("Equipment Class", $model->getEquipmentClass()->getClassName());
        $info .= $this->getSubPropertyRow("Manufacturer", $model->getManufacturer());
        $info .= $this->getSubPropertyRow("Supplier", $model->getSupplier());
        $info .= $this->getSubPropertyRow("Owner", $equip->getOwner());
        $info .= $this->getSubPropertyRow("Model Number", $model->getModelNumber());
        $info .= $this->getSubPropertyRow("Serial Number", $equip->getSerialNumber());
        $info .= $this->getSubPropertyRow("Lab Assigned ID", $equip->getLabAssignedID());
        $info .= $this->getSubPropertyRow("Commission Date", $equip->getCommissionDate());
        $info .= $this->getSubPropertyRow("NEES Operated", ucfirst(strtolower($equip->getNeesOperated())));
        $info .= '<tr><td style="padding: 0px; font-weight:bold;">Scheduling:</td><td style="padding: 0px;">' . ($equip->getSeparateScheduling() ? 'Is ' : "Not ") . 'Scheduled Separately</td></tr>';
        $info .= $this->getSubPropertyRow("Calibration Information", $equip->getCalibrationInformation());
        $info .= $this->getSubPropertyRow("Note", $equip->getNote());

        return $info;
    }

    function getSubPropertyRow($label, $value) {
        if ($value && $value != "") {
            return '<tr><td nowrap="nowrap" style="width:175px; padding: 0px; font-weight:bold; padding-right:10px;">' . $label . ':</td><td style="padding: 0px;">' . $value . '</td></tr>';
        }

        return "";
    }




    /*
     * Return an array of all datafiles for this piece of equipment
     */
    function getDocList($equipment, $facility) {
        /* @var $equipment Equipment */
        $basepath = $equipment->getPathname();
        $dfs = DataFilePeer::findDataFileBrowser($basepath);
        $datafiles = array();

        foreach ($dfs as $df) {
            /* @var $df DataFile */
            $datafiles[] = $df;
        }

        // Lets pass these to the template
        return $datafiles;
    }

    
}
