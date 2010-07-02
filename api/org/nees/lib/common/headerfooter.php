<?php
################################################################################
## headerfooter.php
##   by Daniel Rehn, Tim Warnock and Daniel Frysinger (c) 2005
##
##   draw header and footer for NEEScentral portal
################################################################################


################################################################################
## print_footer
################################################################################
function print_footer() {

  require_once 'lib/controller/actions/footers/MainFooter.php';
  $mainfooter = new MainFooter();

  print $mainfooter->getFooter();
}


################################################################################
## print_header
################################################################################
function print_header($theportlets, $logo="") {

  require_once 'lib/controller/actions/headers/MainHeader.php';

  $mainheader = new MainHeader(null);

  print <<<ENDHTML


  {$mainheader->getMainHeader($logo)}
  {$mainheader->getMainNavigationBar()}
  {$mainheader->getBreadcrumb()}

  <div id="maincontentwrap">
    <div id="centralbody">
      $theportlets
      <div style="clear: both;">&nbsp;</div>
    </div>
  </div>


ENDHTML;
}



################################################################################
## print_header
################################################################################
function print_basic_header($theportlets) {

  require_once 'lib/controller/actions/headers/MainHeader.php';

  $mainheader = new MainHeader(null);

  print <<<ENDHTML

  {$mainheader->getMainBasicHeader()}

  <div id="maincontentwrap">
    <div id="centralbody">
      $theportlets
      <div style="clear: both;">&nbsp;</div>
    </div>
  </div>


ENDHTML;
}


?>