<?php

/**
 * @version		$Id: controller.php 13338 2009-10-27 02:15:55Z ian $
 * @package		Joomla
 * @subpackage	Content
 * @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses. See COPYRIGHT.php for copyright notices and
 * details.
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

set_include_path("/usr/local/propel/runtime/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("api/org/phpdb/propel/central/classes" . PATH_SEPARATOR . get_include_path());
set_include_path("api/org/nees" . PATH_SEPARATOR . get_include_path());

spl_autoload_register('__autoload');
require_once 'propel/Propel.php';
Propel::init("api/org/phpdb/propel/central/conf/central-conf.php");

jimport('joomla.application.component.controller');

require_once "lib/interface/helpers.inc";
require_once "lib/filesystem/FileCommandAPI.php";
require_once "lib/data/DataFile.php";

/**
 * Content Component Controller
 *
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class DataController extends JController {

    /**
     * Constructor
     *
     * @since 1.5
     */
    function __construct() {
        parent::__construct();

        $this->registerTask('get', 'getFile');
        $this->registerTask('put', 'putFile');
        $this->registerTask('view', 'viewFile');
        $this->registerTask('show', 'showFile');
    }

    /**
     * Method to display the view
     *
     * @access    public
     */
    function display() {
        parent::display();
    }

    /**
     * Grabs the file per user's request.
     *
     */
    function getFile() {
        $strSource = JRequest::getVar("path", "");

        if (!$strSource || $strSource == "") {
            return;
        }

        $strPath = FileCommandAPI::set_directory($strSource);
        echo $strPath."<br>";

        // Datafile is not found on disk
        if (!file_exists($strPath)) {
            echo "Data file not found<br>";
            return;
        } 

        $oDataFile = DataFilePeer::findByFullPath($strPath);

        // Datafile is not found on database
        if (is_null($oDataFile)) {
            /*
             * Added the following block for thumbnails
             * and display (800x600) photos.  These files
             * are not stored in the database, just on the NFS.
             * display_<id>_name.jpg.  You can find the DataFile
             * from the id portion of the string.
             *
             * 20100709
             */
            $strPathArray = explode("/", $strPath);
            $strName = end($strPathArray);
            $strNameArray = $strNameArray = explode("_", $strName);
            if($strNameArray[0]=="thumb" || $strNameArray[0]=="display"){
              $oDataFile = DataFilePeer::find($strNameArray[1]);
            }

            if (is_null($oDataFile)) {
              echo "DataFile not found<br>";
              return;
            }
        }

        $bCanAccessFile = $this->hasAccess($oDataFile, $strPath);


        // See if they can view the page
        if ($bCanAccessFile) {
            $this->render($strSource);
        } else {
            $this->notAllowed();
        }

        exit;
    }

    /**
     * Grabs the file per user's request.
     *
     */
    function showFile() {
        $strSource = JRequest::getVar("path", "");
        //echo "path=" . $strSource . "<br>";
        if (!$strSource || $strSource == "") {
            return;
        }

        $strPath = FileCommandAPI::set_directory($strSource);

        // Datafile is not found on disk
        if (!file_exists($strPath)) {
            echo "Data file not found<br>";
            return;
        }

        if (true) {
            $this->render($strSource);
        } else {
            $this->notAllowed();
        }

        exit;
    }

    function putFile() {

    }

    function viewFile() {

    }

    /**
     * Check user's access
     *
     */
    private function hasAccess($p_oDataFile, $p_strPath) {
        $bCanAccessFile = false;

        if ($p_oDataFile->getView() == "PUBLIC") {
            $bCanAccessFile = true;
        } elseif (preg_match("/\/nees\/home\/(facility.groups|Facilities.groups)/", $p_strPath)) {
            $bCanAccessFile = true;
        } elseif ($p_oDataFile->isInPublicDir()) {
            $bCanAccessFile = true;
        } else {
            $oEntity = $p_oDataFile->getOwner($p_strPath);
            if ($oEntity) {
                if ($oEntity->isPublished()) {
                    $bCanAccessFile = true;
                } else {
                    $oUserManager = UserManager::getInstance();
                    if ($oUserManager->isMember($oEntity)) {
                        $bCanAccessFile = true;
                    }
                }
            }
        }

        return $bCanAccessFile;
    }

    private function render($p_strSource) {
        // print file if it exists
        $oFile = FileCommandAPI::create($p_strSource);
        $bStatus = $oFile->printFile();


        // Something bad happened when we tried to print the file
        if (!$bStatus) {
            if ($_REQUEST["Mode"] == "API") {
                echo "cannot get the file";
                return;
            } else {
                header("Location: /datafile_error.php?file=$p_strSource&accsdata={$_SERVER['PATH_INFO']}");
            }
        }
    }

    private function notAllowed() {
        //  Not allowed to view the file - display an error message for API or for gui
        if ($_REQUEST["Mode"] == "API") {
            echo "You do not have permission to access the file";
            return;
        } else {
            header("Location: /error.php?accsdata={$_SERVER['PATH_INFO']}");
        }
    }

}
?>