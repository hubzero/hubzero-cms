<?PHP  // 

   require_once("../../config.php");
   require_once("lib.php");
   require_once('easy_excel.php');
 
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

   $filename = "feedback.xls";
   
   //Dem Browser mitteilen, dass jetzt eine Exceldatei zum Downloaden kommt
   
   header("Content-type: application/vnd.ms-excel");
   header("Content-Disposition: attachment; filename=$filename" );
   header("Expires: 0");
   header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
   header("Pragma: public");
   
   //get the groupid for this module
   //get the groupid
   $mygroupid = $SESSION->lstgroupid;

   // Creating a workbook
   $workbook = new EasyWorkbook("-");
   // Creating the worksheets
   $worksheet1 =& $workbook->add_worksheet(substr($feedback->name, 0, 31));
   $worksheet2 =& $workbook->add_worksheet('detailed');

   $worksheet1->set_portrait();
   $worksheet1->set_paper(9);
   $worksheet1->center_horizontally();
   $worksheet1->hide_gridlines();
   $worksheet1->set_header("&\"Arial," . get_string('bold', 'feedback') . "\"&14".$feedback->name);
   $worksheet1->set_footer(get_string('page', 'feedback')." &P " . get_string('of', 'feedback') . " &N");
   $worksheet1->set_column(0, 0, 30);
   $worksheet1->set_column(1, 20, 15);
   $worksheet1->set_margins_LR(0.10);

   $worksheet2->set_landscape();
   $worksheet2->set_paper(9);
   $worksheet2->center_horizontally();

   //Tabellenkopf schreiben
   $rowOffset1 = 0;
   $worksheet1->setFormat("<f>",12,false);
   $worksheet1->write_string($rowOffset1, 0, UserDate(time()));

   ////////////////////////////////////////////////////////////////////////
   //Uebersicht darstellen
   ////////////////////////////////////////////////////////////////////////
   //ausgefuellte feedbacks holen
   $completedscount = get_completeds_group_count($feedback, $mygroupid);
   if($completedscount > 0){
      //Anzahl der Ausgefuellten feedbacks eintragen
      $rowOffset1++;
      $worksheet1->write_string($rowOffset1, 0, get_string('modulenameplural', 'feedback').': '.strval($completedscount));
   }

   //fragen holen
   $items = get_records_select('feedback_item', 'feedback = '. $feedback->id . ' AND hasvalue = 1', 'position');
   if(is_array($items)){
      $rowOffset1++;
      $worksheet1->write_string($rowOffset1, 0, get_string('questions', 'feedback').': '. strval(sizeof($items)));
   }
   
   $rowOffset1 += 2;
   $worksheet1->write_string($rowOffset1, 0, get_string('question', 'feedback'));
   $worksheet1->write_string($rowOffset1, 1, get_string('responses', 'feedback'));
   $rowOffset1++ ;

   if (empty($items)) {
       $items=array();
   }
   foreach($items as $item) {
      $excelprint_item_func = 'excelprint_item_'.$item->typ;
      $rowOffset1 = $excelprint_item_func($worksheet1, $rowOffset1, $item, $mygroupid);
   }

   ////////////////////////////////////////////////////////////////////////
   //Detail-Tabelle darstellen
   ////////////////////////////////////////////////////////////////////////
   //ausgefuellte fragen holen
   
   $completeds = get_completeds_group($feedback, $mygroupid);
   //wichtig fuer jedes completed muss jedes Item ausgegeben werden, auch wenn es nicht ausgefuellt wurde
   //Deswegen muss fuer jedes Completed eine Schleife ueber die Items des eigentlichen Feedbacks durchgefuehrt werden
   //Das erfolgt in der Function excelprint_detailed_items
   
   $rowOffset2 = 0;
   //erstmal den Tabellenkopf ausgeben
   $rowOffset2 = excelprint_detailed_head($worksheet2, $items, $rowOffset2);
   
   
   if(is_array($completeds)){
      foreach($completeds as $completed) {
         $rowOffset2 = excelprint_detailed_items($worksheet2, $completed, $items, $rowOffset2);
      }
   }
   
   
   $workbook->close();
   exit;
////////////////////////////////////////////////////////////////////////////////   
////////////////////////////////////////////////////////////////////////////////   
//functions
////////////////////////////////////////////////////////////////////////////////   

   
   function excelprint_detailed_head(&$worksheet, $items, $rowOffset) {
      if(!$items) return;
      $colOffset = 0;
      foreach($items as $item) {
         $worksheet->setFormat('<l><f><ru2>');
$worksheet->write_string($rowOffset, $colOffset, stripslashes_safe($item->name));
         $colOffset++;
      }
      return $rowOffset + 1;
   }
   
   function excelprint_detailed_items(&$worksheet, $completed, $items, $rowOffset) {
      if(!$items) return;
      $colOffset = 0;
      foreach($items as $item) {
         $value = get_record('feedback_value', 'item', $item->id, 'completed', $completed->id);
         $get_feedback_printval_func = 'get_feedback_printval_'.$item->typ;

         $printval = $get_feedback_printval_func($item, $value);

         $worksheet->setFormat('<l><vo>');
         if(is_numeric($printval)) {
            $worksheet->write_number($rowOffset, $colOffset, trim($printval));
         } else {
            $worksheet->write_string($rowOffset, $colOffset, trim($printval));
         }
         $printval = '';
         $colOffset++;
      }
      return $rowOffset + 1;
   }
?>
