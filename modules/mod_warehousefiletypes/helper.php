<?php
/**
* @version		$Id: helper.php 11668 2009-03-08 20:33:38Z willebil $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2008 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');
require_once 'lib/data/DataFilePeer.php';

class modWarehouseFileTypesHelper{

  public function getDrawingCountByDirectory($p_strCurrentDirectory){
    return DataFilePeer::getDrawingCountByDirectory($p_strCurrentDirectory);
  }

  public function getPhotoCountByDirectory($p_strCurrentDirectory){
    return DataFilePeer::getPhotoCountByDirectory($p_strCurrentDirectory);
  }

  public function getFrameCaptureCountByDirectory($p_strCurrentDirectory){
    return DataFilePeer::getFrameCaptureCountByDirectory($p_strCurrentDirectory);
  }

  public function getMovieCountByDirectory($p_strCurrentDirectory){
    return DataFilePeer::getMovieCountByDirectory($p_strCurrentDirectory);
  }

  public function getObjectTypeCountByDirectory($p_strCurrentDirectory, $p_strObjectType){
    return DataFilePeer::getObjectTypeCountByDirectory($p_strCurrentDirectory, $p_strObjectType);
  }

  public function getDataFileCountByDirectory($p_strCurrentDirectory, $p_strTool=""){
    return DataFilePeer::getDataFileCountByDirectory($p_strCurrentDirectory, $p_strTool);
  }
}
