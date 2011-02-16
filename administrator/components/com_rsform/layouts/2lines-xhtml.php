<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$out ='<fieldset class="formFieldset">'."\n";
$out.='<legend>{global:formtitle}</legend>'."\n";
$out.='{error}'."\n";

$page_num = 0;
$out.= '<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->'."\n";
$out.='<ol class="formContainer" id="rsform_'.$formId.'_page_'.$page_num.'">'."\n";

foreach ($quickfields as $quickfield)
{
	if (in_array($quickfield, $pagefields))
	{
		$page_num++;
		$last_page  = $quickfield == end($pagefields);
		$last_field = $quickfield == end($quickfields);
		
		$out.="\t".'<li>'."\n";
		$out.= "\t\t".'<div class="formCaption2">&nbsp;</div>'."\n";
		$out.= "\t\t".'<div class="formBody">{'.$quickfield.':body}</div>'."\n";
		$out.="\t".'</li>'."\n";
		
		$out .= "\t".'</ol>'."\n";
		if (!$last_page || !$last_field)
		{
			$out.= '<!-- Do not remove this ID, it is used to identify the page so that the pagination script can work correctly -->'."\n";
			$out.='<ol class="formContainer" id="rsform_'.$formId.'_page_'.$page_num.'">'."\n";
		}
			
		continue;
	}
	
	$required = in_array($quickfield, $requiredfields) ? '<strong class="formRequired">'.(isset($this->_form->Required) ? $this->_form->Required : '(*)').'</strong>' : "";
	$out.="\t".'<li>'."\n";
	$out.= "\t\t".'<div class="formCaption2">{'.$quickfield.':caption}'.$required.'</div>'."\n";
	$out.= "\t\t".'<div class="formBody">{'.$quickfield.':body}<span class="formClr">{'.$quickfield.':validation}</span></div>'."\n";
	$out.= "\t\t".'<div class="formDescription">{'.$quickfield.':description}</div>'."\n";
	$out.="\t".'</li>'."\n";
}
$out.='</ol>'."\n";
$out.='</fieldset>'."\n";
	
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