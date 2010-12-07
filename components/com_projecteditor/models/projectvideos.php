<?php

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

require_once('videos.php');


class ProjectEditorModelProjectVideos extends ProjectEditorModelVideos {

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