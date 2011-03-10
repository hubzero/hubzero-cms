<?php  // $Id: resource.class.php, Jason Lambert

class resource_hub extends resource_base
{

	    /***
    * This function converts parameters stored in the alltext field to the proper
    * this->parameters object storing the special configuration of this resource type
    */
    function alltext2parameters($alltext) {
        /// set parameter defaults
        $alltextfield = new stdClass();
        $alltextfield->res_id=0;
        //$alltextfield->navigationbuttons=0;
        //$alltextfield->navigationmenu=1;
        //$alltextfield->skipsubmenus=1;
        //$alltextfield->navigationupbutton=1;

    /// load up any stored parameters
        if (!empty($alltext)) {
            $parray = explode(',', $alltext);
            foreach ($parray as $key => $fieldstring) {
                $field = explode('=', $fieldstring);
                $alltextfield->$field[0] = $field[1];
            }
        }

        return $alltextfield;
    }

    /***
    * This function converts the this->parameters attribute (object) to the format
    * needed to save them in the alltext field to store all the special configuration
    * of this resource type
    */
    function parameters2alltext($parameters) {
        $optionlist = array();

        $optionlist[] = 'res_id='.$parameters->res_id;
        $optionlist[] = 'tab='.$parameters->tab;
        //$optionlist[] = 'skipsubmenus='.$parameters->skipsubmenus;
        //$optionlist[] = 'navigationmenu='.$parameters->navigationmenu;
        //$optionlist[] = 'navigationupbutton='.$parameters->navigationupbutton;

        return implode(',', $optionlist);
    }

    /***
    * This function will convert all the parameters configured in the resource form
    * to a this->parameter attribute (object)
    */
    function form2parameters($resource) {
        $parameters = new stdClass;
        $parameters->res_id = isset($resource->param_res_id) ? $resource->param_res_id : 0;
        $parameters->tab = isset($resource->param_tab) ? $resource->param_tab: 'about';
        //$parameters->skipsubmenus = isset($resource->param_skipsubmenus) ? $resource->param_skipsubmenus : 0;
        //$parameters->navigationmenu = $resource->param_navigationmenu;
       // $parameters->navigationupbutton = isset($resource->param_navigationupbutton) ? $resource->param_navigationupbutton : 0;

        return $parameters;
    }
 
    function resource_hub($cmid=0)
    {
        parent::resource_base($cmid);
		
		/// prevent notice
        if (empty($this->resource->alltext)) {
            $this->resource->alltext='';
        }
    /// set own attributes
        $this->parameters = $this->alltext2parameters($this->resource->alltext);

    }
 
    function display()
    {
        ///Display the resource
 
        global $CFG;
       
		
		
		$cm = $this->cm;
		$course = $this->course;
        $resource = $this->resource;
		
		//write to moodle log
		add_to_log($course->id, "resource", "view", "view.php?id={$cm->id}", $resource->id, $cm->id);

		//start displaying
		parent::display();
		
		$pagetitle = strip_tags($course->shortname.': '.format_string($resource->name));
		$navigation = build_navigation($this->navlinks, $cm);
                
                print_header($pagetitle, $course->fullname, $navigation,
                        "", "", true, update_module_button($cm->id, $course->id, $this->strresource),
                        navmenu($course, $cm));
						
		
		$inpopup = optional_param('inpopup', 0, PARAM_BOOL);
        $page    = optional_param('page', 0, PARAM_INT);
        $frameset= optional_param('frameset', '', PARAM_ALPHA);
		
		$parameters = $this->alltext2parameters($resource->alltext);
		
		
		//echo print_r($resource);
		//echo print_r($parameters->res_id);
		
		$tab = "";
		
		switch ($parameters->tab)
		{
			case 0:
				$tab = 'play/?tmpl=component&no_html=1';
				break;
			case 1:
				$tab = 'about/?tmpl=component';
				break;
		}
		
		$width = ($inpopup)? '100%' : '100%';
		
		
		echo '<iframe src="'.$CFG->resource_websearch.'/'.$parameters->res_id.'/'.$tab.'" width="'.$width.'" height="600px;" style="border:none;"/>'."\n";
			
    }
 
    function add_instance($resource)
    {
		$this->_postprocess($resource);
        return parent::add_instance($resource);
    }
 
