<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('uploadsensors.php');

class ProjectEditorModelSensorTypes extends ProjectEditorModelUploadSensors {

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