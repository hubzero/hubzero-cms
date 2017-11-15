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

namespace Components\Templates\Models;

use Hubzero\Database\Relational;
use Hubzero\Database\Query;
use Hubzero\Config\Registry;

/**
 * Template style model
 */
class Style extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'template';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'home';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'template' => 'notempty',
		'title'    => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $always = array(
		'template'
	);

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data
	 * @return  string
	 */
	public function automaticTemplate($data)
	{
		return preg_replace("/[^A-Z0-9_\.-]/i", '', trim($data['template']));
	}

	/**
	 * Get parent template
	 *
	 * @return  object
	 */
	public function parent()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Template', 'template', 'element');
	}

	/**
	 * Duplicate the record
	 *
	 * @return  bool
	 */
	public function duplicate()
	{
		// Reset the id to create a new record.
		$this->set('id', 0);

		// Reset the home (don't want dupes of that field).
		$this->set('home', 0);

		// Alter the title.
		$this->set('title', $this->generateNewTitle($this->get('title')));

		if (!$this->save())
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to change the title.
	 *
	 * @param   string  $title  The title.
	 * @return  string  New title.
	 */
	protected function generateNewTitle($title)
	{
		// Alter the title
		$style = self::all()
			->whereEquals('title', $title)
			->row();

		if ($style->get('id'))
		{
			// Check if we are incrementing an existing pattern, or appending a new one.
			if (preg_match('#\((\d+)\)$#', $title, $matches))
			{
				$n = $matches[1] + 1;
				$title = preg_replace('#\(\d+\)$#', sprintf('(%d)', $n), $title);
			}
			else
			{
				$n = 2;
				$title .= sprintf(' (%d)', $n);
			}

			$title = $this->generateNewTitle($title);
		}

		return $title;
	}

	/**
	 * Overloaded save() method to ensure unicity of default style.
	 *
	 * @return  bool
	 */
	public function save()
	{
		$params = $this->get('params');

		if ($params)
		{
			if (is_array($params))
			{
				$params = new Registry($params);
			}

			if (is_object($params))
			{
				$params = $params->toString();
			}

			$this->set('params', $params);
		}

		if ($this->get('home'))
		{
			$query = new Query;
			$query->update($this->getTableName());
			$query->set(array('home' => '0'));
			$query->whereEquals('client_id', (int)$this->get('client_id'));
			$query->whereEquals('home', 1);
			$query->execute();
		}

		return parent::save();
	}

	/**
	 * Overloaded destroy() method to ensure existence
	 * of a default style for a template.
	 *
	 * @return  bool
	 */
	public function destroy()
	{
		if ($this->get('id'))
		{
			$styles = self::all()
				->whereEquals('client_id', $this->get('client_id'))
				->whereEquals('template', $this->get('template'))
				->rows();

			if ($styles->count() == 1 && $styles->current()->get('id') == $this->get('id'))
			{
				$this->addError(\Lang::txt('COM_TEMPLATES_ERROR_CANNOT_DELETE_LAST_STYLE'));
				return false;
			}
		}

		return parent::destroy();
	}
}
