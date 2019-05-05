<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;
use Component;

/**
 * Citation format model
 *
 * @uses \Hubzero\Database\Relational
 */
class Format extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'citations';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 **/
	public $orderBy = 'name';

	/**
	 * Table name
	 *
	 * @var  string
	 **/
	protected $table = '#__citations_format';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'style' => 'notempty'
	);

	/**
	 * Defines a one to one relationship with citation
	 *
	 * @param   string  $fallbackDefault
	 * @return  object
	 **/
	public static function getDefault($fallbackDefault = 'IEEE')
	{
		$config = Component::params('com_citations');
		$defaultFormat = !empty($config->get('default_citation_format')) ? $config->get('default_citation_format'): $fallbackDefault;
		$format = self::blank();
		$format->whereEquals('style', $defaultFormat)->limit(1);
		return $format->row();
	}

	/**
	 * Defines a one to one relationship with citation
	 *
	 * @return  object
	 **/
	public function citations()
	{
		return $this->belongsToMany('Citation', 'format', 'style');
	}

	/**
	 * Defines a one to one relationship with citation
	 *
	 * @return  array
	 **/
	public function getTemplateKeys()
	{
		$template_keys =  array(
			"type" => "{TYPE}",
			"cite" => "{CITE KEY}",
			"ref_type" => "{REF TYPE}",
			"date_submit" => "{DATE SUBMITTED}",
			"date_accept" => "{DATE ACCEPTED}",
			"date_publish" => "{DATE PUBLISHED}",
			"author" => "{AUTHORS}",
			"editor" => "{EDITORS}",
			"title" => "{TITLE/CHAPTER}",
			"booktitle" => "{BOOK TITLE}",
			"chapter" => "{CHAPTER}",
			"journal" => "{JOURNAL}",
			"journaltitle" => "{JOURNAL TITLE}",
			"volume" => "{VOLUME}",
			"number" => "{ISSUE/NUMBER}",
			"pages" => "{PAGES}",
			"isbn" => "{ISBN/ISSN}",
			"issn" => "{ISSN}",
			"doi" => "{DOI}",
			"series" => "{SERIES}",
			"edition" => "{EDITION}",
			"school" => "{SCHOOL}",
			"publisher" => "{PUBLISHER}",
			"institution" => "{INSTITUTION}",
			"address" => "{ADDRESS}",
			"location" => "{LOCATION}",
			"howpublished" => "{HOW PUBLISHED}",
			"url" => "{URL}",
			"eprint" => "{E-PRINT}",
			"note" => "{TEXT SNIPPET/NOTES}",
			"organization" => "{ORGANIZATION}",
			"abstract" => "{ABSTRACT}",
			"year" => "{YEAR}",
			"month" => "{MONTH}",
			"search_string" => "{SECONDARY LINK}",
			"sec_cnt" => "{SECONDARY COUNT}"
		);

		return $template_keys;
	}
}
