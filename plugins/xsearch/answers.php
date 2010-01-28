<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

//-----------

jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_xsearch_answers' );

//-----------

class plgXSearchAnswers extends JPlugin
{
	public function plgXSearchAnswers(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'xsearch', 'answers' );
		$this->_params = new JParameter( $this->_plugin->params );
	}
	
	//-----------
	
	public function &onXSearchAreas() 
	{
		$areas = array(
			'answers' => JText::_('PLG_XSEARCH_ANSWERS')
		);
		return $areas;
	}

	//-----------

	public function onXSearch( $searchquery, $limit=0, $limitstart=0, $areas=null )
	{
		if (is_array( $areas ) && $limit) {
			if (!array_intersect( $areas, $this->onXSearchAreas() ) && !array_intersect( $areas, array_keys( $this->onXSearchAreas() ) )) {
				return array();
			}
		}

		// Do we have a search term?
		$t = $searchquery->searchTokens;
		if (empty($t)) {
			return array();
		}

		$database =& JFactory::getDBO();

		// Build the query
		$f_count = "SELECT COUNT(*)";
		$f_fields = "SELECT f.id, f.subject AS title, NULL AS alias, f.question AS itext, NULL AS ftext, f.state, f.created, f.created AS modified, f.created AS publish_up, NULL AS params,
					CONCAT( 'index.php?option=com_answers&id=', f.id ) AS href, 'answers' AS section, NULL AS area, NULL AS category, NULL AS rating, NULL AS times_rated, NULL AS ranking, NULL AS access, 3 AS relevance";

		$f_from = " FROM #__answers_questions AS f";

		$words   = $searchquery->searchTokens;
		$f_where = " WHERE f.state!=2 ";
		foreach ($words as $word) 
		{
			$f_where .= "AND ( (LOWER(f.subject) LIKE '%$word%') 
				OR (LOWER(f.question) LIKE '%$word%') 
				OR (SELECT COUNT(*) FROM #__answers_responses AS a WHERE a.state!=2 AND a.qid=f.id AND (LOWER(a.answer) LIKE '%$word%')) > 0 ) ";
		}

		$order_by  = " ORDER BY relevance DESC, title";
		$order_by .= ($limit != 'all') ? " LIMIT $limitstart,$limit" : "";

		if (!$limit) {
			// Get a count
			$database->setQuery( $f_count . $f_from . $f_where );
			return $database->loadResult();
		} else {
			if (count($areas) > 1) {
				return $f_fields . $f_from . $f_where;
			}
			
			// Get results
			$database->setQuery( $f_fields. $f_from . $f_where . $order_by );
			$rows = $database->loadObjectList();

			if ($rows) {
				foreach ($rows as $key => $row) 
				{
					$rows[$key]->href = JRoute::_('index.php?option=com_answers&task=question&id='.$row->id);
				}
			}

			return $rows;
		}
	}
	
	//----------------------------------------------------------
	// Optional custom functions
	// uncomment to use
	//----------------------------------------------------------

	/*public function documents() 
	{
		// ...
	}
	
	//-----------
	
	public function before()
	{
		// ...
	}*/
	
	//-----------
	
	public function out( $row, $keyword )
	{
		if (strstr( $row->href, 'index.php' )) {
			$row->href = JRoute::_('index.php?option=com_answers&task=question&id='.$row->id);
		}
		$juri =& JURI::getInstance();
		if (substr($row->href,0,1) == '/') {
			$row->href = substr($row->href,1,strlen($row->href));
		}
		
		// Start building the HTML
		$html  = "\t".'<li>'."\n";
		$html .= "\t\t".'<p class="title"><a href="'.$row->href.'">'.stripslashes($row->title).'</a></p>'."\n";
		if ($row->itext) {
			$html .= "\t\t".'<p>&#133; '.stripslashes($row->itext).' &#133;</p>'."\n";
		} else if ($row->ftext) {
			$html .= "\t\t".'<p>&#133; '.stripslashes($row->ftext).' &#133;</p>'."\n";
		}
		$html .= "\t\t".'<p class="href">'.$juri->base().$row->href.'</p>'."\n";
		$html .= "\t".'</li>'."\n";
		
		// Return output
		return $html;
	}
	
	//-----------
	
	/*public function after()
	{
		// ...
	}*/
}
