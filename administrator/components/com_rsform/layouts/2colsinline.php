<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$pages_array = array();
$page = 0;
if (!empty($pagefields))
	foreach ($quickfields as $quickfield)
	{
		$pages_array[$page][] = $quickfield;
		if (in_array($quickfield, $pagefields))
			$page++;
	}
else
	$pages_array[0] = $quickfields;

$out = '<div class="componentheading">{global:formtitle}</div>'."\n";
$out.='{error}'."\n";
	
foreach ($pages_array as $page_num => $items)
{
	$out.= '<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->'."\n";
	$out.= '<table border="0" id="rsform_'.$formId.'_page_'.$page_num.'">'."\n";
	
	$outLeft  ='<table border="0">'."\n";
	$outRight ='<table border="0">'."\n";

	$i = 0;
	foreach ($items as $quickfield)
	{
		$tmp = '';
		if (in_array($quickfield, $pagefields))
		{
			$tmp.= "\t\t\t\t".'<tr class="rsform-block rsform-block-'.JFilterOutput::stringURLSafe($quickfield).'">'."\n";
			$tmp.= "\t\t\t\t\t<td>&nbsp;</td>\n";
			$tmp.= "\t\t\t\t\t<td>{".$quickfield.":body}</td>\n";
			$tmp.= "\t\t\t\t\t<td>&nbsp;</td>\n";
			$tmp.= "\t\t\t\t</tr>\n";
		}
		else
		{
			$required = in_array($quickfield, $requiredfields) ? ' '.(isset($this->_form->Required) ? $this->_form->Required : '(*)') : "";
			
			$tmp.= "\t\t\t\t".'<tr class="rsform-block rsform-block-'.JFilterOutput::stringURLSafe($quickfield).'">'."\n";
			$tmp.= "\t\t\t\t\t<td>{".$quickfield.":caption}".$required."</td>\n";
			$tmp.= "\t\t\t\t\t<td>{".$quickfield.":body}<br/>\n";
			$tmp.= "\t\t\t\t\t{".$quickfield.":validation}</td>\n";
			$tmp.= "\t\t\t\t\t<td>{".$quickfield.":description}</td>\n";
			$tmp.= "\t\t\t\t</tr>\n";
		}

		if ($i%2)
			$outRight .= $tmp;
		else
			$outLeft .= $tmp;

		$i++;
	}

	$outLeft.="\t\t\t</table>\n";
	$outRight.="\t\t\t</table>\n";

	$out .= "\t<tr>\n";
	$out .= "\t\t<td valign=\"top\">\n";
	$out .= "\t\t\t".$outLeft."\n";
	$out .= "\t\t</td>\n";
	$out .= "\t\t<td valign=\"top\">\n";
	$out .= "\t\t\t".$outRight."\n";
	$out .= "\t\t</td>\n";
	$out .= "\t</tr>\n";
	$out .= "</table>\n";
}
	
if ($out != $this->_form->FormLayout && $this->_form->FormId)
{
	// Clean it
	// Update the layout
	$db = JFactory::getDBO();
	$db->setQuery("UPDATE #__rsform_forms SET FormLayout='".$db->getEscaped($out)."' WHERE FormId=".$this->_form->FormId);
	$db->query();
}
	
return $out;
?>