<?php 
/*
 * -----------------------------------------------------------------------------------------
 * Action handler functions 
 * -----------------------------------------------------------------------------------------
 * The idea here is the following:
 * If feedback_action_handler() detects a $_POST variable, whose name starts with 'feedbackaction__',
 * feedback_action_handler() assumes that this means that a special action handler function has to be called. 
 * Only the $_POST variable name is important, its value is irrelevant. After action handling process has been completed
 * the value of the variable will be still available under a new name from which the part 'feedbackaction__'
 * has been removed, if someone wants to use it. (this is useful at least for image maps, if
 * we want to know the _x and _y coordinates of the mouse click on the image)
 * 
 * The special $_POST variable name shall have the following format
 * 	
 * 		feedbackaction__actionname__index
 * 
 * where
 * 	feedbackaction:		this part, referred to as a tag, is always the same
 * 	actionname:			the action handler function name will be constructed based on this part,
 * 						the function name will be feedback_handler_actionname()
 *  index:				array element $SESSION->feedback->actions['actionname_index']
 * 						will be an array whose alements will become the arguments for 
 * 						feedback_handler_actionname() (max 6 arguments will be handled)
 */

/**
 * constants for controlling error reporting
 */
define ('feedback_ACTION_DEBUG_SILENT', 1);
define ('feedback_ACTION_DEBUG_NORMAL', 2);
define ('feedback_ACTION_DEBUG_VERBOSE', 3);
$SESSION->feedback->debuglevel=feedback_ACTION_DEBUG_NORMAL;

/**
 * creates an action request onto an existing HTML form
 * 
 * creates a action request entry in $SESSION->feedback->$actions variable,
 * each entry contains the argument values for the action handler function in an array,
 * action can be activated from browser window by clicking the image ($picfile),
 * feedback_action_handler will automatically call a function
 * feedback_handler_$actionname()
 * 
 * @param string $actionname the name of the action to be defined, 
 * 			can contain only numbers, alphabet and underscore _ charater, 
 * 			two or more underscores __ are not allowed
 * @param array $params array containing the variables that will be forwarded to function feedback_handler_$actionname(),
 * 			count($param) must be less than 7
 * @param string $picfile picture file of the clickable image
 * @param string $title string for tool tip text, defaults to $actionname
 * @param string $redirect destination for possible redirect
 * @param array $hiddenvars array('name'=>value,...) name, value -pairs will be made hidden variables on a form
 * @return string HTML code to show the icon
 */
function feedback_create_action($actionname, $params = array(), $picfile = 'move.gif', $title = '', $redirect = '', $hiddenvars = array()) {
    global $CFG;
    global $SESSION;

    $actionname = substr($actionname, 0, 128); //take just the first 128 chars
    $actionname = eregi_replace('[^0-9a-z_]', '', $actionname); //remove all but numbers and alphabet and underscore to make it tidy
    $actionname = eregi_replace('_{2,}', '_', $actionname); //do not allow two or more underscores in succession
    
    if (count($params) > 6) {
        $SESSION->feedback->errors[] = feedback_action_error(get_string('max_args_exceeded', 'feedback'), $actionname);
        return ''; //too many arguments, we handle only six  
    } 

    $title = empty($title)?$actionname:$title; //if no title use actionname
    $picfile = empty($picfile)?$CFG->pixpath . '/t/move.gif':$picfile; //default icon
    if ((strpos($picfile, '/') === false) && (strpos($picfile, '\\') === false)) {
        $picfile = $CFG->pixpath . '/t/' . $picfile; //no path given, use default path
    } 

    if (!isset($SESSION->feedback->actions)) {
        $SESSION->feedback->actions = array();
    } 

    if (!isset($SESSION->feedback->errors)) {
        $SESSION->feedback->errors = array();
    } 

    $index = sizeof($SESSION->feedback->actions);
    $action = $actionname . '__' . $index;
    $SESSION->feedback->actions[$action] = $params;
    $SESSION->feedback->redirect[$action] = $redirect;

    $HTML = '<input type="image" title="' . $title . '" id="' . 'feedbackaction__' . $action . '" name="' . 'feedbackaction__' . $action . '" src="' . $picfile . '" hspace="1" height=11 width=11 border=0 />';

    foreach($hiddenvars as $name => $value) {
        if (!ctype_digit($name)) {
            $HTML .= '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value . '" />' . "\n";
        } 
    } 

    return $HTML;
} 

