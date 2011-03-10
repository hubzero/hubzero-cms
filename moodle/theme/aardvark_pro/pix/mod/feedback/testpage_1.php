<?PHP  //  

   require_once("../../config.php");
   require_once("lib.php");

   optional_variable($id);    // Course Module ID, or

   $formdata = data_submitted('nomatch');
   if (isset($formdata->id)) {
    	$id = ($formdata->id == '')?$id:$formdata->id;
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
   
   //action handler lines,
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


/// Print the main part of the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
      print_heading($feedback->name);

      //action handler error reported, affected by constant feedback_ACTIONS_DEBUG in picture\lib.php
	  feedback_print_errors();
    
	  echo "<br /><br /><br /> \n";
      echo '<div align="center"><table width="90%"><tr><td valign="top">';

      print_simple_box_start("center");
		
	  echo "<br /><table><tr align='left'><td width='200px'> \n";
	  echo "<strong>Icon to click on:</strong>";
  	  echo "</td><td> \n";
	  echo "<strong>PHP to create the button: </strong>\n";
	  echo "</td></tr><tr align='left'><td> \n";	  
	  
	  echo feedback_create_action_form('print_picture', array('You clicked on the first example'));
  	  echo "</td><td> \n";
	  echo "echo feedback_create_action_form('print_picture', array('You clicked on the first example'));\n";
	  echo "</td></tr><tr align='left'><td> \n";	  
	  echo "<tr align='left'><td> \n";
	  
	  echo feedback_create_action_form('print_picture', array('You clicked on the second example'));
  	  echo "</td><td> \n";
	  echo "echo feedback_create_action_form('print_picture', array('You clicked on the second example'));\n";
	  echo "</td></tr><tr align='left'><td> \n";	  
	  echo "<tr align='left'><td> \n";
	  
	  echo feedback_create_action_form('prnt', array('This will produce an error'));
  	  echo "</td><td> \n";
	  echo "echo feedback_create_action_form('prnt', array('This will produce an error'));\n";
	  echo "</td></tr><tr align='left'><td> \n";	  

	  echo feedback_create_action_form('print_picture', array('You were redirect to testpage_2'),'move.gif','print_picture','testpage_2.php?id='.$SESSION->feedback->coursemoduleid);
  	  echo "</td><td> \n";
	  echo " echo feedback_create_action_form('print_picture', array('You will be redirected to testpage_2'),'move.gif','print_picture','testpage_2.php?id='.\$SESSION->feedback->coursemoduleid);\n";

	  echo "</td></tr><tr align='left'><td> \n";	  
  	  echo "</td><td> \n";
	  echo "</td></tr></table> \n";
	  
	  //-------------------------------------------------------------------------------
	  //the same stuff on a large form 
	  //-------------------------------------------------------------------------------
	  echo '<form action="testpage_1.php?id='.$SESSION->feedback->coursemoduleid.'" method="post">';
  	  echo "<br /><table><tr align='left'><td width='200px'> \n";
	  echo "<strong>These guys are sitting on a large form <br />Icon to click on:</strong>";
  	  echo "</td><td> \n";
	  echo "<strong>PHP to create the button: </strong>\n";
	  echo "</td></tr><tr align='left'><td> \n";	  
	  
	  echo feedback_create_action('print_picture', array('You clicked on the third example'));
  	  echo "</td><td> \n";
	  echo "echo feedback_create_action('print_picture', array('You clicked on the third example'));\n";
	  echo "</td></tr><tr align='left'><td> \n";	  
	  echo "<tr align='left'><td> \n";
	  
	  echo feedback_create_action('print_picture', array('You clicked on the fourth example'));
  	  echo "</td><td> \n";
	  echo "echo feedback_create_action('print_picture', array('You clicked on the fourth example'));\n";
	  echo "</td></tr><tr align='left'><td> \n";	  
	  echo "<tr align='left'><td> \n";
	  
	  echo feedback_create_action('prnt', array('This will produce an error'));
  	  echo "</td><td> \n";
	  echo "echo feedback_create_action('prnt', array('This will produce an error'));\n";
	  echo "</td></tr><tr align='left'><td> \n";	  

	  echo feedback_create_action('print_picture', array('You were redirect to testpage_2'),'move.gif','print_picture','testpage_2.php?id='.$SESSION->feedback->coursemoduleid);
  	  echo "</td><td> \n";
	  echo " echo feedback_create_action('print_picture', array('You will be redirected to testpage_2'),'move.gif','print_picture','testpage_2.php?id='.\$SESSION->feedback->coursemoduleid);\n";
	  echo "</td></tr><tr align='left'><td> \n";	  

	  echo feedback_create_action_submit('submitbutton_picture', array('You pressed the submit-button'), 'Just Submit');
  	  echo "</td><td> \n";
	  echo "echo feedback_create_action_submit('submitbutton_picture', array('You pressed the submit-button'), 'Just Submit');\n";
	  echo "</td></tr><tr align='left'><td> \n";	  
	  
  	  echo "</td><td> \n";
	  echo "</td></tr></table> \n";
	  echo '</form>';

	  //-------------------------------------------------------------------------------
	  //demonstrating hidden variables
	  //-------------------------------------------------------------------------------
	  
	  echo '<form action="testpage_1.php?id='.$SESSION->feedback->coursemoduleid.'" method="post">';
  	  echo "<br /><table><tr align='left'><td width='200px'> \n";
	  echo "<strong>This is a lonely guy on a form of its own <br />Icon to click on:</strong>";
  	  echo "</td><td> \n";
	  echo "<strong>PHP to create the button, demonstrates adding hidden variable: </strong>\n";
	  echo "</td></tr><tr align='left'><td> \n";	  

	  
	  echo feedback_create_action('print_picture', array('You defined also a hidden var \'testvar\''),'move.gif','','',array('testvar'=>'working OK!'));
  	  echo "</td><td> \n";
	  echo "echo feedback_create_action('print_picture', array('You defined also a hidden var 'testvar'),'move.gif','','',array('testvar'=>'working OK!'));\n";
	  echo "</td></tr><tr align='left'><td> \n";	  

	  
  	  echo "</td><td> \n";
	  echo "</td></tr></table> \n";
	  echo '</form>';
	  
	  echo "<br /><br />\n";	  
	  
  	  echo "<table><tr align='left'><td> \n";
	  echo "<strong>The result of your action:</strong> (from call to function feedback_handler_print_picture(\$text) in item/picture/lib.php) \n";
	  echo "</td></tr><tr align='left'><td> \n";	  
	  echo isset($SESSION->feedback->testmessage)?$SESSION->feedback->testmessage.'<br />':'<br /><br />';
	  unset($SESSION->feedback->testmessage);
	  echo "</td></tr><tr align='left'><td> \n";	  
	  if (isset($_POST['testvar'])) {
	   	echo "Value of 'testvar' :" . $_POST['testvar'].'<br />';
		unset($_POST['testvar']);
	  } else {
	  echo "Value of 'testvar': <strong>value not set</strong><br />\n";
	  }
	  echo "</td></tr></table> \n";

	  print_simple_box_end();

      echo '</td></tr></table></div>';
/// Finish the page
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////

   print_footer($course);

?>
