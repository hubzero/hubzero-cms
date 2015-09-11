<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author	Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 * @since	 Class available since release 1.3.2
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;
use Hubzero\Utility\String;
use Hubzero\Base\Object;

/**
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Format extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'citations';
	// table name jos_citations

	/**
	 * Default order by for model
	 *
	 * @var string
	 **/
	public $orderBy = 'name';


	protected $table = '#__citations_format';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		//'name'	=> 'notempty',
		//'liaison' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var array
	 **/
	public $always = array(
		//'name_normalized',
		//'asset_id'
	);


	/**
	 * Defines a one to one relationship with citation
	 *
	 * @return $this
	 * @since  1.3.2
	 **/
	public function citation()
	{
		return $this->belongsToOne('Citation', 'id', 'format');
	}


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
