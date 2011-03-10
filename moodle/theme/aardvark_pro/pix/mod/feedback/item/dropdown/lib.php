<?PHP  // $Id: lib.php,v 1.1.2.1 2006/01/03 16:01:22 andreas Exp $
function show_edit_dropdown($item, $usehtmleditor = false) {

	$item->presentation=empty($item->presentation)?'':$item->presentation;

?>
   <table>
      <tr>
         <th colspan="2"><?php print_string('dropdownlist', 'feedback');?>
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
            <?php print_string('dropdown_values', 'feedback');?>
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

//liefert ein eindimensionales Array mit drei Werten(typ, name, XXX)
//XXX ist ein eindimensionales Array (anzahl der Antworten bei Typ DropDown) Jedes Element ist eine Struktur (answertext, answercount)
function get_analysed_dropdown($item, $groupid = false) {
   $analysedItem = array();
   $analysedItem[] = $item->typ;
   $analysedItem[] = $item->name;
   //die moeglichen Antworten extrahieren
   $answers = null;
   $answers = explode ("|", stripslashes_safe($item->presentation));
   if(!is_array($answers)) return null;

   //die Werte holen
   //$values = get_records('feedback_value', 'item', $item->id);
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
         if ($value->value == $i) {
            $ans->answercount++;
         }
      }
      $ans->quotient = $ans->answercount / sizeof($values);
      $analysedAnswer[] = $ans;
   }
   $analysedItem[] = $analysedAnswer;
   return $analysedItem;
}

function get_feedback_printval_dropdown($item, $value) {
   $printval = '';
   $presentation = explode ("|", stripslashes_safe($item->presentation));
   $index = 1;
   foreach($presentation as $pres){
      if($value->value == $index){
         $printval = $pres;
         break;
      }
      $index++;
   }
   return $printval;
}

function print_analysed_dropdown($item, $itemnr = 0, $groupid = false) {
   $analysedItem = get_analysed_dropdown($item, $groupid);
   if($analysedItem) {
      //echo '<table>';
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
      //echo '</table>';
   }
   return $itemnr;
}

function excelprint_item_dropdown(&$worksheet, $rowOffset, $item, $groupid) {
   $analysed_item = get_analysed_dropdown($item, $groupid);


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

function print_feedback_dropdown($item, $value = false, $readonly = false){
   $presentation = explode ("|", stripslashes_safe($item->presentation));
   $requiredmark =  ($item->required == 1)?'<font color="red">*</font>':'';
?>
   <td valign="top" align="left"><?php echo text_to_html(stripslashes_safe($item->name) . $requiredmark, true, false, false);?></td>
   <td valign="top" align="left">
<?php
   $index = 1;
   $selected = '';
   if($readonly){
      foreach($presentation as $dropdown){
         if($value == $index){
            print_simple_box_start('left');
            echo text_to_html($dropdown, true, false, false);
            print_simple_box_end();
            break;
         }
         $index++;
      }
   } else {
?>
      <select name="<?php echo $item->typ .'_' . $item->id;?>" size="1">
         <option value="0">&nbsp;</option>
<?php
      foreach($presentation as $dropdown){
         if($value == $index){
            $selected = 'selected="selected"';
         }else{
            $selected = '';
         }
?>
         <option value="<?php echo $index;?>" <?php echo $selected;?>><?php echo text_to_html($dropdown, true, false, false);?></option>
<?php
         $index++;
      }
?>
      </select>
<?php
   }
?>
   </td>
<?php
}

function check_feedback_value_dropdown($value) {
   if($value == 0)return false;
   return true;
}

function create_feedback_value_dropdown($data) {
   return $data;
}

function get_presentation_dropdown($data) {
   $present = str_replace("\n", '|', trim($data->itemvalues));
   return $present;
}

function get_feedback_hasvalue_dropdown() {
   return 1;
}

?>