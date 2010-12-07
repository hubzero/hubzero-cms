<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('photos.php');


class ProjectEditorModelProjectPhotos extends ProjectEditorModelPhotos {

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