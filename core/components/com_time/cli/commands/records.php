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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Time\Cli\Commands;

use Hubzero\Console\Command\Base;
use Hubzero\Console\Command\CommandInterface;
use Hubzero\Console\Output;
use Hubzero\Console\Arguments;
use Components\Time\Models\Record;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'record.php';
require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'task.php';

/**
 * Time records class
 **/
class Records extends Base implements CommandInterface
{
	/**
	 * Default (required) command
	 *
	 * @return void
	 **/
	public function execute()
	{
		$this->index();
	}

	/**
	 * Lists all time records constrained by given constraints
	 *
	 * @return void
	 **/
	public function index()
	{
		$limit = $this->arguments->getOpt('limit', 20);

		foreach (Record::all()->limit($limit) as $record)
		{
			$line = '(' . $record->user->name . ') ' . $record->task->name . ': ' . $record->description;
			$this->output->addLine($line);
		}
	}

	/**
	 * Help output
	 *
	 * @return void
	 **/
	public function help()
	{
		$this->output->addOverview('Time records functions');
	}
}