<?PHP  //

   // add or edit an item
   
   require_once("../../config.php");
   require_once("lib.php");

   optional_variable($id);    // Course Module ID, or
   optional_variable($position);    // position of item
   optional_variable($typ, false);    // typ of item
   
   if(!$typ)redirect('edit.php?id=' . $id);

   // set up some general variables
   $usehtmleditor = can_use_html_editor(); 

   $formdata = data_submitted('nomatch');
   if (!empty($formdata->id)) {
    	$id = $formdata->id;   
   }
 
   if ($id) {
      if (! $cm = get_record("course_modules", "id", $id)) {
         error("Course Module ID was incorrect");
      }
      
      if (! $course = get_record("course", "id", $cm->course)) {
         error("Course is misconfigured");
      }
      
      if (! $feedback = get_record("feedback", "id", $cm->instance)) {
         error("Course module is incorrect");
      }
   }

   require_login($course->id);
   
   if(!(isteacher($course->id) || isadmin())){
      error(get_string('error'));
   }

   //action handler lines  -------------------------------------------
   feedback_action_handler($id, '/mod/feedback/edit.php'); 
   
   //item holen oder erstellen
   $formdata->itemid=isset($formdata->itemid)?$formdata->itemid:NULL;
   if($item = get_record('feedback_item', 'id', $formdata->itemid)){
      $typ = $item->typ;
      $position = $item->position;
   }else {
   	  $formdata->position=isset($formdata->position)?$formdata->position:NULL;
      $position = ($formdata->position == '')?$position:$formdata->position;
   
      if ($position == '')$position = 0;
	  $formdata->typ=isset($formdata->typ)?$formdata->typ:NULL;
      $typ = ($formdata->typ == '')?$typ:$formdata->typ;
      if ($typ == '')$typ = 0;

   }


/// Print the page header

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    }

    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");

    print_header("$course->shortname: $feedback->name", "$course->fullname",
                 "$navigation <a href=\"index.php?id=$course->id\">$strfeedbacks</a> -> $feedback->name", 
                  "", "", true, update_module_button($cm->id, $course->id, $strfeedback), 
                  navmenu($course, $cm));

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
      print_heading($feedback->name);
	  
	        
      //print errormsg
      if(isset($error)){echo $error;}
 
      //action handler error reported, affected by constant feedback_ACTIONS_DEBUG in picture\lib.php
	  feedback_print_errors();
            
      print_simple_box_start('center');
      echo '<form style="display:inline;" action="'.me().'" method="post">';
      echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
      
      //this div makes the buttons stand side by side
      echo '<div style="display:inline">';      
      $show_edit_func = 'show_edit_'.$typ;
      $show_edit_func($item, $usehtmleditor);
      echo '</div>';      
      echo '<input type="hidden" name="id" value="'.$id.'" />';
      echo '<input type="hidden" name="position" value="'.$position.'" />';
      echo '<input type="hidden" name="itemid" value="'.(isset($item->id)?$item->id:'').'" />';
      echo '<input type="hidden" name="typ" value="'.$typ.'" />';
      echo '<input type="hidden" name="feedbackid" value="'.$feedback->id.'" />';
      if(!empty($item->id)){
		 echo feedback_create_action_submit('updateitem_edit_item', array($item), get_string('update_item', 'feedback'),'edit.php?id='.$SESSION->feedback->coursemoduleid);
      }else{
  		 echo feedback_create_action_submit('createitem_edit_item', array(), get_string('save_item', 'feedback'),'edit.php?id='.$SESSION->feedback->coursemoduleid);
      }
	  echo feedback_create_action_submit('editcancel_edit_item', array(), get_string('cancel'),'edit.php?id='.$SESSION->feedback->coursemoduleid);

      echo '</form>';
      
      print_simple_box_end();
	  
	  //echo "<pre>";print_r($SESSION->feedback);echo "</pre>";

	  if ($typ!='label') {
	      echo '<script language="javascript">';
	      echo 'document.getElementById("itemname").focus()';
	      echo '</script>';
	  } 

/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

   print_footer($course);

?>
