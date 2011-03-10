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
   
   //action handler lines  -------------------------------------------
   feedback_action_handler($id); 
 
   //move up/down items
   if(isset($formdata->moveupitem) && $formdata->moveupitem > 0){
      $item = get_record('feedback_item', 'id', $formdata->moveupitem);
      moveup_feedback_item($item);
   }
   if(isset($formdata->movedownitem) && $formdata->movedownitem > 0){
      $item = get_record('feedback_item', 'id', $formdata->movedownitem);
      movedown_feedback_item($item);
   }
   
   if(isset($formdata->switchitemrequired) && $formdata->switchitemrequired > 0) {
      $item = get_record('feedback_item', 'id', $formdata->switchitemrequired);
      switch_feedback_item_required($item);
   }
   
   if(isset($formdata->savetemplate) && $formdata->savetemplate == 1) {
      if(trim($formdata->templatename) == '')
      {
         $savereturn = 'notsaved_name';
      }else {
	     $formdata->public=isset($formdata->public)?$formdata->public:0;
         if(!save_as_feedback_template($feedback, $formdata->templatename, $formdata->public?1:0))
         {
            $savereturn = 'failed';
         }else {
            $savereturn = 'saved';
         }
      }
   }

   //get the feedbackitems
   $lastposition = 0;
   $feedbackitems = get_records('feedback_item', 'feedback', $feedback->id, 'position');
   if(is_array($feedbackitems)){
      $feedbackitems = array_values($feedbackitems);
      $lastitem = $feedbackitems[count($feedbackitems)-1];
      $lastposition = $lastitem->position;
   }
   $lastposition++;
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
	
	  $savereturn=isset($savereturn)?$savereturn:'';
	  
      if($savereturn == 'notsaved_name') {
         echo '<p align="center"><b><font color="red">'.get_string('name_required','feedback').'</font></b></p>';
      }

      if($savereturn == 'saved') {
         echo '<p align="center"><b><font color="green">'.get_string('template_saved','feedback').'</font></b></p>';
      }
      
      if($savereturn == 'failed') {
         echo '<p align="center"><b><font color="red">'.get_string('saving_failed','feedback').'</font></b></p>';
      }

      //action handler error reported, affected by constant feedback_ACTIONS_DEBUG in picture\lib.php
  	  feedback_print_errors();
      
      print_continue('view.php?id='.$id);
      echo '<div align="center"><table width="70%"><tr><td valign="top">';
      print_simple_box_start("center");
?>
      <form action="edit_item.php" method="get">
         <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
      <table>
         <tr><th colspan="2"><?php print_string('add_items','feedback');?></th></tr>
         <tr><td>
         <select id="typ" name="typ" onchange="document.location.href='edit_item.php?id=<?php echo $id . '&amp;position=' . $lastposition; ?>&amp;typ=' + this.form.typ.value;">
            <option value=""><?php print_string('select');?></option>
<?php            
            //print dropdown with feedback-item-types
            foreach($feedback_names as $fn) {
               echo '<option value="'.$fn.'">'.$fn."</option>\n";
            }
?>
         </select>
         </td><td>
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="hidden" name="position" value="<?php echo $lastposition;?>" />
            <button type="submit"><?php print_string('add_item', 'feedback');?></button>
         </td></tr>
         </table>
      </form>
<?php
      print_simple_box_end();
      echo '</td><td valign="top">';
      print_simple_box_start("center");
      $templates = get_feedback_template_list($course);//get the templates
?>
      <table>
      <tr><th colspan="2"><?php print_string('templates','feedback');?></th></tr>
      <tr><td valign="top">
         <form action="use_templ.php" method="post">
            <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <table><tr><td align="center" valign="top">
         <?php print_string('using_templates', 'feedback');?>
         </td></tr><tr><td align="center" valign="top">
            <select id="templateid" name="templateid">
<?php
               //print dropdown with templates
			   $notemplates = false;
               if(!is_array($templates)) {
                  $notemplates = true;
               }else {
                  echo '<option value="">'.get_string('select').'</option>';
                  foreach($templates as $template) {
                     echo '<option value="'.$template->id.'">'.$template->name.'</option>';
                  }
               }
?>
            </select>
         </td></tr><tr><td align="center" valign="top">
<?php
            if($notemplates) {
               print_string('no_templates_available_yet', 'feedback');
            }else {
?>
            <button type="submit"><?php print_string('use_this_template', 'feedback');?></button>
<?php
            }