/**
 * creates an action request onto a separate HTML form
 * 
 * creates a action request entry in $SESSION->feedback->$actions variable,
 * each entry contains the argument values for the action handler function in an array,
 * action can be activated from browser window by clicking the image ($picfile),
 * feedback_action_handler will automatically call a function
 * feedback_handler_$actionname()
 * 
 * @param string $actionname the name of the action to be defined, 
 * 			can contain only numbers, alphabet and underscore _ charater, 
 * 			two or more underscores __ are not allowed
 * @param array $params array containing the variables that will be forwarded to function feedback_handler_$actionname()
 * 			count($param) must be less than 
 * @param string $picfile picture file of the clickable image
 * @param string $title string for tool tip text, defaults to $actionname
 * @param string $actionscript value of the the action atribute in a form tag, defaults to me()
 * @param array $hiddenvars array('name'=>value,...) name, value -pairs will be made hidden variables on a form
 * @return string HTML code to show the icon
 */
function feedback_create_action_form($actionname, $params, $picfile = '', $title = '', $actionscript = '', $hiddenvars = array()) {
    global $USER;
    global $SESSION;

    $actionscript = empty($actionscript)?me():$actionscript;

    $HTML = '<form action="' . $actionscript . '" method="post" >' . "\n";
    $HTML .= feedback_create_action($actionname, $params, $picfile, $title, '', $hiddenvars) . "\n";
    $HTML .= '<input type="hidden" name="id" id="id" value="' . $SESSION->feedback->coursemoduleid . '" />' . "\n";
    $HTML .= '<input type="hidden" name="sesskey" id="sesskey" value="' . $USER->sesskey . '" />' . "\n";
    $HTML .= "</form>\n";

    return $HTML;
} 

/**
 * creates a submit button action request onto an existing HTML form
 * 
 * creates a action request entry in $SESSION->feedback->$actions variable,
 * each entry contains the argument values for the action handler function in an array,
 * action can be activated from browser window by clicking the image ($picfile),
 * feedback_action_handler will automatically call a function
 * feedback_handler_$actionname()
 * 
 * @param string $actionname the name of the action to be defined, 
 * 			can contain only numbers, alphabet and underscore _ charater, 
 * 			two or more underscores __ are not allowed
 * @param array $params array containing the variables that will be forwarded to function feedback_handler_$actionname(),
 * 			count($param) must be less than 7
 * @param string $title submit button text, defaults to $actionname
 * @param string $redirect destination for possible redirect
 * @param array $hiddenvars array('name'=>value,...) name, value -pairs will be made hidden variables on a form
 * @return string HTML code to show the icon
 */
function feedback_create_action_submit($actionname, $params = array(), $title = '', $redirect = '', $hiddenvars = array()) {
    global $CFG;
    global $SESSION;

    $actionname = substr($actionname, 0, 128); //take just the first 128 chars
    $actionname = eregi_replace('[^0-9a-z_]', '', $actionname); //remove all but numbers and alphabet and underscore to make it tidy
    $actionname = eregi_replace('_{2,}', '_', $actionname); //do not allow two or more underscores in succession
    
    if (count($params) > 6) {
        $SESSION->feedback->errors[] = feedback_action_error(get_string('max_args_exceeded', 'feedback'), $actionname);
        return ''; //too many arguments, we handle only six
    } 

    $title = empty($title)?$actionname:$title; //if no title use actionname
    
    if (!isset($SESSION->feedback->actions)) {
        $SESSION->feedback->actions = array();
    } 

    if (!isset($SESSION->feedback->errors)) {
        $SESSION->feedback->errors = array();
    } 

    $index = sizeof($SESSION->feedback->actions);
    $action = $actionname . '__' . $index;
    $SESSION->feedback->actions[$action] = $params;
    $SESSION->feedback->redirect[$action] = $redirect;

    $HTML = '<input type="submit" value="' . $title . '" id="feedbackaction__' . $action . '" name="feedbackaction__' . $action . '" />';

    foreach($hiddenvars as $name => $value) {
        if (!ctype_digit($name)) {
            $HTML .= '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . $value . '" />' . "\n";
        } 
    } 

    return $HTML;
} 

/**
 * handles actions created by feedback_create_action function
 * 
 * actions are recognized from ordinary $_POST variables by their specific format,
 * all variables names of the format <br>
 * <br>
 * feedbackaction__actionname__index<br>
 * <br>
 * where<br>
 * 	feedbackaction:		always litterally the same string, indicates that this is an action request<br>
 * 	actionname:			name of the action to perform, feedback_handler_actionname will be called<br>
 *  index:				together with actionname defines the set of parameters (stored in array $SESSION->feedback->actions)
 * 						which will be passed to function feedback_handler_actionname<br>
 * <br>
 *
 * @param int $id course module id to be appended to $redirect when redirected 
 * @param string $redirect safe page to which execution flow can be redirected after error
 * @return boolean returns true on success
 */