    function update_instance($resource)
    {
		$this->_postprocess($resource);
        return parent::update_instance($resource);
    }
	function _postprocess(&$resource) {
        global $RESOURCE_WINDOW_OPTIONS;
        $alloptions = $RESOURCE_WINDOW_OPTIONS;

            if ($resource->windowpopup) {
        $optionlist = array();
        foreach ($alloptions as $option) {
            $optionlist[] = $option."=".$resource->$option;
            unset($resource->$option);
        }
        $resource->popup = implode(',', $optionlist);
        unset($resource->windowpopup);
        $resource->options = '';

    } else {
        if (empty($resource->blockdisplay)) {
            $resource->options = '';
        } else {
            $resource->options = 'showblocks';
        }
        unset($resource->blockdisplay);
        $resource->popup = '';
    }
    /// Load parameters to this->parameters
        $this->parameters = $this->form2parameters($resource);
    /// Save parameters into the alltext field
        $resource->alltext = $this->parameters2alltext($this->parameters);
    }
 
    function delete_instance($resource)
    {
        return parent::delete_instance($resource);
    }
 
    function setup_elements(&$mform)
    {
	    global $CFG, $RESOURCE_WINDOW_OPTIONS;

		//$mform->addElement('htmleditor', 'alltext', get_string('fulltext', 'resource'), array('cols'=>85, 'rows'=>30));
		//$mform->setType('alltext', PARAM_RAW);
		//$mform->setHelpButton('alltext', array('reading', 'writing', 'richtext'), false, 'editorhelpbutton');
		//$mform->addRule('alltext', get_string('required'), 'required', null, 'client');
		$mform->addElement('text', 'param_res_id', 'ID of hub resource');
		$mform->setType('param_res_id', PARAM_INT);
		if (!empty($CFG->resource_websearch)) {
            $searchbutton = $mform->addElement('button', 'searchbutton', 'Search for a Resource'.'...');
            $buttonattributes = array('title'=>get_string('searchweb', 'resource'), 'onclick'=>"return window.open('"
                              . "$CFG->resource_websearch', 'websearch', 'menubar=1,location=1,directories=1,toolbar=1,"
                              . "scrollbars,resizable,width=1024,height=768');");
            $searchbutton->updateAttributes($buttonattributes);
        }

		$mform->addElement('header', 'displaysettings', 'Display Settings');

		$toptions = array(0 => 'play', 1 => 'about');
		$mform->addElement('select', 'param_tab', 'Which tab to open on click?', $toptions);
		$mform->setDefault('param_tab', !empty($CFG->resource_popup));
		
		$woptions = array(0 => get_string('pagewindow', 'resource'), 1 => get_string('newwindow', 'resource'));
		$mform->addElement('select', 'windowpopup', get_string('display', 'resource'), $woptions);
		$mform->setDefault('windowpopup', !empty($CFG->resource_popup));
		


		$mform->addElement('checkbox', 'blockdisplay', get_string('showcourseblocks', 'resource'));
		$mform->setDefault('blockdisplay', 0);
		$mform->disabledIf('blockdisplay', 'windowpopup', 'eq', '1');
		$mform->setAdvanced('blockdisplay');
		
		foreach ($RESOURCE_WINDOW_OPTIONS as $option) {
        if ($option == 'height' or $option == 'width') {
            $mform->addElement('text', $option, get_string('new'.$option, 'resource'), array('size'=>'4'));
            $mform->setDefault($option, $CFG->{'resource_popup'.$option});
            $mform->disabledIf($option, 'windowpopup', 'eq', '0');
        } else {
            $mform->addElement('checkbox', $option, get_string('new'.$option, 'resource'));
            $mform->setDefault($option, $CFG->{'resource_popup'.$option});
            $mform->disabledIf($option, 'windowpopup', 'eq', '0');
        }
        $mform->setAdvanced($option);
    }
		
    }
 
    function setup_preprocessing(&$defaults)
    {
		if (!isset($defaults['popup'])) {
            // use form defaults

        } else if (!empty($defaults['popup'])) {
            $defaults['windowpopup'] = 1;
            if (array_key_exists('popup', $defaults)) {
                $rawoptions = explode(',', $defaults['popup']);
                foreach ($rawoptions as $rawoption) {
                    $option = explode('=', trim($rawoption));
                    $defaults[$option[0]] = $option[1];
                }
            }
        } else {
            $defaults['windowpopup'] = 0;
        }
        //Converts the alltext to form fields
        if (!empty($defaults['alltext'])) {
            $parameters = $this->alltext2parameters($defaults['alltext']);
            $defaults['param_res_id']    = $parameters->res_id;
            $defaults['param_tab']  = $parameters->tab;
            //$defaults['param_skipsubmenus']       = $parameters->skipsubmenus;
            //$defaults['param_navigationmenu']     = $parameters->navigationmenu;
           // $defaults['param_navigationupbutton'] = $parameters->navigationupbutton;
        }
    }
 
}

?>