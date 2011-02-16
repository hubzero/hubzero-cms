<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
	
$out = '<div class="componentheading">{global:formtitle}</div>'."\n";
$out.='{error}'."\n";
$out.='<table border="0">'."\n";

$page_num = 0;
if (!empty($pagefields))
{
	$out .= "\t".'<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->'."\n";
	$out .= "\t".'<tbody id="rsform_'.$formId.'_page_'.$page_num.'">'."\n";
}
	
foreach ($quickfields as $quickfield)
{
	if (in_array($quickfield, $pagefields))
	{
		$page_num++;
		$last_page  = $quickfield == end($pagefields);
		$last_field = $quickfield == end($quickfields);
		
		$out.= "\t".'<tr class="rsform-block rsform-block-'.JFilterOutput::stringURLSafe($quickfield).'">'."\n";
		$out.= "\t\t<td>&nbsp;</td>\n";
		$out.= "\t\t<td>{".$quickfield.":body}</td>\n";
		$out.= "\t\t<td>&nbsp;</td>\n";
		$out.= "\t</tr>\n";
		
		$out .= "\t".'</tbody>'."\n";
		if (!$last_page || !$last_field)
		{
			$out .= '<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->'."\n";
			$out .= "\t".'<tbody id="rsform_'.$formId.'_page_'.$page_num.'">'."\n";
		}
			
		continue;
	}
	
	$required = in_array($quickfield, $requiredfields) ? ' '.(isset($this->_form->Required) ? $this->_form->Required : '(*)') : "";
	$out.= "\t".'<tr class="rsform-block rsform-block-'.JFilterOutput::stringURLSafe($quickfield).'">'."\n";
	$out.= "\t\t<td>{".$quickfield.":caption}".$required."</td>\n";
	$out.= "\t\t<td>{".$quickfield.":body}<div class=\"formClr\"></div>{".$quickfield.":validation}</td>\n";
	$out.= "\t\t<td>{".$quickfield.":description}</td>\n";
	$out.= "\t</tr>\n";
}
if (!empty($pagefields))
	$out .= "\t".'</tbody>'."\n";
$out.= "</table>\n";

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