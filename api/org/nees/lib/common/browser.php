<?php
/** ****************************************************************************
 * @title
 *   NEEScentral Browser functions
 *
 * @author
 *   Tim Warnock
 *
 * @abstract
 *   Browse data, metadata, and project information
 *
 * @description
 *   Functions for creating file browsers and other helper functions
 *
 ******************************************************************************/

## temp unavailable
## header("Location: /help/outage.php");
## exit;


require_once 'lib/common/portlet.php';
//require_once 'lib/interface/Data.php';

################################################################################
## generate hidden form elements
################################################################################
function getexhidden( $exlinks ) {
  $querystring = array();
  $exlinks = ltrim( $exlinks, "?" );
  parse_str( $exlinks, $querystring );
  $form = "";
  foreach ($querystring as $key => $value) {
    // Hack's way, to avoid IE error form.action
    if($key == "action") continue;

    $form .= "<input type='hidden' name=\"$key\" value=\"$value\" />";
  }
  return $form;
}

################################################################################
## date cleaning function
################################################################################
function cleanDate( $uglyDate, $shortDate = 0 ) {
  if(is_null($uglyDate) || ($uglyDate == "") || preg_match ('/0{1,4}-00-00.*/', $uglyDate)) {
    return "TBD";
  } elseif(preg_match ('/[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}/', $uglyDate)) {
    if($shortDate) {
      return date("m/d/y", strtotime($uglyDate));
    } else {
      return date("M j, Y \a\\t h:i a", strtotime($uglyDate));
    }
  } elseif(preg_match ('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $uglyDate)) {
    if($shortDate) {
      return date("m/d/y", strtotime($uglyDate));
    } else {
      return date("M j, Y", strtotime($uglyDate));
    }
  }
}

################################################################################
## calendar popup printing function
################################################################################
function make_date_input( $fieldName, $fieldValue = "", $format = "datetime" ) {

  if($format === "datetime") {
    $inputSize = 16;
    $inputFormat = "%m-%d-%Y %H:%M";
    $showTime = "true";
    $timeFormatStr = "timeFormat     :    \"12\",";
  }
  elseif($format === "date") {
    $inputSize = 10;
    $inputFormat = "%m-%d-%Y";
    $showTime = "false";
    $timeFormatStr = "";
  }
  else {
    return 0;
  }


  $retval = <<<ENDHTML
    <input size="$inputSize" maxlength="$inputSize" class="textentry" type="text" id="$fieldName" name="$fieldName" value="$fieldValue"/>
    <script type="text/javascript">
<!--
      Calendar.setup({
          inputField     :    "$fieldName",   // id of the input field
          ifFormat       :    "$inputFormat", // format of the input field
          showsTime      :    $showTime,
          $timeFormatStr
          weekNumbers    :    false,
          align          :    "TC",
          singleClick    :    false,
          showOthers     :    true
      });
//-->
    </script>
ENDHTML;

  return $retval;
}

################################################################################
## size cleaning function
################################################################################
function cleanSize( $size, $precision = null) {
  $default_precision = 0;

  if($size >= 1099511627776) {
    $size = $size / 1099511627776;
    $unit = " TB";
    $default_precision = 2;
  }
  elseif($size >= 1073741824) {
    $size = $size / 1073741824;
    $unit = " GB";
    $default_precision = 1;
  }
  elseif($size >= 1048576) {
    $size = $size / 1048576;
    $unit = " MB";
  }
  elseif($size >= 1024) {
    $size = $size / 1024;
    $unit = " KB";
  }
  else {
    $unit = " b";
  }

  if(is_null($precision)) $precision = $default_precision;
  $size = round($size, $precision);
  return "$size $unit";
}





################################################################################
## return a truncated title and add ellipses (optionally add toggle expand)
################################################################################
function truncate( $str_orig = "na", $str_len = 45, $expand_link = "" ) {
  $return = $str_orig;
  if ( strlen($return) > $str_len) {
    if ( strlen($expand_link) > 1 ) {
      ##
      ## more/less links
      $expand_link = mt_rand();
      $str_orig = str_replace("'", "&#039;", $str_orig);

      ## expand string
      $expanded_string = nl2br( $str_orig );
      $expanded_string = preg_replace("/[\n\r]+/", "", $expanded_string);
      $expanded_string .= " [<a class=\"darkgraylt\" href=\"javascript:collapse_$expand_link();\">collapse</a>]";

      ## collapse string
      $collapsed_string = strip_tags( $str_orig );
      $collapsed_string = preg_replace("/[\n\r]+/", " ", $collapsed_string);
      $collapsed_string = substr($collapsed_string, 0, $str_len) .
                         " ... [<a class=\"darkgraylt\" href=\"javascript:expand_$expand_link();\">more</a>]";
      $return = <<< ENDHTML

      <script type="text/javascript">
<!--
        function expand_$expand_link() {
          newtitle = '$expanded_string';
          if (document.all)
            truncated_$expand_link.innerHTML=newtitle;
          else if (document.getElementById){
            rng = document.createRange();
            expandstr = document.getElementById("truncated_$expand_link");
            rng.setStartBefore(expandstr);
            htmlFrag = rng.createContextualFragment(newtitle);
            while (expandstr.hasChildNodes())
              expandstr.removeChild(expandstr.lastChild);
            expandstr.appendChild(htmlFrag);
          }
        }

        function collapse_$expand_link() {
          newtitle = '$collapsed_string';
          if (document.all)
            truncated_$expand_link.innerHTML=newtitle;
          else if (document.getElementById){
            rng = document.createRange();
            expandstr = document.getElementById("truncated_$expand_link");
            rng.setStartBefore(expandstr);
            htmlFrag = rng.createContextualFragment(newtitle);
            while (expandstr.hasChildNodes())
              expandstr.removeChild(expandstr.lastChild);
            expandstr.appendChild(htmlFrag);
          }
        }
//-->
        </script>

      <div id="truncated_$expand_link">
        $collapsed_string
      </div>
ENDHTML;
    } else {
    ##
    ## simple truncate
      $return = substr($str_orig, 0, $str_len);
      $return .= "...";
    }
  }
  return $return;
}


function getGoogleAnalyticsText() {
  $theText = <<<HTML


<!-- Google Analytics script -->
<script src="/analytics/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
<!--
_uacct = "UA-847511-1";
urchinTracker();
//-->
</script>


HTML;

  return $theText;
}


function getResourceBox() {
  $ret = <<<HTML


  <div style="background-color:#ffffff; padding-top:10px;">
    <div style="height:55px; background: #FFFFFF url(/images/help_support.gif) no-repeat top left; padding:10px 0 0 60px;">
      <h2>Questions?</h2>
    </div>

    <ul style="padding:0 0 20px 20px;">
      <li>Review detailed instructions in the <a href="javascript:showNCGuide()" class="bluelt">NEEScentral User's Guide&nbsp;&raquo;</a>.</li>
      <li>Submit a support request to <a class="bluelt" href="mailto:it-support@nees.org">NEESit&nbsp;&raquo;</a></li>
      <li>Visit the <a href="http://www.nees.org/training/kb" target="_blank" class="bluelt">NEESit Knowledge Base&nbsp;&raquo;</a> for articles on specific topics </li>
      <li>Learn about the latest enhancements in the <a href="http://www.nees.org/news/detail/neescentral187/" target="_blank" class="bluelt">NEEScentral Release Notes&nbsp;&raquo;</a>.</li>
    </ul>
  </div>

HTML;
  return $ret;
}


function getLeftPortlet() {

  require "lib/util/TreeBrowser.php";
  $tree = new TreeBrowser();

  $left_portlet_content = getResourceBox();
  $left_portlet_content .= "<h5 style='padding-left:10px;'>NEEScentral Demo Project</h5>";
  $left_portlet_content .= $tree->makeTree(4, false);

  $left_portlet = make_portlet("<div class='mainportlet_title'>Additional Resources</div>", $left_portlet_content, "mainportlet", null, "column_left_main");

  return $left_portlet;
}


function getLeftHelpPortlet() {

  require_once "lib/util/TreeUserGuide.php";

  $left_portlet_content = getResourceBox();
  $left_portlet_content .= "<h5 style='padding-left:10px;'>NEEScentral User Guide</h5>";
  $left_portlet_content .= getHelpDocTree();

  $left_portlet = make_portlet("<div class='mainportlet_title'>Additional Resources</div>", $left_portlet_content, "mainportlet", null, "column_left_main");

  return $left_portlet;
}


?>