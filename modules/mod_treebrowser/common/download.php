<?php

// Why we need to check the login
//require "nees.php";

## Draw the page
require "downloadapplet.php";
if(strlen($fileNames) == 0) {
$notice = <<<ENDHTML

<div class="miniportlet" style="border-bottom:none; border-left:none; border-right:none;">
  <div class="miniportlet_h3" style="border-left:none; border-right:none;">
    <div class="miniportlet_title">
      NEEScentral Bulk Download
    </div>
  </div>
  <div style="margin:10px; width:500px;">
    <div class="notice">
			There are no files to download
    </div>
  </div>
</div>


ENDHTML;

print_popup("NEEScentral Bulk Download", $notice, 0, 520);
 // echo($notice);
  return;
}



$return = <<<ENDHTML

<div class="miniportlet" style="border-bottom:none; border-left:none; border-right:none;">
  <div class="miniportlet_h3" style="border-left:none; border-right:none;">
    <div class="miniportlet_title">
      NEEScentral Bulk Download
    </div>
  </div>
  <div style="margin:10px; width:500px;">
    $downloadapplet

    <div class="info">
				<ol>
					<li>Click the <strong>Browse</strong> button.</li>
					<li>Select the target directory to download the files/folders you have selected.</li>
					<li>Click the <strong>Start</strong> button to initiate the transfer.</li>
					<li>Closing the window while a transfer is in progress will result in a failed download.</li>
				</ol>
    </div>
  </div>
</div>


ENDHTML;

print_popup("NEEScentral Bulk Download", $return, 0, 520);

?>
