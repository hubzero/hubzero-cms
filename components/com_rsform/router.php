<?php
/**
* @version 1.3.0
* @package RSform!Pro 1.3.0
* @copyright (C) 2007-2011 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined( '_JEXEC' ) or die( 'Restricted access' );

function RSFormBuildRoute(&$query)
{
	$segments = array();
	
	$view = isset($query['view']) ? $query['view'] : 'rsform';
	
	switch ($view)
	{
		case 'submissions':
			$layout = isset($query['layout']) ? $query['layout'] : 'default';
			switch ($layout)
			{
				case 'view':
					$segments[] = 'view-submission';
					$segments[] = @$query['cid'];
					
					unset($query['view'], $query['layout'], $query['cid']);
				break;
				
				default:
				case 'default':
					$segments[] = 'view-submissions';
					
					unset($query['view'], $query['layout']);
				break;
			}
		break;
		
		default:
		case 'rsform':
			if (!empty($query['formId']))
			{
				$segments[] = 'form';
				
				$formId = (int) $query['formId'];
				
				$db = JFactory::getDBO();
				$db->setQuery("SELECT `FormTitle` FROM #__rsform_forms WHERE `FormId`='".$formId."'");
				$formName = JFilterOutput::stringURLSafe($db->loadResult());
				
				$segments[] = $formId.(!empty($formName) ? ':'.$formName : '');
				
				unset($query['formId']);
			}
		break;
	}
	
	return $segments;
}

function RSFormParseRoute($segments)
{
	$query = array();
	
	$segments[0] = !empty($segments[0]) ? $segments[0] : 'form';
	$segments[0] = str_replace(':', '-', $segments[0]);
	
	switch ($segments[0])
	{
		default:
		case 'form':
			$exp = explode(':', @$segments[1]);
			$query['formId'] = (int) @$exp[0];
		break;
		
		case 'view-submissions':
			$query['view'] = 'submissions';
		break;
		
		case 'view-submission':
			$query['view'] = 'submissions';
			$query['layout'] = 'view';
			$query['cid'] = @$segments[1];
		break;
	}
	
	return $query;
}
?>