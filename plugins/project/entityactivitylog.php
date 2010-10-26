<?php
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import library dependencies
jimport('joomla.event.plugin');

require_once 'lib/data/EntityTypePeer.php';
require_once 'lib/data/EntityActivityLogPeer.php';
require_once 'lib/data/EntityActivityLog.php';

 
class plgProjectEntityActivityLog extends JPlugin{
	
   /**
    * Constructor
    *
    * 
    */
  function plgProjectEntityActivityLog( &$subject ){
    parent::__construct( $subject );
 
    // load plugin parameters
    $this->_plugin = JPluginHelper::getPlugin( 'project', 'entityactivitylog' );
    $this->_params = new JParameter( $this->_plugin->params );
  }

  /**
   * Updates the view count for the current entity.
   * @global <type> $mainframe
   * @param array $params
   */
  function onUpdateViews(&$params){
    global $mainframe;

    $iEntityTypeId = $_REQUEST[EntityActivityLogPeer::ENTITY_TYPE_ID];
    $iEntityId = $_REQUEST[EntityActivityLogPeer::ENTITY_ID];

    EntityActivityLogPeer::updateViews($iEntityTypeId, $iEntityId);
  }
  
  /**
   * Returns the current view count for the requested entity.
   * @global <type> $mainframe
   * @param array $params
   */
  function onViews(&$params){
    global $mainframe;

    $iEntityTypeId = $_REQUEST[EntityActivityLogPeer::ENTITY_TYPE_ID];
    $iEntityId = $_REQUEST[EntityActivityLogPeer::ENTITY_ID];

    /* @var $oEntityActivityLog EntityActivityLog */
    $oEntityActivityLog = EntityActivityLogPeer::getEntityActivityLog($iEntityTypeId, $iEntityId);
    if(!$oEntityActivityLog){
      return 0;
    }
    return $oEntityActivityLog->getViewCount();
  }

  /**
   * Updates the download count for the current entity.
   * @global <type> $mainframe
   * @param array $params
   */
  function onUpdateDownloads(&$params){
    global $mainframe;

    $iEntityTypeId = $_REQUEST[EntityActivityLogPeer::ENTITY_TYPE_ID];
    $iEntityId = $_REQUEST[EntityActivityLogPeer::ENTITY_ID];

    EntityActivityLogPeer::updateDownloads($iEntityTypeId, $iEntityId);
  }

  /**
   * Returns the current download count for the requested entity.
   * @global <type> $mainframe
   * @param array $params
   */
  function onDownloads(&$params){
    global $mainframe;

    $iEntityTypeId = $_REQUEST[EntityActivityLogPeer::ENTITY_TYPE_ID];
    $iEntityId = $_REQUEST[EntityActivityLogPeer::ENTITY_ID];

    /* @var $oEntityActivityLog EntityActivityLog */
    $oEntityActivityLog = EntityActivityLogPeer::getEntityActivityLog($iEntityTypeId, $iEntityId);
    if(!$oEntityActivityLog){
      return 0;
    }
    return $oEntityActivityLog->getDownloadCount();
  }
 
}
?>
