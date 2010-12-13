<?php

class plgYSearchWeightTitle
{
	public static function onYSearchWeightAll($terms, $res)
	{
		$title = strtolower($res->get_title());
		$strterms = strtolower($terms->get_raw_without_section());
		if (preg_replace('/[^a-z]/', '', $title) == preg_replace('/[^a-z]/', '', $terms))
			return 10;

		$title_stems = self::stem_list($title);
		if (!($title_len = count($title_stems))) return 0.5;

		$term_stems = array();
		foreach ($terms->get_positive_chunks() as $chunk)
			foreach (self::stem_list($chunk) as $stem)
				$term_stems[] = $stem;
		$term_stems = array_unique($term_stems);
		if (!($term_len = count($term_stems))) return 0.5;

		$intersecting_stems = count(array_intersect($title_stems, $term_stems));

		return 1 - (($term_len - $intersecting_stems) * (0.8/$term_len));
	}

	private static function stem_list($str)
	{
		$stems = array();
		foreach (array_unique(preg_split('/\s+/', trim($str))) as $word)
			if (!DocumentMetadata::is_stop_word($word))
				$stems[] = stem(preg_replace('/[^[:alnum:]]/', '', $word));
		return $stems;
	}
}
