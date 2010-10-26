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
 
class sitesVieweditequipment extends JView
{

    function display($tpl = null)
    {

        // Grab facility from Oracle
        $facilityID = JRequest::getVar('id');
        $equipmentID = JRequest::getVar('equipmentid', -1);
        $parentEquipmentID = JRequest::getVar('parentequipmentid', -1);
    	$facility = FacilityPeer::find($facilityID);
        $errorMsg = JRequest::getVar('errorMsg', '');
        $equipment = null;
		
  	$fac_name = $facility->getName();
	$fac_shortname = $facility->getShortName();
        $this->assignRef('FacilityName', $fac_name);
        $this->assignRef('facility', $facility);
        $this->assignRef('parentequipmentid', $parentEquipmentID);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name );             

        // add or edit? (-1 will mean new)
        $this->assignRef('equipmentid', $equipmentID);

        // Add facility name to breadcrumb
        $pathway->addItem($fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));

        // Add Equipment tab to breadcrumb
        $pathway->addItem("Equipment", JRoute::_('index.php?option=com_sites&view=majorequipment&id=' . $facilityID ));

        // Add current page to breadcrumb
        $pathway->addItem((($equipmentID == -1) ? 'Add' : 'Edit'). ' Equipment' , JRoute::_('index.php?option=com_sites&view=editequipment&id=' . $facilityID) . '&equipmentid=' . $equipmentID);

        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(3, $facilityID);
        $this->assignRef('tabs', $tabs); 


        // If editing, grab the equipment we are updating to populate form
        $classSpecificFields = '';
        if($equipmentID > -1)
        {
            $equipment = EquipmentPeer::find($equipmentID);
            $classSpecificFields =  $this->printClassForms($equipment);
        }
        else
        {
            $equipment = new Equipment();
            $classSpecificFields =  '';
        }

        $this->assignRef('classSpecificFields', $classSpecificFields);

        // If we're editing and got an error grab info from the previously submitted form, not the database
        $equipmentValues = array();
        if(!empty($errorMsg))
        {
            $equipmentValues['name'] = JRequest::getVar('name');
        }
        else
        {
            $equipmentValues['name'] = $equipment->getName();
            $equipmentValues['owner'] = $equipment->getOwner();
            $equipmentValues['serialnumber'] = $equipment->getSerialNumber();
            $equipmentValues['calibrationinformation'] = $equipment->getCalibrationInformation();
            $equipmentValues['note'] = $equipment->getNote();
            $equipmentValues['labassignedid'] = $equipment->getLabAssignedId();
            $equipmentValues['commissiondate'] = $equipment->getCommissionDate();
            
        }



        // Equipment Class dropdown options
        if($equipmentID > -1)
        {
            $equipmentClassDropDownList = $this->getEquipmentClassDropDownList($equipment->getEquipmentModel()->getEquipmentClass()->getID());
            $equipmentValues['previousequipmentclassid'] = $equipment->getEquipmentModel()->getEquipmentClass()->getID();
        }
        else
        {
            $equipmentClassDropDownList = $this->getEquipmentClassDropDownList('');
            $equipmentValues['previousequipmentclassid'] = -1;

        }
        $this->assignRef('equipmentClassDropDownList', $equipmentClassDropDownList);

        // Equipment Model logic
        $equippmentModelDropdownOptions = $this->getEquipmentModelDropDownList(
                (($equipmentID > -1) ? $equipment->getEquipmentModel()->getEquipmentClass()->getID() : '41'),
                (($equipmentID > -1) ? $equipment->getEquipmentModel()->getID() : ''));


        $this->assignRef('equippmentModelDropdownOptions', $equippmentModelDropdownOptions);


        // NEES Operated radio button options
        $operatedEquipmentOptions = $this->getNeesOperatedRadioButtons(($equipmentID > -1 ? $equipment->getNeesOperated() : '0'));
        $this->assignRef('operatedEquipmentOptions', $operatedEquipmentOptions);

        // Sepeareted Scheduling
        $separatedSchedulingOptions = $this->getSchedulingButtons( ($equipmentID > -1 ? $equipment->getSeparateScheduling() : '1') );
        $this->assignRef('separatedSchedulingOptions', $separatedSchedulingOptions);


        // See if current logged in user can edit in this facility
	$allowEdit = FacilityHelper::canEditSensorModel($facility);
        $allowCreate = FacilityHelper::canCreateSensorModel($facility);


        //Pass the main array for populating fields to the form
        $this->assignRef('equipmentValues', $equipmentValues);

        // Check rights to be here, even though the referring page won't display a link here
        // without rights, we need to stop the hackers too
        if($equipmentID == -1) // we're creating a new piece of equipment
        {
            if($allowCreate)
                parent::display($tpl);
            else
                echo 'You are not authorized to create equipment records';
        }
        else // we're doing an edit
        {
            if($allowEdit)
                parent::display($tpl);
            else
                echo 'You are not authorized to edit equipment records';
        }

    }


  function getSchedulingButtons($separated) {
        $values = array(1, 0);

        $s = "";
        foreach ($values as $value) {
            $s .= '<span style="padding-right:10px"><input type="radio" name="separated" value="' . $value . '"';
            if ($value == $separated) {
                $s .= " checked";
            }
            $s .= '/>' . ($value ? 'True' : 'False') . '</span>';
        }
        return $s;
    }

    

    public function printEquipmentModelJS() {
        $dbResult = EquipmentModelPeer::dumpEquipmentModelTable();
        $dbResult->setFetchMode(ResultSet::FETCHMODE_ASSOC);

        print "var models = new Object();\n";

        $recordedClasses = array();

        while ($dbResult->next()) {

            $equipment_class_id = $dbResult->get('CLASSID');

            if ($equipment_class_id) {
                if (!in_array($equipment_class_id, $recordedClasses)) {
                    $recordedClasses[] = $equipment_class_id;
                    print("models[{$equipment_class_id}] = new Object();\n");
                }
                print("models[" . $equipment_class_id . "][" . $dbResult->get('MODELID') . "] = '" . addslashes($dbResult->get('NAME')) . "';\n");
            }
        }
    }


    private function getNeesOperatedRadioButtons($neesOp)
    {
        $values = array("no", "partially", "fully");

        $s = "";
        foreach ($values as $value) {
            $s .= '<span style="padding-right:10px"> <input type="radio" name="neesOperated" value="' . strtoupper($value) . '"';
            if ((strtolower($value) == strtolower($neesOp))) {
                $s .= " checked";
            } elseif ($neesOp == "" && $value == "no") {
                $s .= " checked";
            }
            $s .= ' />' . ucfirst($value) . '</span>';
        }
        return $s;
    }


    // Generate the choose-equipment-class drop-down list.
    function getEquipmentClassDropDownList($equipmentClassId)
    {
        $classes = EquipmentClassPeer::findAll();
        usort($classes, array( $this, 'sortByName') );

        //var_dump($classes);

        $s = "";
        foreach ($classes as $class) {

            if($class->getClassName() == 'Actuator') continue;

            $s .= '<option value="' . $class->getId() . '"';

            if ($class->getId() == $equipmentClassId) {
                $s .= " selected";
            }
            $s .= '>' . $class->getClassName() . "</option>\n";
        }

        return $s;
    }


    // Used to support the usort function
    function sortByName($a,$b)
    {
        return strcasecmp( $a->getClassName(), $b->getClassName() );
    }


    function getEquipmentModelDropDownList($equipmentClassID, $selectedEquipmentModelID)
    {
        $models = EquipmentModelPeer::findByEquipmentClass($equipmentClassID);

        $s = "";
        foreach ($models as $model) {
            $s .= '<option value="' . $model->getId() . '"';

            if ($model->getId() == $selectedEquipmentModelID) {
                $s .= " selected";
            }
            $s .= '>' . $model->getName() . "</option>\n";
        }

        return $s;
    }


    function printClassForms($equipment)
    {
        $rv = "";

        if (!$equipment) {
            return;
        }

        if ($class = $equipment->getEquipmentModel()->getEquipmentClass()) {
            $classAttributes = EquipmentAttributeClassPeer::findByEquipmentClass($class->getId());

            foreach ($classAttributes as $classat) {
                $attr = $classat->getEquipmentAttribute();
                $rv .= $this->printAttrForm($equipment, $class, $classat, $attr, "[{$class->getId()}][{$classat->getId()}]");
            }
        }

        return $rv;

    }

    private function printAttrForm($equipment, $class, $classattr, $attr, $postfix = null)
    {
        $rv = "";

        // Believe it or not, we have attributes without labels...
        // DRB 8.17.2010 - With what I've seen, I have no trouble believing it
        if (!$attr || !$attr->getLabel())
            return;
        
        if ($attr->getDataType() == 'GROUP')
        {
            $rv .= "<tr><td colspan=\"2\"><h3>{$attr->getLabel()}</h3></td></tr>\n";
            $children = EquipmentAttributePeer::findByParent($attr->getId());
            if (count($children) == 0)
            {
                return;
            }

            $count = 0;

            foreach ($children as $child)
            {
                $this->printAttrForm($equipment, $class, $classattr, $child, $postfix);
                $count++;
            }

            $groupVals = $this->getGroupValues($attr->getName(), $equipment);
            foreach ($groupVals as $gattr => $gvalue)
            {
                $array = split(": ", $gvalue);
                $label = $array[0];

                $array1 = split(": ", $gattr);
                $gid = $array1[1];
                $aid = $array1[2];

                $array2 = split("  ", $array[1]);
                $gv = $array2[0];
                $gunit = $array2[1];

                $rv .= "<tr><td>{$label}</td><td><input type=\"text\" style=\"width:400px\" name=\"AttributeMap{$postfix}[{$aid}][{$gid}]\" value=\"" . htmlspecialchars($gv) . "\" />{$gunit}</td></tr>\n";
                //$this->getDescription($attr);
            }

            //$rv .= "<tr><td></td></tr>\n";
        }
        else
        {
            $value = '';
            
            if ($equipment)
            {
                $val = EquipmentAttributeValuePeer::findByEquipmentAndAttributeAndClass($equipment->getId(), $attr->getId(), $classattr->getId());
                if ($val) {
                    $value = $val->getValue();
                }
            }
            
            if (is_null($attr->getParent()))
            {
                $rv .=  "<tr><td>{$attr->getLabel()}</td><td><input type=\"text\" style=\"width:400px\" name=\"AttributeMap{$postfix}[{$attr->getId()}]\" value=\"" . htmlspecialchars($value) . "\" />\n";

                // Units!  We should make this a select box soon!
                $datatype = $attr->getDataType();
                $unit = $attr->getUnit();
                if (($datatype == 'INTEGER' || $datatype == 'NUMBER') && $unit && $unit->getName() != 'no unit')
                {
                    $rv .= $unit->getSymbol();
                }

                $rv .= $this->getDescription($attr);
                $rv .= "</td></tr>\n";
            }
        }

        return $rv;
    }

    function getDescription($attr)
    {
    // Print field description if we've got one and it's not the same as the label.
        $description = $attr->getDescription();
        if ($description && 0 != strcasecmp($description, $attr->getLabel()))
            return("<br />\n$description\n");
        else
            return '';

    }

   function getGroupValues($attr, $equip=null) {
        return EquipmentAttributePeer::findGroupValues($attr, $equip->getId());
    }

}// end class
