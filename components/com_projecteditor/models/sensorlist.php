<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('sensors.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'lib/data/LocationPlanPeer.php';
require_once 'lib/data/LocationPlan.php';
require_once 'lib/data/Location.php';
require_once 'lib/data/LocationPeer.php';
require_once 'lib/data/MeasurementUnitPeer.php';
require_once 'lib/data/MeasurementUnit.php';
require_once 'lib/data/MeasurementUnitCategory.php';
require_once 'lib/data/MeasurementUnitCategoryPeer.php';

class ProjectEditorModelSensorList extends ProjectEditorModelSensors {

    /**
     * Constructor
     *
     * @since 1.5
     */
    function __construct() {
        parent::__construct();
    }

    
}
?>