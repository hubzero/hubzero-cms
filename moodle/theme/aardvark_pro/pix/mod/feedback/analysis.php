<?PHP  //shows an analysed view of feedback

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

   print_heading($feedback->name);
      
   print_continue('view.php?id='.$id);
   
   //analysierte Items ausgeben
   print_simple_box_start("center", '80%');

   //button "export to excel"
   echo '<div align="center"><form action="analysis_to_excel.php" method="get">';
   echo '<button type="submit">'. get_string('export_to_excel', 'feedback') . '</button>';
   echo '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />';
   echo '<input type="hidden" name="id" value="'.$id.'" />';
   echo '</form></div>';
   
   //get the groupid
   //lstgroupid is the choosen id
   $mygroupid = $SESSION->lstgroupid;

   //get completed feedbacks
   $completedscount = get_completeds_group_count($feedback, $mygroupid);
   
   //show the count
   echo '<b>'.get_string('completed_feedbacks', 'feedback').': '.$completedscount. '</b><br />';
   
   // get the items of the feedback
   $items = get_records_select('feedback_item', 'feedback = '. $feedback->id . ' AND hasvalue = 1', 'position');
   //show the count
   if(is_array($items)){
   	echo '<b>'.get_string('questions', 'feedback').': ' .sizeof($items). ' </b><hr />';
   } else {
	$items=array();
   }

   echo '<div align="center"><table>';
   $itemnr = 0;
   //print the items in an analysed form
   foreach($items as $item) {
      $print_analysed_func = 'print_analysed_'.$item->typ;
      $itemnr = $print_analysed_func($item, $itemnr, $mygroupid);
   }
   echo '</table></div>';
   print_simple_box_end();
   
   print_footer($course);

?>
