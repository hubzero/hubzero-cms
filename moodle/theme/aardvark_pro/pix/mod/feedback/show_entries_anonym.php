<?PHP  // 

   require_once("../../config.php");
   require_once("lib.php");

   optional_variable($id);    // Course Module ID, or
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


   //get the completeds
   $feedbackcompleteds = get_records_select('feedback_completed','feedback='.$feedback->id.' AND userid=0', 'timemodified');

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
      
      print_continue('view.php?id='.$id);
      //Liste mit anonym ausgefuellten Feedbacks anzeigen
      print_simple_box_start("center");
?>
      <script type="text/javascript">
         function go2delete(form)
         {
            form.action = "<?php echo $CFG->wwwroot;?>/mod/feedback/delete_completed.php";
            form.submit();
         }
      </script>

      <div align="center">
      <form name="frm" action="<?php echo me();?>" method="post">
         <table>
            <tr>
               <td>
                  <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
                  <select name="completedid" size="<?php echo (sizeof($feedbackcompleteds)>10)?10:5;?>" style="width:300;">
<?php
                  if(is_array($feedbackcompleteds)) {
                     $num = 1;
                     foreach($feedbackcompleteds as $compl) {
                        $selected = ($formdata->completedid == $compl->id)?'selected="selected"':'';
                        echo '<option value="'.$compl->id.'" '. $selected .'>'.UserDate($compl->timemodified) . '</option>';
                        $num++;
                     }
                  }
?>
                  </select>
                  <input type="hidden" name="showanonym" value="<?php echo feedback_ANONYMOUS_YES;?>" />
                  <input type="hidden" name="id" value="<?php echo $id;?>" />
               </td>
               <td valign="top">
                  <button type="submit"><?php print_string('show_entry', 'feedback');?></button><br />
                  <button type="button" onclick="go2delete(this.form);"><?php print_string('delete_entry', 'feedback');?></button>
               </td>
            </tr>
         </table>
      </form>
      </div>
<?php
      print_simple_box_end();
      if(!isset($formdata->completedid)) {
         $formdata = null;
      }
      //Elemente ausgeben
      if(isset($formdata->showanonym) && $formdata->showanonym == feedback_ANONYMOUS_YES) {
         //get the feedbackitems
         $feedbackitems = get_records('feedback_item', 'feedback', $feedback->id, 'position');
         $feedbackcompleted = get_record('feedback_completed', 'id', $formdata->completedid);
         if(is_array($feedbackitems)){
            if($feedbackcompleted) {
               echo '<p align="center">'.UserDate($feedbackcompleted->timemodified).'<br />('.get_string('anonymous', 'feedback').')</p>';
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
               if (isset($value->value)) {
                print_feedback_item($feedbackitem, $value->value, true);   
               }
               echo '</tr>';
            }
            echo '<tr><td colspan="2" align="center">';
            echo '</td></tr>';
            echo '</table>';
            echo '</form>';
            print_simple_box_end();
         }
      }
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

   print_footer($course);

?>