function feedback_action_handler($id,$redirect='') {
    global $CFG;
    global $SESSION;
	
    $SESSION->feedback->coursemoduleid=$id; //just to remember the $id
	feedback_action_onerror($id,$redirect); // set a safe place for redirection after error
	
    $actions = feedback_collect_action_requests($_POST); 
    // do nothing if something is wrong at this stage
    if (!empty($SESSION->feedback->errors)) {
		$onerror=isset($SESSION->feedback->onerror)?$SESSION->feedback->onerror:'';
        unset($SESSION->feedback->actions);
        unset($SESSION->feedback->redirect);
		unset($SESSION->feedback->onerror);

		//if there is a defined place to go after error, redirect
		if (!empty($onerror)) {
	      header('Location: ' . $onerror);
          die();
		}
        return false;
    } 

    $lastredirect = ''; 
    // at present, only one action at a time, more actions could be defined in hidden input fields, for example
    foreach($actions as $action) {
        list($actionname, $index) = explode('__', $action);
        $handlername = 'feedback_handler_' . $actionname; 
        // extract function arguments from $SESSION->feedback->actions[$action]
        $actionarg_0 = null;
        $actionarg_1 = null;
        $actionarg_2 = null;
        $actionarg_3 = null;
        $actionarg_4 = null;
        $actionarg_5 = null;

        extract($SESSION->feedback->actions[$action], EXTR_PREFIX_ALL | EXTR_OVERWRITE, 'actionarg');
        $status = $handlername($actionarg_0, $actionarg_1, $actionarg_2, $actionarg_3, $actionarg_4, $actionarg_5); 
        // in the end, only the last redirect will be used
        $lastredirect = isset($SESSION->feedback->redirect[$action])?$SESSION->feedback->redirect[$action]:'';

        if (!$status) {
            unset($SESSION->feedback->actions);
            unset($SESSION->feedback->redirect);
            return false; //Stop at any error
        } 
    } 
    // all our actions handled
    unset($SESSION->feedback->actions);
    unset($SESSION->feedback->redirect); 
    // if there is a redirect requirement, handle it
    if (!empty($lastredirect)) {
        header('Location: ' . $lastredirect);
        die();
    } 
    return true;
} 

/**
 * collects all the variable names that match the feedbackaction format
 * 
 * actions are recognized from ordinary variables by this specific format:<br>
 * <br>
 * feedbackaction__actionname__index<br>
 * <br>
 * where<br>
 * 	feedbackaction:		always litterally the same string, indicates that this is an action request<br>
 * 	actionname:			name of the action to perform, feedback_handler_actionname will be called<br>
 *  index:				together with actionname defines the set of parameters (stored in array $SESSION->feedback->actions)
 * 						which will be passed to function feedback_handler_actionname<br>
 *<br>
 *  
 * @param array $arr array from which the action requests are searched for, typically $_POST
 * @return array returns valid action requests in an array
 */
function feedback_collect_action_requests(&$arr) {
    global $SESSION;
    $actions = array();

    foreach ($arr as $name => $value) {
        $name = substr($name, 0, 128); //take just the first 128 chars, avoid abuse
        $name = eregi_replace('[^0-9a-z_]', '', $name); //make sure that its alphanumeric or underscore
        $name = eregi_replace('_{3,}', '__', $name); //do not allow three or more underscores in succession 
        // explode main parts by '__'
        if (substr_count($name, '__') == 2) {
            list($tag, $actionname, $index_xy) = explode('__', $name, 3);
        } else {
            continue; //not a valid action request
        } 
        // if $index_xy contains actually _y or _x ending do a special thing
        if (substr_count($index_xy, '_') == 1) { // does index contain imagemap _x or _y extension
            list($index, $xy) = explode('_', $index_xy, 2);
        } elseif (substr_count($index_xy, '_') == 0) {
            $xy = '';
            $index = $index_xy;
        } else {
            continue; //not a valid action request
        } 

        $action = $actionname . '__' . $index;

        if ($tag != 'feedbackaction') {
            continue; //not a tag for an actionn request
        } 

        if (!isset($arr[$actionname . $xy])) { // if some reason $arr[$actionname.$xy] exists, it is not overwritten
            $arr[$actionname . $xy] = $arr[$name]; //we'll just remove the tag from variable name, the value may be used afterwards
            unset($arr[$name]); //remove the variable name, it has been stored
        } 

        if ($xy == 'x') {
            continue; //it was the _x value of an imagemap, we'll reject this, but act on _y value
        } 

        if (!isset($SESSION->feedback->actions[$action])) {
                $SESSION->feedback->errors[] = feedback_action_error(get_string('parameters_missing', 'feedback'), $action);
            continue; //action parameters missing
        } 

        list($handlername, $index) = explode('__', $action);
        $handler_name = 'feedback_handler_' . $handlername;
        if (!is_callable($handler_name)) {
                $SESSION->feedback->errors[] = feedback_action_error(get_string('no_handler', 'feedback'), $action);
            	continue; // no such action handler exists	
        } 

        $actions[] = $action;
    } 

    return $actions;
} 

