<?php

/**
 * @title TrialXMLRenderer
 *
 * @abstract
 *    A class that knows how to convert a Trial domain object into a
 * chunk of XML compatible with htdocs/api/NEES.xsd's trialMetadataType
 *
 * @author
 *    Jinghong Gao
 *
 */

require_once 'lib/filesystem/FileCommandAPI.php';

class FileListXMLRenderer {
  function render($title, $dir, $fullpath,$fileAsLink=false,$expandImage=false,$showmetadata=0) {
    $output="";

    $fc = FileCommandAPI::create($fullpath);
    $ls = $fc->ls();
    $indent=1;
    $fl=implode(":",explode("/",$dir));
    $sectionTitle = $title." Files";
    if( count($ls) ) {
      $output .= "<filelist title=\"$sectionTitle\">\n";
      $output .= $this->dirlist($dir, $fullpath,$indent,$showmetadata,$fileAsLink,$expandImage);
      $output .= "</filelist>\n";
    }
    return $output;
  }

  function dirlist($dir, $basepath,$indent, $showmetadata=0,$fileAsLink=false,$expandImage=false)
  {
    $filetype=array("jpeg","jpg","gif","png");

    $output='';
    $indent++;
    //$fc = FileCommandAPI::create($dir);
    $fc = FileCommandAPI::create($dir);
    $ls = $fc->ls();
    foreach( $ls as $line => $type ) {
      for($i=0;$i<$indent;$i++)
        $output .= "\t";
      if( $type == 'directory' ) {
        $basepath .= ('/' . $line);
        $output .= "<directory name=\"".$line."\">\n";
        $output .= $this->dirlist( $dir . '/' . $line, $basepath,$indent, $showmetadata,$fileAsLink,$expandImage );
        for($i=0;$i<$indent;$i++)
          $output .= "\t";
        $output .= "</directory>\n";
      }
      else {
        preg_match("/^(.+)\s+([^\s]+)\s+(\d+)$/s", $line, $match);
        $filename = $match[1];
        $filedate = $match[2];
        $filesize = $match[3];
        $realpath = ($basepath . '/' . $filename);
        $tmp = (pathinfo($filename));
        $ext = isset($tmp['extension']) ? $tmp['extension'] : null;

        if(in_array(strtolower($ext),$filetype) && $expandImage)
          $type="image";
        else if ($fileAsLink)
          $type="file";
        else
          $type="item";

        $output .= "<$type path=\"".$dir . '/' . $filename ."\">$filename</$type>\n";
      }
    }
    return ($output);
  }
}
?>
