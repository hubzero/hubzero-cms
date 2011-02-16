<?php

require_once '../static/ProjectEditor.php';
require_once '../util/StringHelper.php';
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

 //$strFile = "";
 //$imageInfo = GetImageSize($strFile);
 //var_dump($imageInfo);
 //$src_img = ImageCreateFromBMP($strFile);
 
 $strUrl = "https://".$_SERVER['SERVER_NAME'].ProjectEditor::DEFAULT_PROJECT_URL;
 //print "before=".$strUrl."\n";
 echo "before=".$strUrl."<br>";
 if(StringHelper::endsWith($strUrl, ProjectEditor::DEFAULT_PROJECT_URL)){
   $strUrl = str_replace("[id]", "22", $strUrl);
 }
 //print "after=".$strUrl."\n";
 echo "after=".$strUrl."<br>";

?>
