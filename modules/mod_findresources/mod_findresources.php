<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

//-------------------------------------------------------------

	class modFindResources
	{
		private $params;

		//-----------

		public function __construct( $params ) 
		{
			$this->params = $params;
		}

		//-----------

		public function __set($property, $value)
		{
			$this->attributes[$property] = $value;
		}

		//-----------

		public function __get($property)
		{
			if (isset($this->attributes[$property])) {
				return $this->attributes[$property];
			}
		}
		
		//-----------

		public function display() 
		{
			require_once( JPATH_ROOT.DS.'components'.DS.'com_tags'.DS.'tags.tag.php' );
			require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.type.php');
			
			$database =& JFactory::getDBO();
			
			// Get some initial parameters
			$params =& $this->params;
			$numtags = $params->get( 'numtags', 25 );
			
			$obj = new TagsTag( $database );
			
			$tags = $obj->getTopTags( $numtags );
			
			// Get major types
			$t = new ResourcesType( $database );
			$categories = $t->getMajorTypes();
			
			// Start output
			$html = '';
			
			// search
			$html  .= '<form action="/xsearch/" method="get" class="search">'."\n";
			$html  .= '<fieldset>'."\n";
			$html  .= '<p>'."\n";
			$html  .= '<input type="text" name="searchword" value="" />'."\n";
			$html  .= '<input type="hidden" name="category" value="resources" />'."\n";
			$html  .= '<input type="submit" value="Search" />'."\n";
			$html  .= '</p>'."\n";
			$html  .= '</fieldset>'."\n";
			$html  .= '</form>'."\n";
			
			$tl = array();
			if (count($tags) > 0) {
				$html  .= '<ol class="tags">'."\n";
				$html  .= '    <li>'.JText::_('Popular Tags:').'</li>'."\n";
				foreach ($tags as $tag)
				{
					$tl[$tag->tag] = "\t".'<li><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag->tag).'">'.stripslashes($tag->raw_tag).'</a></li>'."\n";
				}
				if ($modtoptags->sortby == 'alphabeta') {
					ksort($tl);
				}
				$html .= implode('',$tl);	
				$html .= '<li><a href="/tags/" class="showmore">'.JText::_('More').' &rsaquo;</a></li>'."\n";
				$html .= '</ol>'."\n";
			} else {
				$html  .= '<p>'.JText::_('No tags found.').'</p>'."\n";
			}
			
			if (count($categories) > 0) {
				$html  .= '<p>'."\n";
				$i = 0;
				foreach ($categories as $category) 
				{
					$i++;
					$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $category->type);
					$normalized = strtolower($normalized);
					
					if (substr($normalized, -3) == 'ies') {
						$cls = $normalized;
					} else {
						$cls = substr($normalized, 0, -1);
					}
					$html  .= '<a href="'.JRoute::_('index.php?option=com_resources'.'&type='.$normalized).'">'.stripslashes($category->type).'</a>';
					$html  .= $i == count($categories) ? '...' : ', ';
					$html  .= "\n";
				}
				$html  .= '<a href="/resources" class="showmore">All Categores &rsaquo;</a>';
				$html  .= '</p>'."\n";
			}
			
			$html  .= '<div class="uploadcontent">'."\n";
			$html  .= ' <h4>Upload your own content! <span><a href="/contribute" class="contributelink">Get started &rsaquo;</a></span></h4>'."\n";
			$html  .= '</div>'."\n";
			
			echo $html;
		}		
	}

//-------------------------------------------------------------

$modfindresources = new modFindResources( $params );
require( JModuleHelper::getLayoutPath('mod_findresources') );
?>
