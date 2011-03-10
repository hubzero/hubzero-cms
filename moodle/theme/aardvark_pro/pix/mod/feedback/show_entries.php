<?PHP  // 

   require_once("../../config.php");
   require_once("lib.php");

   optional_variable($id);    // Course Module ID, or
   optional_variable($userid, false);
   
   $formdata = data_submitted('nomatch');
   
   if($userid) {
      $formdata->userid = intval($userid);
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


   //get the feedbackitems
   $feedbackitems = get_records('feedback_item', 'feedback', $feedback->id, 'position');
   $feedbackcompleted = get_record_select('feedback_completed','feedback='.$feedback->id.' AND userid='.$formdata->userid);

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
      
      //Elemente ausgeben
      if(is_array($feedbackitems)){
         $usr = get_record('user', 'id', $formdata->userid);
         if($feedbackcompleted) {
            echo '<p align="center">'.UserDate($feedbackcompleted->timemodified).'<br />('.fullname($usr).')</p>';
         } else {
            echo '<p align="center">'.get_string('not_completed_yet','feedback').'</p>';
         }
         print_simple_box_start("center", '50%');
         echo '<form>';
         echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
         echo '<table>';
         $itemnr = 0;
         foreach($feedbackitems as $feedbackitem){
            //value holen
            $value = get_record_select('feedback_value','completed ='.$feedbackcompleted->id.' AND item='.$feedbackitem->id);
            echo '<tr>';
            if($feedbackitem->hasvalue == 1) {
               $itemnr++;
               echo '<td valign="top">' . $itemnr . '.)&nbsp;</td>';
            } else {
               echo '<td>&nbsp;</td>';
            }
            
            print_feedback_item($feedbackitem, $value->value, true);
            echo '</tr>';
         }
         echo '<tr><td colspan="2" align="center">';
         echo '</td></tr>';
         echo '</table>';
         echo '</form>';
         print_simple_box_end();
      }
      print_continue('view.php?id='.$id);
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

   print_footer($course);

?>
