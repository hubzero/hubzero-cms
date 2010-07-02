<?php
################################################################################
## portlet.php
##   by Tim Warnock (c) 2005
##
##   include library for portlet functions
################################################################################


################################################################################
## mouseover function
## () or (link) or (hi, low) or (hi, low, link)
################################################################################
function mouseover() {
  //global $_COLOR;
  $color = "#f7fcfd";
  $colorhi = "#fffff4";
  $link = "";
  if ( func_num_args() == 1 ) {
    $getlink = func_get_arg(0);
    $link = "onclick=\"parent.location = '$getlink';\" ";
    $link .= "style=\"cursor: pointer;\"";
  } elseif ( func_num_args() >= 2 ) {
    $colorhi = func_get_arg(0);
    $color = func_get_arg(1);
  }
  if ( func_num_args() == 3 ) {
    $getlink = func_get_arg(2);
    $link = "onclick=\"parent.location = '$getlink';\" ";
    $link .= "style=\"cursor: pointer;\"";
  }
  $return = "onmouseover=\"this.style.backgroundColor='$colorhi'\" ";
  $return .= "onmouseout=\"this.style.backgroundColor='$color'\" $link";
  return $return;
}


################################################################################
# return html for portlet
# make_portlet( title, content, style, max-width, min-width )
################################################################################
function make_portlet( $portlet_title = "",
                       $portlet_content = "",
                       $portlet_style = "mainportlet",
                       $portlet_width = "750px",
                       $column_style = "column_right_main",
                       $is_shadow = false) {

  $column_width_style = (!empty($portlet_width)) ? "style='width:$portlet_width;'" : "";

  $portlet = "

        <!-- make_portlet -->
        <div class='$column_style' $column_width_style>
          <div class='$portlet_style'>
            <div class='$portlet_style" . "_h3'>$portlet_title</div>
            <div class='$portlet_style" . "_p'>
              $portlet_content
            </div>
          </div>
        </div>
        <!-- End make_portlet -->";

  return $portlet;
}


################################################################################
# print all html to browser
# print_to_browser(title, portlets
################################################################################
function print_to_browser( $title = "NEEScentral", $portlets, $logo="") {

  require_once 'lib/common/headerfooter.php';

  print <<<ENDHTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>$title</title>

ENDHTML;

  include "lib/common/meta.php";
  print <<<ENDHTML
</head>
<body>
<!-- Tooptip -->
<script type="text/javascript" src="/common/wz_tooltip.js"></script>
<div id="sitewrap">

ENDHTML;

  print_header($portlets, $logo);
  print_footer();

  print <<<ENDHTML

</div>
</body>
</html>

ENDHTML;

}



################################################################################
# print all html to browser (simplified popup version)
# print_popup(title, page)
################################################################################
function print_popup( $title = "NEEScentral", $portlets, $autoclose = "1", $width=650 ) {
  $close = "";
  if ($autoclose) {
    $close = "onunload='window.opener.location.reload(true);'";
  }
  $widthpx = $width . "px;";

  echo <<<ENDHTML

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>$title</title>
  <link href="/common/main.css" rel="stylesheet" type="text/css" media="screen,projection,print" />
  <script type="text/javascript" src="/common/base.js"></script>
</head>
<body $close style="width:$widthpx min-width:$widthpx margin: 0 auto; border:1px solid #666666;">
<!-- Tooptip -->
<script type="text/javascript" src="/common/wz_tooltip.js"></script>

ENDHTML;

  require_once 'lib/common/headerfooter.php';
  print_basic_header($portlets);
  echo <<<ENDHTML

</body>
</html>

ENDHTML;
}


function print_basic_central($page_title, $page_content) {

  require_once 'lib/common/headerfooter.php';
  print <<<ENDHTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <title>$page_title</title>

ENDHTML;
  include "lib/common/meta.php";
  print <<<ENDHTML
</head>
<body>
<!-- Tooptip -->
<script type="text/javascript" src="/common/wz_tooltip.js"></script>
<div id="sitewrap">

ENDHTML;

  print_basic_header($page_content);

  print_footer();

  print <<<ENDHTML

</div>
</body>
</html>

ENDHTML;

}

?>