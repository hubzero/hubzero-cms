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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Handles asynchrous enqueuement, helps maintain the index
 */
require_once Component::path('com_search') . DS . 'helpers' . DS . 'solr.php';

class plgSearchSolr extends \Hubzero\Plugin\Plugin
{
	/**
	 * onContentAvailable 
	 * 
	 * @param mixed $table 
	 * @param mixed $model 
	 * @access public
	 * @return void
	 */
	public function onContentAvailable($table, $model)
	{
		 $type = $this->getType($table);
		 $id = $model->getPkValue();

		 if ($type != false)
		 {	
		 	\Components\Search\Helpers\SolrHelper::enqueueDB($type, array($id), 'index');	
		}
	}

	/**
	 * getType 
	 * 
	 * @param mixed $table 
	 * @access private
	 * @return void
	 */
	private function getType($table)
	{
		$type = false;
		switch ($table)
		{
			case '#__blog_entries':
				$type = 'blog-entry';
			break;

			case '#__resources':
				$type = 'resource';
			break;

			case '#__courses':
				$type = 'course';
			break;

			case '#__events':
				$type = 'event';
			break;

			case '#__users':
				$type = 'member';
			break;

			case '#__citations':
				$type = 'citation';
			break;

			case '#__projects':
				$type = 'project';
			break;

			case '#__answer_questions':
				$type = 'question';
			break;

			case '#__kb_articles':
				$type = 'kb-article';
			break;

			case '#__publications':
				$type = 'publication';
			break;

			case '#__wiki_pages':
				$type = 'wiki';
			break;

			case '#__xgroups':
				$type = 'group';
			break;
		}
		return $type;
	}
}
