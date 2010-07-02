<?php

require "lib/nees.php";

$return = <<<ENDHTML

<div class="miniportlet" style="border-bottom:none; border-left:none; border-right:none;">
  <div class="miniportlet_h3" style="border-left:none; border-right:none; border-bottom:none;">
    <div class="miniportlet_title">Upload: Successful</div>
  </div>

  <div class="miniportlet_p">
    <div class="contentpadding">
      <br/><br/>
      <ul>
ENDHTML;
   if (isset($_REQUEST['fnumber']))
   {
     $return .= "<li><strong>Files Uploaded: </strong>{$_REQUEST['fnumber']}</li>";
   }
   if (isset($_REQUEST['rate']))
   {
     $return .= "<li><strong>Approximate Transfer Rate: </strong>{$_REQUEST['rate']}</li>";
   }
   if (isset($_REQUEST['ttime']))
   {
     $return .= "<li><strong>Total Transfer Time: </strong>{$_REQUEST['ttime']}</li>";
   }

   if ($pos = strpos($_SERVER['QUERY_STRING'], "backurl"))
   {
     $backURL = substr($_SERVER['QUERY_STRING'], $pos + strlen("backurl="));
     $backURL = "location.href='$backURL'";
   }
   else
   {
     $backURL = "history.go(-1)";
   }

$return .= <<<ENDHTML
     </ul>

     <input type="button" class="btn" value="Close Window" onclick="window.close()" />
     <input type="button" class="btn" value="Upload more files" onclick="$backURL" />

    </div>
  </div>
</div>
ENDHTML;

$reload_on_close = false;
print_popup("NEEScentral Upload results", $return, $reload_on_close);
?>
