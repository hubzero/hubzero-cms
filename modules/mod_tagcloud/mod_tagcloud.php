<?php

function cloud()
{
	$base = 140;
	$mod = 30;

	$dbh = JFactory::getDBO();
	$dbh->setQuery('select tagid, count(*) as count, tag, raw_tag, description from #__tags_object jto inner join #__tags jt on jt.id = tagid where raw_tag not like \'tool:%\' group by tagid order by count desc limit 20');
	if (!($tags = $dbh->loadAssocList()))
		return;

	$max = $tags[0]['count'];
	$min = $tags[count($tags) - 1]['count'];
	$mid = $min + (($max - $min)/2);

	usort($tags, create_function('$a, $b', 'return strcmp($a["raw_tag"], $b["raw_tag"]);'));

	echo '<h2>Top Tags</h2>';
	echo '<ol class="tags">';
	foreach ($tags as $tag)
	{
		$size = $base;
		if ($tag['count'] > $mid)
			$size += $mod * ($tag['count']/$max);
		elseif ($tag['count'] < $mid)
			$size -= $mod * ($min/$tag['count']);
		echo '<li><a href="/tags/'.$tag['tag'].'" title="'.htmlentities($tag['description']).'" style="font-size: '.$size.'%">'.$tag['raw_tag'].'</a> </li>';
	}
	echo '</ol>';
	echo '<a class="more" href="/tags/">More tags &rsaquo;</a>';
}
cloud();
