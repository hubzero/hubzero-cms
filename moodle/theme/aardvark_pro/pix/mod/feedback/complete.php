<?PHP  // $Id: complete.php,v 1.1.2.1 2006/01/09 20:49:15 andreas Exp $

/// This page prints a particular instance of feedback
/// (Replace feedback with the name of your module)

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
   
   //nur wenn nicht anonym, dann require_login()
   if($feedback->anonymous != feedback_ANONYMOUS_YES) {
      require_login($course->id);
      if(isguest()){
         error(get_string('error'));
      }
   }
   
   if((isteacher($course->id) || isadmin())){
      error(get_string('error'));
   }

   //action handler lines  -------------------------------------------
   feedback_action_handler($id); 
   
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
   
   //action handler error reported, affected by constant feedback_ACTIONS_DEBUG in picture\lib.php
   feedback_print_errors();
  
   
   //additional check for multiple-submit (prevent browsers back-button). the main-check is in view.php

   $feedback_can_submit = true;
   if($feedback->multiple_submit == 0 ) {
      if($multiple_count = get_record('feedback_tracking', 'userid', $USER->id, 'feedback', $feedback->id)) {
         $feedback_can_submit = false;
      }
   }
   if($feedback_can_submit) {

      //saving the items
      if(isset($formdata->savevalues) && $formdata->savevalues == 1){
         //checken, ob alle required items einen wert haben
         if(check_feedback_values($_POST)) {
            if($formdata->anonymous == feedback_ANONYMOUS_YES){
               $userid = 0;
            }else {
               $userid = $USER->id;
            }
            if($new_completed_id = save_feedback_values($_POST, $userid)){
               $savereturn = 'saved';
               if($userid > 0) {
                  add_to_log($course->id, "feedback", "submit", "view.php?id=$cm->id", "$feedback->name");
                  feedback_email_teachers($cm, $feedback, $course, $userid);
               }else {
                  feedback_email_teachers_anonym($cm, $feedback, $course, $userid);
               }
               //tracking the submit
               $multiple_count = null;
               $multiple_count->userid = $USER->id;
               $multiple_count->feedback = $feedback->id;
               $multiple_count->completed = $new_completed_id;
               $multiple_count->count = 1;
               insert_record('feedback_tracking', $multiple_count);
               
            }else {
               $savereturn = 'failed';
            }
         }else {
            $savereturn = 'missing';
         }
      }
   
      
      //get the feedbackitems
      $feedbackitems = get_records('feedback_item', 'feedback', $feedback->id, 'position');
      $feedbackcompleted = get_record_select('feedback_completed','feedback='.$feedback->id.' AND userid='.$USER->id);
   
   /// Print the main part of the page
   ///////////////////////////////////////////////////////////////////////////
   ///////////////////////////////////////////////////////////////////////////
   ///////////////////////////////////////////////////////////////////////////
      print_heading($feedback->name);
      if(isset($savereturn) && $savereturn == 'saved') {
         echo '<p align="center"><b><font color="green">'.get_string('entries_saved','feedback').'</font></b></p>';
         if($course->id == 1) {
            print_continue($CFG->wwwroot);
         } else {
            print_continue($CFG->wwwroot.'/course/view.php?id='.$course->id);
         }
      }else {
         if(isset($savereturn) && $savereturn == 'failed') {
            echo '<p align="center"><b><font color="red">'.get_string('saving_failed','feedback').'</font></b></p>';
            //print_continue('view.php?id='.$id);
         }
   
         if(isset($savereturn) && $savereturn == 'missing') {
            echo '<p align="center"><b><font color="red">'.get_string('saving_failed_because_missing_items','feedback').'</font></b></p>';
            //print_continue('view.php?id='.$id);
         }
   
         //Elemente ausgeben
         if(is_array($feedbackitems)){
            $itemnr = 0;
            print_simple_box_start('center', '75%');
            echo '<div align="center"><form name="frm" method="post" onsubmit=" ">';
            echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
            echo '<table>';
            switch ($feedback->anonymous) {
               //case feedback_ANONYMOUS_USER:
               //   echo '<tr><th colspan="3" align="center"><input type="checkbox" name="anonymous" value="1" checked="checked">&nbsp;'.get_string('anonymous', 'feedback').'</th></tr>';
               //   break;
               case feedback_ANONYMOUS_YES:
                  echo '<tr><th colspan="3" align="center"><input type="hidden" name="anonymous" value="1" />'.get_string('anonymous', 'feedback').'</th></tr>';
                  break;
               case feedback_ANONYMOUS_NO:
                  echo '<tr><td colspan="3" align="center"><input type="hidden" name="anonymous" value="0" />&nbsp;</td></tr>';
                  break;
            }
            //checken, ob required-elements existieren
            $countreq = count_records('feedback_item', 'feedback', $feedback->id, 'required', 1);
            if($countreq > 0) {
               echo '<tr><td colspan="3"><font color="red">(*)' . get_string('items_are_required', 'feedback') . '</font></td></tr>';
            }
            
            foreach($feedbackitems as $feedbackitem){
               //value holen //value aus POST-Daten wiederherstellen
               $frmvaluename = $feedbackitem->typ . '_'. $feedbackitem->id;
               $value =  isset($formdata->{$frmvaluename})?$formdata->{$frmvaluename}:NULL;
               
               echo '<tr>';
               if($feedbackitem->hasvalue == 1) {
                  $itemnr++;
                  echo '<td valign="top">' . $itemnr . '.)&nbsp;</td>';
               } else {
                  echo '<td>&nbsp;</td>';
               }
               print_feedback_item($feedbackitem, $value);
               echo '</tr>';
               echo '<tr><td>&nbsp;</td></tr>';
            }
            
            echo '</table>';
            echo '<input type="hidden" name="id" value="'.$id.'" />';
            echo '<input type="hidden" name="feedbackid" value="'.$feedback->id.'" />';
            echo '<input type="hidden" name="completedid" value="'.(isset($feedbackcompleted->id)?$feedbackcompleted->id:'').'" />';
            echo '<input type="hidden" name="savevalues" value="1" />';
            echo '<button type="submit">'.get_string('save_entries','feedback').'</button>';
            echo '</form>';
            
            if($course->id == 1) {
               echo '<form name="frm" action="'.$CFG->wwwroot.'" method="post" onsubmit=" ">';
            } else {
               echo '<form name="frm" action="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'" method="post" onsubmit=" ">';
            }
            echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
            echo '<button type="submit">'.get_string('cancel').'</button>';
            echo '</form>';
            echo '</div>';
            print_simple_box_end();
         }
      }
   }else {
      print_simple_box_start('center');
         echo '<h2><font color="red">'.get_string('feedback is already submittet', 'feedback').'</font></h2>';
         print_continue($CFG->wwwroot.'/course/view.php?id='.$course->id);
      print_simple_box_end();
   }
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

   print_footer($course);

?>