/**
 * stores error messages in $SESSION->feedback->errors array
 * 
 * @param string $error error messge
 * @param string $action action during which error occured
 * @return string HTML for the error messages
 */
function feedback_action_error($error, $action = '') {
    global $SESSION; 
    // prepare an error report
    $retval = $error . ": <strong>$action</strong><br /><br />"; 
    // if we are debugging, dump some variables
    if ($SESSION->feedback->debuglevel==feedback_ACTION_DEBUG_VERBOSE) {

        ob_start();
        echo get_string('selected_dump', 'feedback').'<br /><br /><br /><div align="left">';
        echo '$SESSION->feedback->coursemoduleid:';
        print_object($SESSION->feedback->coursemoduleid);

        if (!empty($SESSION->feedback->actions[$action])) {
            echo "\$SESSION->feedback->actions[$action]:<br />";
            print_r($SESSION->feedback->actions[$action]);
        } else {
            echo '$SESSION->feedback->actions[$action]: EMPTY  <br />';
        } 

        if (empty($SESSION->feedback->redirect)) {
            echo '$SESSION->feedback->redirect: EMPTY  <br />';
        } else {
            echo '$SESSION->feedback->redirect<br />';
            print_object($SESSION->feedback->redirect);
        } 

        if (empty($SESSION->feedback->onerror)) {
            echo '$SESSION->feedback->onerror: EMPTY  <br />';
        } else {
            echo '$SESSION->feedback->onerror<br />';
            print_object($SESSION->feedback->onerror);
        } 
	
        echo '</div>';
        $retval .= ob_get_contents();
        ob_end_clean();
    } 
    return $retval;
} 

/**
 * prints errors from $SESSION->feedback->errors array and resets errors
 */
function feedback_print_errors() {
 
    global $SESSION;
		
    if(empty($SESSION->feedback->errors)) {
		return;
    }

    if($SESSION->feedback->debuglevel==feedback_ACTION_DEBUG_SILENT) {
		$SESSION->feedback->errors = array(); //just remove errors, no reporting
  	 	return;
    }
	
    print_simple_box_start("center", "60%", "#FFAAAA", 20, "noticebox");
    print_heading(get_string('handling_error', 'feedback'));

	if ($SESSION->feedback->debuglevel==feedback_ACTION_DEBUG_VERBOSE) {
	    echo '<p align="center"><b><font color="black"><pre>';
	    print_r($SESSION->feedback->errors) . "\n";
    	echo '</pre></font></b></p>';
	}
	
    print_simple_box_end();
    echo '<br /><br />';
    $SESSION->feedback->errors = array(); //remove errors
} 

/**
 * prepares a link in $SESSION->feedback->onerror to a safe page, which can be shown after error has occured
 * 
 * This is useful for redirecting after an error, because user can be redireted to 
 * a page where it is not possible to make any form submission using wrong
 * information
 * 
 * @param int $id course module id to be appended to the URL
 * @param string $redirect path+filename from moodle root to a page that will be shown after error
 */
function feedback_action_onerror($id='',$redirect='' ) {
    
	global $SESSION;
	global $CFG;
		
	if(empty($redirect)){
		unset($SESSION->feedback->onerror);
		return;
	}
		
	if(is_file($CFG->dirroot.$redirect)){
		if(empty($id)){
			$SESSION->feedback->onerror=$CFG->wwwroot.$redirect;		
		} else {
			$SESSION->feedback->onerror=$CFG->wwwroot.$redirect."?id=$id";		
		}
	} else {
		unset($SESSION->feedback->onerror);
	}
} 

/*
 * -----------------------------------------------------------------------------------------
 * This is the end of main action handler functions
 * -----------------------------------------------------------------------------------------
 */

/*
 * -----------------------------------------------------------------------------------------
 * Funtions below are action handler function for php.files in mod/feedback
 * (item related handlers are in the lib.php file of the iteam
 * -----------------------------------------------------------------------------------------
 */
// ------------------------------------------
// action handling functions for edit_item
// ------------------------------------------
function feedback_handler_editcancel_edit_item() {
    return true; //cancelled: do nothing
} 

function feedback_handler_updateitem_edit_item($item) {
    global $SESSION;

    $formdata = data_submitted('nomatch');

    if (!update_feedback_item($item, $formdata)) {
        $SESSION->feedback->errors[] = feedback_action_error(get_string('item_update_failed', 'feedback'), 'updateitem');
        return false;
    } 
    return true;
} 

function feedback_handler_createitem_edit_item() {
    global $SESSION;

    $formdata = data_submitted('nomatch');

    if (!create_feedback_item($formdata)) {
        $SESSION->feedback->errors[] = feedback_action_error(get_string('item_creation_failed', 'feedback'), 'createitem');
        return false;
    } 
    return true;
} 

?>
