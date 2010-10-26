<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('base.php');
require_once 'api/org/nees/oracle/Suggest.php';


class ProjectEditorModelReview extends ProjectEditorModelBase {

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