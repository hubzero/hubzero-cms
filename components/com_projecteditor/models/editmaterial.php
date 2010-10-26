<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('materials.php');
require_once 'api/org/nees/oracle/Suggest.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/SpecimenPeer.php';
require_once 'lib/data/MaterialPeer.php';
require_once 'lib/data/MaterialProperty.php';

class ProjectEditorModelEditMaterial extends ProjectEditorModelMaterials {

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