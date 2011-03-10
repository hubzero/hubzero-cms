<?PHP  // $Id: lib.php,v 1.1.2.1 2006/01/03 16:01:22 andreas Exp $
function show_edit_check($item, $usehtmleditor = false) {

	$item->presentation=empty($item->presentation)?'':$item->presentation;
	
?>
   <table>
      <tr><th colspan="2">
         <?php print_string('checkbox', 'feedback');?>
            &nbsp;(<input type="checkbox" name="required" value="1" <?php 
			$item->required=isset($item->required)?$item->required:0;
			echo ($item->required == 1?'checked="checked"':'');
			?> />&nbsp;<?php print_string('required', 'feedback');?>)
         </th>
      </tr>
      <tr>
         <td><?php print_string('item_name', 'feedback');?></td>
         <td><input type="text" id="itemname" name="itemname" size="40" maxlength="255" value="<?php echo isset($item->name)?stripslashes_safe($item->name):'';?>" /></td>
      </tr>
      <tr>
         <td>
            <?php print_string('check_values', 'feedback');?>
            <?php print_string('use_one_line_for_each_value', 'feedback');?>
         </td>
         <td>
<?php
            $itemvalues = str_replace('|', "\n", stripslashes_safe($item->presentation));
?>
            <textarea name="itemvalues" cols="30" rows="5"><?php echo $itemvalues;?></textarea>
         </td>
      </tr>
   </table>
<?php
}

//liefert ein Array mit drei Werten(typ, name, XXX)
//XXX ist ein Array (anzahl der Antworten bei Typ checkbox) Jedes Element ist eine Struktur (answertext, answercount)
function get_analysed_check($item, $groupid = false) {
   $analysedItem = array();
   $analysedItem[] = $item->typ;
   $analysedItem[] = $item->name;
   //die moeglichen Antworten extrahieren
   $answers = null;
   $answers = explode ("|", stripslashes_safe($item->presentation));
   if(!is_array($answers)) return null;

   //die Werte holen
   $values = get_feedback_group_values($item, $groupid);
   if(!$values) return null;
   //schleife ueber den Werten und ueber die Antwortmoeglichkeiten
   
   $analysedAnswer = array();

   for($i = 1; $i <= sizeof($answers); $i++) {
      $ans = null;
      $ans->answertext = $answers[$i-1];
      $ans->answercount = 0;
      foreach($values as $value) {
         //ist die Antwort gleich dem index der Antworten + 1?
         $vallist = explode('|', $value->value);
         foreach($vallist as $val) {
            if ($val == $i) {
               $ans->answercount++;
            }
         }
      }
      $ans->quotient = $ans->answercount / sizeof($values);
      $analysedAnswer[] = $ans;
   }
   $analysedItem[] = $analysedAnswer;
   return $analysedItem;
}

function get_feedback_printval_check($item, $value) {
   $printval = '';
   $presentation = array_values(explode ("|", stripslashes_safe($item->presentation)));
   $vallist = array_values(explode ('|', $value->value));
   for($i = 0; $i < sizeof($vallist); $i++) {
      for($k = 0; $k < sizeof($presentation); $k++) {
         if($vallist[$i] == ($k + 1)) {//Die Werte beginnen bei 1, das Array aber mit 0
            $printval .= trim($presentation[$k]) . chr(10);
            break;
         }
      }
   }
   return $printval;
}

