<?php 

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport('joomla.application.component.controller');

class CurateController extends JController{
	
  /**
   * Constructor
   *
   * @since 1.5
   */
  function __construct(){
	parent::__construct();
	
  }	
	
  /**
   * Method to display the view
   * 
   * @access    public
   */
  function display(){
  	$strViewName	= JRequest::getVar('view', 'search');
	JRequest::setVar('view', $strViewName );
    parent::display();
  }
  
  
}

?>