?>
         </td></tr></table>
         </form>
      </td><td valign="top">
         <form action="<?php echo me();?>" method="post">
         <input type="hidden" name="sesskey" value="<?php echo $USER->sesskey;?>" />
         <table><tr><td align="center">
         <?php print_string('creating_templates', 'feedback');?>
         </td></tr><tr><td align="center">
            <input type="text" name="templatename" size="40" maxlength="200" />
         </td></tr><tr><td align="center">
            <input type="hidden" name="savetemplate" value="1" />
            <input type="hidden" name="id" value="<?php echo $id;?>" />
            <input type="checkbox" name="public" value="1" /><?php print_string('public','feedback');?>&nbsp;
            <button type="submit"><?php print_string('save_as_new_template', 'feedback');?></button>
         </td></tr></table>
         </form>
      </td></tr>
      <tr><td colspan="2" align="center">
         <a href="delete_template.php?id=<?php echo $id;?>"><?php print_string('delete_templates', 'feedback');?></a>
      </td></tr></table>
<?php
      print_simple_box_end();
      echo '</td></tr></table></div>';
      if(is_array($feedbackitems)){
         $itemnr = 0;
         
         $helpbutton = helpbutton('preview', get_string('preview','feedback'), 'feedback',true,false,'',true);
         
         print_heading($helpbutton . get_string('preview', 'feedback'));
         print_simple_box_start('center', '80%');
         echo '<div align="center"><table>';
         
         //print the inserted items
         foreach($feedbackitems as $feedbackitem){
            echo '<tr>';
            //Items ohne value sind nur zur Beschriftung
            if($feedbackitem->hasvalue == 1) {
               $itemnr++;
               echo '<td valign="top">' . $itemnr . '.)&nbsp;</td>';
            } else {
               echo '<td>&nbsp;</td>';
            }
            print_feedback_item($feedbackitem);
            echo '<td>';
            if($feedbackitem->position > 1){
               //schalter item hochschieben
               echo '<form action="'.me().'" method="post">';
               echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
               echo '<input title="'.get_string('moveup_item','feedback').'" type="image" src="'.$CFG->pixpath .'/t/up.gif" hspace="1" height=11 width=11 border=0 />';
               echo '<input type="hidden" name="moveupitem" value="'.$feedbackitem->id.'" />';
               echo '<input type="hidden" name="id" value="'.$id.'" />';
               echo '</form>';
            }else{
               echo '&nbsp;';
            }
            echo '</td>';
            echo '<td>';
            if($feedbackitem->position < $lastposition - 1){
               //schalter item runterschieben
               echo '<form action="'.me().'" method="post">';
               echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
               echo '<input title="'.get_string('movedown_item','feedback').'" type="image" src="'.$CFG->pixpath .'/t/down.gif" hspace="1" height=11 width=11 border=0 />';
               echo '<input type="hidden" name="movedownitem" value="'.$feedbackitem->id.'" />';
               echo '<input type="hidden" name="id" value="'.$id.'" />';
               echo '</form>';
            }else{
               echo '&nbsp;';
            }
            echo '</td>';
            echo '<td>';
               //schalter item bearbeiten
            echo '<form action="edit_item.php" method="post">';
            echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
            echo '<input title="'.get_string('edit_item','feedback').'" type="image" src="'.$CFG->pixpath .'/t/edit.gif" hspace="1" height=11 width=11 border=0 />';
            echo '<input type="hidden" name="itemid" value="'.$feedbackitem->id.'" />';
            echo '<input type="hidden" name="typ" value="'.$feedbackitem->typ.'" />';
            echo '<input type="hidden" name="id" value="'.$id.'" />';
            echo '</form>';
            echo '</td>';
            echo '<td>';
               //schalter item switch required
            if($feedbackitem->hasvalue == 1) {
               echo '<form action="'.me().'" method="post">';
               echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
               if($feedbackitem->required == 1) {
                  echo '<input title="'.get_string('switch_item_to_not_required','feedback').'" type="image" src="pics/required.gif" hspace="1" height=11 width=11 border=0 />';
               } else {
                  echo '<input title="'.get_string('switch_item_to_required','feedback').'" type="image" src="pics/notrequired.gif" hspace="1" height=11 width=11 border=0 />';
               }
               echo '<input type="hidden" name="switchitemrequired" value="'.$feedbackitem->id.'" />';
               echo '<input type="hidden" name="id" value="'.$id.'" />';
               echo '</form>';
            }else {
               echo '&nbsp;';
            }
            echo '</td>';
            echo '<td>';
               //schalter item loeschen
            echo '<form action="delete_item.php" method="post">';
            echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
            echo '<input title="'.get_string('delete_item','feedback').'" type="image" src="'.$CFG->pixpath .'/t/delete.gif" hspace="1" height=11 width=11 border=0 />';
            echo '<input type="hidden" name="deleteitem" value="'.$feedbackitem->id.'" />';
            echo '<input type="hidden" name="id" value="'.$id.'" />';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
            echo '<tr><td>&nbsp;</td></tr>';
         }
         echo '</table>';
         echo '<font color="red">(*)' . get_string('items_are_required', 'feedback') . '</font>';
         echo '</div>';
         print_simple_box_end();
      }else{
         print_simple_box(get_string('no_items_available_yet','feedback'),"center");
      }
	  //echo "<pre>";print_r($SESSION->feedback);echo "</pre>";
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

   print_footer($course);

?>
