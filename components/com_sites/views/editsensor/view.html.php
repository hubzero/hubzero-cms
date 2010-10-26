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
 
class sitesVieweditsensor extends JView
{
    public $sensorModels = null;
    public $sensorManufacturers = null;
    public $sensorTypes = null;

    function display($tpl = null)
    {

        // Grab facility from Oracle
        $facilityID = JRequest::getVar('id');
        $sensorID = Jrequest::getVar('sensorid', -1);

    	$facility = FacilityPeer::find($facilityID);
		
  	$fac_name = $facility->getName();
	$fac_shortname = $facility->getShortName();
        $this->assignRef( 'FacilityName', $fac_name);
        $this->assignRef( 'facility', $facility);

        // Page title and breadcrumb stuff
        $mainframe = &JFactory::getApplication();
        $document  = &JFactory::getDocument();          
        $pathway   =& $mainframe->getPathway();
        $document->setTitle( $fac_name );             

        // add or edit? (-1 will mean new)
        $this->assignRef('sensorid', $sensorID);

        // Add facility name to breadcrumb
        $pathway->addItem($fac_name, JRoute::_('index.php?option=com_sites&view=site&id=' . $facilityID));

        // Add Sensor to breadcrumb
        $pathway->addItem("Sensors", JRoute::_('index.php?option=com_sites&view=sensors&id=' . $facilityID));

        // Add current page to breadcrumb
        $pathway->addItem((($sensorID == -1) ? 'Add' : 'Edit'). ' Sensor' , JRoute::_('index.php?option=com_sites&view=editsensor&id=' . $facilityID) . '&sensorid=' . $sensorID);

        // Get the tabs for the top of the page
        $tabs = FacilityHelper::getFacilityTabs(4, $facilityID);
        $this->assignRef('tabs', $tabs); 

        // See if current logged in user can edit in this facility
	$allowEdit = FacilityHelper::canEdit($facility);
	$allowCreate = FacilityHelper::canCreate($facility);
	$this->assignRef('$allowEdit', $allowEdit);
	$this->assignRef('$allowCreate', $allowCreate);

        // Sensor type dropdown
        $this->assignRef('sensorTypes', $this->getSensorTypes());

        // Get Sensor Manufacturing information
        $this->assignRef('sensorManufacturers', $this->getSensorManufacturers());


        // If editing, grab curreent sensor information
        if($sensorID > -1)
        {
            $sensor = SensorPeer::find($sensorID);
            $sensorModel = $sensor->getSensorModel();

            $name = $sensor->getName();
            $serialNumber = $sensor->getSerialNumber();
            $localId = $sensor->getLocalId();
            $supplier = $sensor->getSupplier();
            $commissionDate = ($sensor->getCommissionDate());
            $decommissionDate =  ($sensor->getDecommissionDate());
            $sensorModelId = $sensor->getSensorModelId();
            $sensorTypeId = $sensorModel->getSensorTypeId();
            $manufacturer = $sensorModel->getManufacturer();
            
            $this->assignRef('name', $name);
            $this->assignRef('originalName', $name);
            $this->assignRef('serialNumber', $serialNumber);
            $this->assignRef('localId', $localId);
            $this->assignRef('supplier', $supplier);
            $this->assignRef('commissionDate', $commissionDate);
            $this->assignRef('decommissionDate', $decommissionDate);

            $this->assignRef('manufacturer', $manufacturer);
            $this->assignRef('selectedSensorTypeId', $sensorTypeId);
            $this->assignRef('sensorModelId', $sensorModelId);


        }
        else
        {
            //Specify which sensor type to select by default (for adds)
            $selectedSensorTypeId = 2; //accelerometer
            $this->assignRef('selectedSensorTypeId', $selectedSensorTypeId);

            $blank = '';
            $this->assignRef('name', $blank);
            $this->assignRef('originalName', $blank);
            $this->assignRef('serialNumber', $blank);
            $this->assignRef('localId', $blank);
            $this->assignRef('supplier', $blank);
            $this->assignRef('commissionDate', $blank);
            $this->assignRef('decommissionDate', $blank);

            $this->assignRef('manufacturer',  $blank);
            $this->assignRef('sensorModelId',  $blank);

       }

        
        if($sensorID != -1 && $allowEdit)
            parent::display($tpl);
        else
            echo 'You are not authorized to edit sensors';

        if($sensorID == -1 && $allowCreate)
            parent::display($tpl);
        else
            echo 'You are not authorized to create sensors';


    }


    function sortByName($a,$b)
    {
        return strcasecmp( $a->getName(), $b->getName() );
    }


    function getSensorTypes()
    {
        if($this->sensorTypes == NULL)
        {
            $this->sensorTypes = SensorTypePeer::findAll();
            usort($this->sensorTypes, array( $this, 'sortByName') );
        }

        return $this->sensorTypes;
    }


    function getSensorModels()
    {
        if($this->sensorModels == NULL)
        {
            $this->sensorModels = SensorModelPeer::findAll();
            usort($this->sensorModels, array( $this, 'sortByName') );
        }

        return $this->sensorModels;
    }


    function getSensorManufacturers()
    {
        if($this->sensorManufacturers == NULL)
        {
            $this->sensorManufacturers = array();
            $rs = SensorModelPeer::findAllManufacturers();
            $rs->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while($rs->next())
            {
                $man = $rs->get("MANUFACTURER");

                if (empty($man))
                    $man = "Unknown manufacturer";

                $this->sensorManufacturers[] = $man;
            }
        }

        return $this->sensorManufacturers;
    }

    // Generate the array of all sensor models, take the currently selected model and
    // generate a special javascipt function if needed
    public function sensorModelArray($sensorModelID)
    {

        $sensorModels = $this->getSensorModels();
        $sensorManufacturers = $this->getSensorManufacturers();
        $unknownSensorTypes = SensorTypePeer::findByName("Unspecified Sensor Type");

        $content = array();

        foreach ($sensorModels as $sm)
        {
            $smType = $sm->getSensorType();

            if (! $smType ) $smType = $unknownSensorType;

            $smTypeId = $smType->getId();
            $smMan = $sm->getManufacturer();

            if ( empty( $smMan )) $smMan = "Unknown manufacturer";

            $smManIdx = array_search($smMan, $sensorManufacturers);

            if ( $smManIdx === false ) { print $sm->getManufacturer(); exit; $smManIdx = -1; }

            $smName = $sm->getName();
            $smId = $sm->getId();
            $content[] = "addsm($smTypeId, $smManIdx, \"$smName\", $smId);";
        }

        # Select the current
        if ( $sensorModelID > -1)
        {
          $content[] = "selsm(" . $sensorModelID . ");";
        }

        # List the manufacturers and types
        
        $content[] = "\nvar manufacturers = [\"".join($sensorManufacturers,"\",\"")."\"];";

        $smTypes = $this->getSensorTypes();
        $type_content = array();
        
        foreach($smTypes as $smType)
        {
            $type_content[] = $smType->getId().":\"".$smType->getName()."\"";
        }
        $content[]="\nvar sensorTypes = {".join(",",$type_content)."};";

        $content[]="\n//".count($sensorManufacturers)." manufacturers";
        $content[]="//".count($smTypes)." types\n";

        return join("\n",$content);
        
  }








    
}
