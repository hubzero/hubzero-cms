<?php

class plgYSearchEvents extends YSearchPlugin
{
	public static function onYSearch($request, &$results)
	{
		ximport('xdocument');
		XDocument::addComponentStylesheet('com_events');

		$terms = $request->get_term_ar();
		$weight = 'match(e.title, e.content) against(\''.join(' ', $terms['stemmed']).'\')';

		$addtl_where = array();
		foreach ($terms['mandatory'] as $mand)
			$addtl_where[] = "(e.title LIKE '%$mand%' OR e.content LIKE '%$mand%')";
		foreach ($terms['forbidden'] as $forb)
			$addtl_where[] = "(e.title NOT LIKE '%$forb%' AND e.content NOT LIKE '%$forb%')";

		$rows = new YSearchResultSQL(
			"SELECT 
				e.title,
				e.content AS description,
				concat('/events/details/', e.id) AS link,
				$weight AS weight,
				publish_up AS date,
				'Events' AS section
			FROM jos_events e
			WHERE 
				approved AND publish_down > CURRENT_TIMESTAMP AND $weight > 0".
				($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '').
			" ORDER BY $weight DESC"
		);
		foreach ($rows->to_associative() as $row)
		{
			if (!$row) continue;
			$row->set_description(preg_replace('/(\[+.*?\]+|\{+.*?\}+|[=*])/', '', $row->get_description()));
			$results->add($row);
		}
	}

  public static function onBeforeYSearchRenderEvents($res)
  {
    return 
      '<p class="event-date">
        <span class="month">Nov</span>
        <span class="day">05</span>
        <span class="year">2009</span>
      </p>';
  }
}
