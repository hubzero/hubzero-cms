<?PHP  // 

   require_once("../../config.php");
   require_once("lib.php");

   optional_variable($id);    // Course Module ID, or
   optional_variable($lstgroupid, -2); //groupid (aus der Listbox gewaehlt)
   
   //checken, ob eine gruppe ausgewaehlt wurde
   if($lstgroupid == -1) {
      $SESSION->lstgroupid = false;
   }else {
      if(!isset($SESSION->lstgroupid) || $lstgroupid != -2)
         $SESSION->lstgroupid = $lstgroupid;
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

   if($feedback->anonymous != feedback_ANONYMOUS_YES) {
      require_login($course->id);
   }

   if($feedback->anonymous == feedback_ANONYMOUS_NO) {
      add_to_log($course->id, "feedback", "view", "view.php?id=$cm->id", "$feedback->name");
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
      print_simple_box_start('center');
      echo text_to_html($feedback->summary);
      print_simple_box_end();
      
      if(isteacher($course->id) || isadmin()){
         //get the effective groupmode of this course and module
         $groupmode = groupmode($course, $cm);
         
         //get students in conjunction with groupmode
         if($groupmode > 0) {
            if($SESSION->lstgroupid == -2) {
               if(isadmin()) {
                  $mygroupid = false;
                  $SESSION->lstgroupid = false;
               }else{
                  $mygroupid = mygroupid($course->id);
               }
            }else {
               $mygroupid = $SESSION->lstgroupid;
            }
            if($mygroupid) {
               $students = get_group_students($mygroupid, 'u.lastname ASC');
            } else {
               $students = get_course_students($course->id, $sort="u.lastname", $dir="ASC");
            }
         }else {
            $students = get_course_students($course->id, $sort="u.lastname", $dir="ASC");
         }
   	     $mygroupid=isset($mygroupid)?$mygroupid:NULL;
         $completedFeedbackCount = get_completeds_group_count($feedback, $mygroupid);
         echo '<div align="center"><a href="analysis.php?id=' . $id . '">';
         echo get_string('analysis', 'feedback') . ' ('.get_string('completed_feedbacks', 'feedback').': '.$completedFeedbackCount.')</a>';
         echo '</div>';
      }
      echo '<p>';
      print_simple_box_start('center');
      if(isteacher($course->id) || isadmin()){
         
         echo '<div align="center">';
         echo '<form action="edit.php?id='.$id.'" method="post">';
         echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
         echo '<input type="hidden" name="id" value="'.$id.'" />';
         echo '<button type="submit">'.get_string('edit_items', 'feedback').'</button>';
         echo '</form>';
         echo '<br />';
         echo '</div>';
         //liste der Studenten anzeigen
         print_simple_box_start('center');
         
         //available group modes (NOGROUPS, SEPARATEGROUPS or VISIBLEGROUPS)
         $feedbackgroups = get_groups($course->id);
         if(is_array($feedbackgroups) && $groupmode != SEPARATEGROUPS){
            //Dropdownliste zur Auswahl der Gruppe
            echo '<div align="center"><form action="'.me().'" method="get">';
            echo '<select name="lstgroupid" onchange="this.form.submit()">';
            echo '<option value="-1">'.get_string('allgroups').'</option>';
            foreach($feedbackgroups as $group) {
               if($group->id == $mygroupid){
                  $groupselect = ' selected="selected" ';
               } else {
                  $groupselect = '';
               }
               echo '<option value="'.$group->id . '"' . $groupselect .'>'.$group->name . '</option>';
            }
            echo '</select>';
            echo '<input type="hidden" name="id" value="'.$cm->id.'" />';
            echo '<input type="hidden" name="sesskey" value="'.$USER->sesskey.'" />';
            echo '</form></div>';
         }
         if (!$students)
         {
            notify(get_string('noexistingstudents'));
         } else
         {
            echo '<div align="center"><table><tr><td width="400">';
            //if($feedback->anonymous == feedback_ANONYMOUS_USER || $feedback->anonymous == feedback_ANONYMOUS_NO) {
            if($feedback->anonymous == feedback_ANONYMOUS_NO) {
               foreach ($students as $student)
               {
                  $completedCount = count_records_select('feedback_completed', 'userid = ' . $student->id . ' AND feedback = ' . $feedback->id);
                  if($completedCount > 0) {
                     $feedbackcompleted = get_record_select('feedback_completed','feedback='.$feedback->id.' AND userid='.$student->id);
                  ?>
                     <table width="100%">
                        <tr>
                           <td align="left">
                              <?php echo print_user_picture($student->id, $course->id, $student->picture, false, true);?>
                           </td>
                           <td align="left">
                              <?php echo fullname($student);?>
                           </td>
                           <td align="right">
                              <form name="frm<?php echo $student->id;?>" action="show_entries.php" method="post">
                                 <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
                                 <button type="submit" name="cmdShow" value="1"><?php print_string('show_entries','feedback');?></button>
                                 <input type="hidden" name="userid" value="<?php echo $student->id;?>" />
                                 <input type="hidden" name="id" value="<?php echo $id;?>" />
                              </form>
                           </td>
                           <td align="right">
                              <form name="frm<?php echo $student->id;?>" action="delete_completed.php" method="post">
                                 <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
                                 <button type="submit" name="cmdShow" value="1"><?php print_string('delete_entry','feedback');?></button>
                                 <input type="hidden" name="deletecompleted" value="<?php echo $feedbackcompleted->id;?>" />
                                 <input type="hidden" name="id" value="<?php echo $id;?>" />
                              </form>
                           </td>
                        </tr>
                     </table>
                  <?php
                  }
               }
            }
            //if($feedback->anonymous == feedback_ANONYMOUS_USER || $feedback->anonymous == feedback_ANONYMOUS_YES) {
            if($feedback->anonymous <= feedback_ANONYMOUS_YES) {
?>
               <hr />
               <form name="frm0" action="show_entries_anonym.php" method="post">
                  <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
                  <table width="100%"><tr>
                     <td align="center" colspan="2">
                        <?php print_string('anonymous_entries', 'feedback');?>&nbsp;(<?php echo count_records_select('feedback_completed', 'userid = 0 AND feedback = ' . $feedback->id);?>)
                     </td>
                     <td align="right">
                     <button type="submit" name="cmdShow" value="1"><?php print_string('show_entries','feedback');?></button>
                  </td></tr></table> 
                  <input type="hidden" name="userid" value="0" />
                  <input type="hidden" name="id" value="<?php echo $id;?>" />
               </form>
<?php
            }
            echo '</td></tr></table></div>';
         }
         print_simple_box_end();
      }else { //Interface for users
         //check multiple Submit
         $feedback_can_submit = true;
         if($feedback->multiple_submit == 0 ) {
            if($multiple_count = get_record('feedback_tracking', 'userid', $USER->id, 'feedback', $feedback->id)) {
               $feedback_can_submit = false;
            }
         }
         if($feedback_can_submit) {
            echo '<a href="complete.php?id='.$id.'">'.get_string('complete_the_form', 'feedback').'</a>';
         }else {
            echo '<h2><font color="red">'.get_string('this_feedback_is_already_submitted', 'feedback').'</font></h2>';
            print_continue($CFG->wwwroot.'/course/view.php?id='.$course->id);
         }
      }
      print_simple_box_end();
      echo "</p>";
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

   print_footer($course);

?>
