<?PHP  //

   //deletes an item of the feedback
   
   require_once("../../config.php");
   require_once("lib.php");

   optional_variable($id);    // Course Module ID, or

   $formdata = data_submitted('nomatch');
   $id = ($formdata->id == '')?$id:$formdata->id;
   if(isset($formdata->canceldelete) && $formdata->canceldelete == 1){
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
   
   //delete item
   if(isset($formdata->confirmdelete) && $formdata->confirmdelete == 1){
      delete_feedback_item($formdata->deleteitem);
      redirect('edit.php?id=' . $id);
   }

    $strfeedbacks = get_string("modulenameplural", "feedback");
    $strfeedback  = get_string("modulename", "feedback");

	$navigation=empty($navigation)?'':$navigation;
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
      print_heading(get_string('are_you_sure_to_delete_this_item', 'feedback'));
      echo '<div align="center">(' . get_string('all_related_values_will_be_deleted','feedback') . ')';
?>
      <p>&nbsp;</p>
      <div>
         <form style="display:inline;" name="frm" action="<?php echo me();?>" method="post">
            <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey; ?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="deleteitem" value="<?php echo $formdata->deleteitem;?>" />
            <input type="hidden" name="confirmdelete" value="1" />
            <button type="submit"><?php print_string('delete');?></button>
         </form>
         <form style="display:inline;" name="frm" action="<?php echo me();?>" method="post">
            <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey; ?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="canceldelete" value="1" />
            <button type="submit"><?php print_string('cancel');?></button>
         </form>
      </div>
      <div style="clear:both">&nbsp;</div>
<?php      
      echo '</div>';
      print_simple_box_end();
      

   print_footer($course);

?>
