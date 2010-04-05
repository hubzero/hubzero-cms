<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

//-------------------------------------------------------------

	class modRandomQuote
	{
		private $attributes = array();

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
			require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_feedback'.DS.'selectedquotes.class.php' );
			ximport('Hubzero_View_Helper_Html');
		
			$database =& JFactory::getDBO();
			
			$params =& $this->params;
			
			//Get the admin configured settings
			$filters = array();
			$filters['limit'] = 1;
			$charlimit = $params->get( 'charlimit', 150 );
			$showauthor = $params->get( 'show_author', 1 );
			$showall = $params->get( 'show_all_link', 1 );
			$quotesrc = $params->get( 'quotesrc', 'miniquote' );
		
			$pool = trim($params->get( 'quotepool'));
			$filters['notable_quotes'] = $pool == 'notable_quotes' ?  1 : 0;
			$filters['flash_rotation'] = $pool == 'flash_rotation' ?  1 : 0;
			$filters['miniquote'] = $quotesrc == 'miniquote' ?  1 : 0;
			$filters['sortby'] = 'RAND()';
	
			$this->filters = $filters;
	
			// Get quotes
			$sq = new SelectedQuotes( $database );
			$quotes = $sq->getResults( $filters );
			$quote = $quotes ? $quotes[0] : '';
			
			// Push some CSS to the template
			ximport('xdocument');
			XDocument::addModuleStylesheet('mod_randomquote');
			
			// Start output
			$html = '';
			$quote_to_show = $quotesrc == 'miniquote' ? stripslashes($quote->miniquote) : stripslashes($quote->short_quote);
			/*
			if($quote) {
				$html  .= '<p class="fquote">'."\n";
				$html  .= ' <a href="/about/quotes/?quoteid='.$quote->id.'" title="'.JText::_('View the full quote by').' '.stripslashes($quote->fullname).'. '.stripslashes($quote->org).'" class="showfullquote">'.Hubzero_View_Helper_Html::shortenText(stripslashes($quote_to_show), $charlimit, 0).'</a> '."\n" ;
				$html  .= ' <span> - '.$quote->fullname.', '.JText::_('in').'&nbsp;<a href="/about/quotes">'.JText::_('Notable Quotes').'</a></span></p>'."\n";
			}*/
			
			if($quote) {
				$html  .= '<h3 class="notable_quote">'.JText::_('Notable Quote').'</h3>'."\n";
				$html  .= '<div class="frontquote">'."\n";
				$html  .= ' <blockquote cite="'.$quote->fullname.'"><p>'."\n";
				$html  .= Hubzero_View_Helper_Html::shortenText(stripslashes($quote_to_show), $charlimit, 0)."\n" ;
				$html  .= strlen($quote->quote) > $charlimit 
				? '<a href="/about/quotes/?quoteid='.$quote->id.'" title="'.JText::_('View the full quote by').' '.$quote->fullname.'" class="showfullquote">...&raquo;</a>'."\n" 
				: '' ;
				$html  .= ' </p></blockquote>'."\n";
				$html  .= '<p class="cite"><cite>'.$quote->fullname.'</cite>, '.$quote->org.' <span>-</span> <span>'.JText::_('in').'&nbsp;<a href="/about/quotes">'.JText::_('Notable&nbsp;Quotes').'</a></span></p>'."\n";
				//$html  .= ' <p class="cite"><cite>'.$quote->fullname.'</cite> - '.JText::_('in').'&nbsp;<a href="/about/quotes">'.JText::_('Quotes').'</a></p>'."\n";
				$html  .= '</div>'."\n";
			}			
			echo $html;
		}		
	}

//-------------------------------------------------------------

$modrandomquote = new modRandomQuote( $params );
require( JModuleHelper::getLayoutPath('mod_randomquote') );
?>