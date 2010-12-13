<?php

class plgYSearchSuffixes extends YSearchPlugin
{
	public static function onYSearchExpandTerms(&$terms)
	{
		$add = array();
		foreach ($terms as $term)
		{
			// eg nanoelectric <-> nanoelectronic
			if (preg_match('/^(.*?)(on)?ic$/', $term, $match))
				$add[] = count($match) == 3 ? $match[1].'ic' : $match[1].'onic';

			// the fulltext indexer mangles course names, but it helps if we add a space between the letters and numbers
			if (preg_match('/^([a-zA-Z]+)(\d+)/', $term, $course_name))
				$add[] = $course_name[1].' '.$course_name[2];
		}
		$terms = array_merge($terms, $add);
		foreach ($terms as $term)
		{
			// try plural
			$add[] = substr($term, 0, -1) == 's' ? $term.'es' : $term.'s';
			if (substr($term, 0, -1) == 'y')
				$add[] = substr($term, 0, strlen($term) -1).'ies';
		}
		$terms = array_merge($terms, $add);
	}
}
