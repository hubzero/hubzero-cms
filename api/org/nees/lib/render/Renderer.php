<?php

/**
 * @title
 *    Base interface for Renderers
 *
 * @abstract
 *    This is the interface to be implemented by objects responsible for translating
 *  DomainObjects into some kind of text format.
 *
 * @author
 *    Adam Birnbaum
 *
 */
interface Renderer {
  function render( BaseObject $obj, $title = null );
}

function xmlEncode($pName)
{
  $pName=preg_replace("/&/","&amp;",$pName);
  $pName=preg_replace("/</","&lt;",$pName);
  $pName=preg_replace("/>/","&gt;",$pName);
  $pName=preg_replace("/\"/","&quot;",$pName);
  // database and xhtml pages are in UTF-8, html2ps and ps2pdf v.0B use ISO-8859-1
  $temp = iconv("UTF-8","ISO-8859-1",$pName);
  if ($temp == chr(176)) $pName = "&amp;deg;";
  return($pName);
}


?>
