<?PHP  // 

   require_once("../../config.php");
   require_once("lib.php");

   optional_variable($id);    // Course Module ID, or
   optional_variable($templateid, false);    // Course Module ID, or

   $formdata = data_submitted('nomatch');
   if (!empty($formdata->id)) {
    	$id = $formdata->id;   
   }
   
  $templateid = ($formdata->templateid == '')?$templateid:$formdata->templateid;
   if(isset($formdata->canceladd) && $formdata->canceladd == 1){
      redirect('edit.php?id='.$id);
   }
   
   if(!$templateid) {
      redirect('edit.php?id='.$id);
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
   
   //if confirmed the template is used note: all of the old values will be deleted!
   if(isset($formdata->confirmadd) && $formdata->confirmadd == 1){
      items_from_feedback_template($feedback, $templateid);
      redirect('edit.php?id=' . $id);
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
      print_heading($feedback->name);
      
      print_simple_box_start("center", "60%", "#FFAAAA", 20, "noticebox");
      print_heading(get_string('are_you_sure_to_use_this_template', 'feedback'));
      echo '<div align="center">(' . get_string('all_old_values_will_be_deleted','feedback') . ')';
?>
      <p>&nbsp;</p>
      <form style="display:inline;" name="frm" action="<?php echo me();?>" method="post">
         <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
         <input type="hidden" name="id" value="<?php echo $id;?>" />
         <input type="hidden" name="templateid" value="<?php echo $templateid;?>" />
         <input type="hidden" name="confirmadd" value="1" />
         <button type="submit"><?php print_string('use_this_template', 'feedback');?></button>
      </form>
      <form style="display:inline;" name="frm" action="<?php echo me();?>" method="post">
         <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
         <input type="hidden" name="id" value="<?php echo $id;?>" />
         <input type="hidden" name="canceladd" value="1" />
         <button type="submit"><?php print_string('cancel');?></button>
      </form>
      <div style="clear:both">&nbsp;</div>
<?php      
      echo '</div>';
      print_simple_box_end();

      $templateitems = get_records('feedback_item', 'template', $templateid, 'position');
      if(is_array($templateitems)){
         $templateitems = array_values($templateitems);
      }

      if(is_array($templateitems)){
         $itemnr = 0;
         echo '<p align="center">'.get_string('preview', 'feedback').'</p>';
         print_simple_box_start('center', '75%');
         echo '<div align="center"><table>';
         foreach($templateitems as $templateitem){
            echo '<tr>';
            if($templateitem->hasvalue == 1) {
               $itemnr++;
               echo '<td valign="top">' . $itemnr . '.)&nbsp;</td>';
            } else {
               echo '<td>&nbsp;</td>';
            }
            print_feedback_item($templateitem);
            echo '</tr>';
            echo '<tr><td>&nbsp;</td></tr>';
         }
         echo '</table></div>';
         print_simple_box_end();
      }else{
         print_simple_box(get_string('no_items_available_at_this_template','feedback'),"center");
      }

   print_footer($course);

?>
