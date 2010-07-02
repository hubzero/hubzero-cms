<?php
require_once "lib/data/tsunami/util/TsunamiBase.php";
require_once "lib/common/Filter.php";
include "lib/data/tsunami/util/Catalog.php";
include "lib/common/meta.php";

$pId = $_REQUEST['pId'];
$sId = $_REQUEST['sId'];
$f = new Filter();
$f->setFilter("(dirty=1)AND(TsunamiProjectId=".$pId.")");
$dlDAO = TsunamiDAOFactory::newDAO("TsunamiDocLib","MySQL");
$dList = $dlDAO->listTsunamiDocLibs($f,true);
$sDAO = TsunamiDAOFactory::newDAO("TsunamiSite","MySQL");
$s=$sDAO->getTsunamiSite($sId);
$location = $s->getName();

if (empty($dList))
{
	print "<p class=\"\">There are no items marked for Title and Description assignment.";
	print "<p class=\"\">If this a mistake, please close the window and try again";
	exit;

}

$mData=array();
foreach ($subCategory as $subcat => $sc) {
	if (isset($_REQUEST[$sc[1]])){ // found a Metadata id
		$topic = $Category[$_REQUEST[$sc[1]]][1]; //Metadata topic
		if (!isset($mData[$topic])) {//first time
			$mData[$topic]=$Category[$_REQUEST[$sc[1]]][0];
		}
	}
}

$siteList = "";
foreach($mData as $topic => $title)
	$siteList .= $title."|";

$siteList = substr($siteList,0,strlen($siteList)-1);



?>

<script type="text/javascript">
<!--

	function isblank(s)
	{
		for(var i = 0; i<s.length; i++){
			var c = s.charAt(i);
			if ((c!=' ') && (c!='\n') && (c!='\t') && (c!='')) return(false);
		}
		return(true);
	}

	function CheckSubmit(f)
	{
		var msg;
		var error="0";

		for(var i=0; i<f.length;i++){
			var e=f.elements[i];
			var n = e.name;
			if (n.match("Title")!=null) {
				if (e.value == "" || e.value==null || isblank(e.value)){
					error="1";
					break;
				}
			}
		}

			if (error == "1") {
				msg="A non-blank Title is required for ALL files\n";
				msg+="that are to be entered into the repository.\n\n";
				msg+="Please make sure EACH entry has a Title.\n";
				alert(msg);
				return(false);
			}
			return(true);
	}

-->
</script>




<?php
// Finally, we output the page heading and make a form listing a Title and Description for every DocLib and
//   update each DocLib after the form has been posted.
	print "<html><body>";
	print "<div id=tsunamiBody>\n";
   print "<span id=\"tsunamiTitle\"><h3>Tsunami Data Repository</h3></span>\n";
   print "<span id=\"tsunamiPage\"><h2>Upload - Create Titles</h2></span>\n";

   print "<div style = \"margin-left: 1.5em\">\n";

   print "<table border='0' cellpadding='0' cellspacing='0' class=\"tsunamiCell\"style=\"margin-bottom:2em\">\n";
   print "<tr><td style=\"color: #0081A0\">Files upload to:</td><td>$location -> $siteList</td></tr>\n";
	print "</table>\n";

	print "<form action=\"/tsunami/\" onsubmit=\"return CheckSubmit(this);\">\n";
	print "<input type=\"hidden\" name=\"action\" value=\"UploadComplete\" />\n";
	print "<table border='0' cellpadding='0' cellspacing='0' class=\"tsunamiCell\">\n";

foreach ($dList as $key => $doclib) {

	print "<tr><td align-left style=\"padding-right:1em\">".$doclib->getName()."</td><td style=\"position:right\">Title:</td>\n";
	print "<td><input type=\"text\" name=\"Title_".$doclib->getId()."\" size=\"30\" /></td></tr>\n";
	print "<tr><td></td><td align-right>Description (optional):</td>\n";
	print "<td><input type=\"text\" name=\"Desc_".$doclib->getId()."\" size=\"45\" /></td></tr>\n";

}
	print "</table>\n";
   print "<div style = \"margin-left:26em\">\n";
   print "<input type=\"submit\" value= \"Done\" name=\"Done\") />\n";
   print "</div>\n";
   print "<input type=\"hidden\" name=\"pId\" value=\"".$pId."\" />\n";
   print "<input type=\"hidden\" name=\"sId\" value=\"".$sId."\" />\n";
   print "</form>\n";
   print "</div>\n"; // Close the 1.5em indent
   print "</div>\n"; // Close tsunamiBody

	print getGoogleAnalyticsText() . "\n";
	print "</body></html>\n";


?>