function print_analysed_check($item, $itemnr = 0, $groupid = false) {
   $analysedItem = get_analysed_check($item, $groupid);
   if($analysedItem) {
      $itemnr++;
      echo '<tr><th colspan="2">'. $itemnr . '.)&nbsp;' . $analysedItem[1] .'</th></tr>';
      $analysedVals = $analysedItem[2];
      $pixnr = 0;
      foreach($analysedVals as $val) {
         if( function_exists("bcmod")) {
            $pix = 'pics/' . bcmod($pixnr, 10) . '.gif';
         }else {
            $pix = 'pics/0.gif';
         }
         $pixnr++;
         $pixwidth = intval($val->quotient * feedback_MAX_PIX_LENGTH);
         $quotient = number_format(($val->quotient * 100), 2, ',', '.');
         echo '<tr><td align="right"><b>' . trim($val->answertext) . ':</b></td><td align="left"><img style=" vertical-align: baseline;" src="'.$pix.'" height="5" width="'.$pixwidth.'" />&nbsp;' . $val->answercount . (($val->quotient > 0)?'&nbsp;('. $quotient . '&nbsp;%)':'').'</td></tr>';
      }
   }
   return $itemnr;
}

function excelprint_item_check(&$worksheet, $rowOffset, $item, $groupid) {
   $analysed_item = get_analysed_check($item, $groupid);


   $data = $analysed_item[2];

   $worksheet->setFormat("<l><f><ro2><vo><c:green>");
   //frage schreiben
   $worksheet->write_string($rowOffset, 0, $analysed_item[1]);
   if(is_array($data)) {
      for($i = 0; $i < sizeof($data); $i++) {
         $aData = $data[$i];
         
         $worksheet->setFormat("<l><f><ro2><vo><c:blue>");
         $worksheet->write_string($rowOffset, $i + 1, trim($aData->answertext));
         
         $worksheet->setFormat("<l><vo>");
         $worksheet->write_number($rowOffset + 1, $i + 1, $aData->answercount);
         $worksheet->setFormat("<l><f><vo><pr>");
         $worksheet->write_number($rowOffset + 2, $i + 1, $aData->quotient);
      }
   }
   $rowOffset +=3 ;
   return $rowOffset;
}

function print_feedback_check($item, $value = false, $readonly = false){
   $presentation = explode ("|", stripslashes_safe($item->presentation));
   if (is_array($value)) {
      $values = $value;
   }else {
      $values = explode("|", $value);
   }
   $requiredmark =  ($item->required == 1)?'<font color="red">*</font>':'';
?>
   <td valign="top" align="left"><?php echo text_to_html(stripslashes_safe($item->name) . $requiredmark, true, false, false);?></td>
   <td valign="top" align="left">
<?php
   $index = 1;
   $checked = '';
   if($readonly){
      print_simple_box_start('left');
      foreach($presentation as $check){
         foreach($values as $val) {
            if($val == $index){
               echo text_to_html($check . '<br />', true, false, false);
               break;
            }
         }
         $index++;
      }
      print_simple_box_end();
   } else {
      foreach($presentation as $check){
         foreach($values as $val) {
            if($val == $index){
               $checked = 'checked="checked"';
               break;
            }else{
               $checked = '';
            }
         }
?>
         <table><tr>
         <td valign="top"><input type="checkbox"
               name="<?php echo $item->typ. '_' . $item->id?>[]"
               id="<?php echo $item->typ. '_' . $item->id?>"
               value="<?php echo $index;?>" <?php echo $checked;?> />
         </td><td><?php echo text_to_html($check, true, false, false);?>&nbsp;
         </td></tr></table>
<?php
         $index++;
      }
   }
?>
      <input type="hidden" name="<?php echo $item->typ. '_' . $item->id?>[]" value="" />
   </td>
<?php
}

function check_feedback_value_check($value) {
   if($value[0] == "")return false;
   return true;
}

function create_feedback_value_check($data) {
   $vallist = $data;
   return trim(check_item_arrayToString($vallist));
}

function check_item_arrayToString($arr) {
   if(!is_array($arr)) {
      return '';
   }
   $retval = '';
   $arrvals = array_values($arr);
   $retval = $arrvals[0];
   for($i = 1; $i < sizeof($arrvals) - 1; $i++) {
      $retval .= '|'.$arrvals[$i];
   }
   return $retval;
}

function get_presentation_check($data) {
   $present = str_replace("\n", '|', trim($data->itemvalues));
   return $present;
}

function get_feedback_hasvalue_check() {
   return 1;
}
?>