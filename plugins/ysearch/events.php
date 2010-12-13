<?php

class plgYSearchEvents extends YSearchPlugin
{
	public static function onYSearch($request, &$results)
	{
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
				approved AND $weight > 0".
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
		$date = $res->get('date');
		return 
			'<p class="event-date">
			<span class="month">'.date('M', $date).'</span>
			<span class="day">'.date('d', $date).'</span>
			<span class="year">'.date('Y', $date).'</span>
			</p>';
	}
}
