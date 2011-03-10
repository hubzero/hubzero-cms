<?PHP  //

   //deletes a template
   
   require_once("../../config.php");
   require_once("lib.php");

   optional_variable($id);    // Course Module ID, or

   $formdata = data_submitted('nomatch');
   if (!empty($formdata->id)) {
    	$id = $formdata->id;   
   }
   
   if(isset($formdata->canceldelete) && $formdata->canceldelete == 1){
      redirect('edit.php?id='.$id);
   }

   if(isset($formdata->cancelconfirm) && $formdata->cancelconfirm == 1){
      redirect('delete_template.php?id='.$id);
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
   
   //delete template
   if(isset($formdata->confirmdelete) && $formdata->confirmdelete == 1){
      delete_feedback_template($formdata->deletetempl);
      redirect('delete_template.php?id=' . $id);
   }


    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");
	
	$navigation=isset($navigation)?$navigation:'';
    print_header("$course->shortname: $feedback->name", "$course->fullname",
                 "$navigation <a href=\"index.php?id=$course->id\">$strfeedbacks</a> -> $feedback->name", 
                  "", "", true, update_module_button($cm->id, $course->id, $strfeedback), 
                  navmenu($course, $cm));

/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
      print_heading(get_string('delete_template','feedback'));
      if(isset($formdata->shoulddelete) && $formdata->shoulddelete == 1) {
      
         print_simple_box_start("center", "60%", "#FFAAAA", 20, "noticebox");
         print_heading(get_string('are_you_sure_to_delete_this_template', 'feedback'));
         echo '<div align="center">';
?>
         <p>&nbsp;</p>
         <form style="display:inline;" name="frm" action="<?php echo me();?>" method="post">
            <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="deletetempl" value="<?php echo $formdata->deletetempl;?>" />
            <input type="hidden" name="confirmdelete" value="1" />
            <button type="submit"><?php print_string('delete');?></button>
         </form>
         
         <form style="display:inline;" name="frm" action="<?php echo me();?>" method="post">
            <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="cancelconfirm" value="1" />
            <button type="submit"><?php print_string('cancel');?></button>
         </form>
         <div style="clear:both">&nbsp;</div>
<?php      
         echo '</div>';
         print_simple_box_end();
      }else {
         $templates = get_feedback_template_list($course, true);
         echo '<div align="center">';
         if(!is_array($templates)) {
            print_simple_box(get_string('no_templates_available_yet', 'feedback'), "center");
         }else {
            echo '<table width="30%">';
            echo '<tr><th>'.get_string('templates', 'feedback').'</th><th>&nbsp;</th></tr>';
            foreach($templates as $template) {
               echo '<tr><td align="center">'.$template->name.'</td>';
               echo '<td align="center">';
               echo '<form action="'.me().'" method="post">';
               echo '<input title="'.get_string('delete_template','feedback').'" type="image" src="'.$CFG->pixpath .'/t/delete.gif" hspace="1" height=11 width=11 border=0 />';
               echo '<input type="hidden" name="deletetempl" value="'.$template->id.'" />';
               echo '<input type="hidden" name="shoulddelete" value="1" />';
               echo '<input type="hidden" name="id" value="'.$id.'" />';
               echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
               echo '</form>';
               echo '</td></tr>';
            }
            echo '</table>';
         }
?>
         <form name="frm" action="<?php echo me();?>" method="post">
            <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="canceldelete" value="0" />
            <button type="button" onclick="this.form.canceldelete.value=1;this.form.submit();"><?php print_string('cancel');?></button>
         </form>
         </div>
<?php
      }

   print_footer($course);

?>
