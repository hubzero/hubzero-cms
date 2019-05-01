<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Helpers\Usage;

use Components\Publications\Helpers\Usage as Base;
use Lang;
use Date;

/**
 * Extended resource stats class (And More)
 */
class Andmore extends Base
{
	/**
	 * Number of views
	 *
	 * @var string
	 */
	public $views = 'unavailable';

	/**
	 * Average view time
	 *
	 * @var string
	 */
	public $avg_view = 'unavailable';

	/**
	 * Total views
	 *
	 * @var string
	 */
	public $tot_view = 'unavailable';

	/**
	 * Constructor
	 *
	 * @param   object   &$db       Database
	 * @param   integer  $resid     Publication ID
	 * @param   integer  $type      Publication type
	 * @param   integer  $rating    Publication rating
	 * @param   integer  $cites     Number of citations
	 * @param   string   $lastcite  Last citation date
	 * @return  void
	 */
	public function __construct(&$db, $resid, $type, $rating=0, $cites=0, $lastcite='')
	{
		parent::__construct($db, $resid, $type, $rating, $cites, $lastcite);
	}

	/**
	 * Display formatted results for a given time range
	 *
	 * @param   string  $disp  Time range [curr, last, year, all]
	 * @return  string
	 */
	public function display($disp='ALL')
	{
		list($caption, $period) = $this->fetch($disp);

		if ($this->_type == 1)
		{
			$vlabel  = Lang::txt('Views');
			$avlabel = Lang::txt('Avg. view time');
		}
		else
		{
			$vlabel = Lang::txt('Downloads');
			$avlabel = Lang::txt('Avg. downloads');
		}

		$html = '';
		if ($this->users != 'unavailable' && $this->avg_view != 'unavailable')
		{
			$html .= '<table class="usagestats" summary="' . Lang::txt('Statistics for this resource') . '">' . "\n";
			$html .= ' <caption>' . Lang::txt('Usage Stats') . '</caption>' . "\n";
			$html .= ' <tfoot>' . "\n";
			$html .= '  <tr>' . "\n";
			$html .= '   <td colspan="2">' . $caption;
			if ($this->datetime)
			{
				$html .= ': ' . Lang::txt('updated') . ' ' . Date::of($this->datetime)->toLocal($this->dateFormat);
			}
			$html .= '</td>' . "\n";
			$html .= '  </tr>' . "\n";
			$html .= ' </tfoot>' . "\n";
			$html .= ' <tbody>' . "\n";
			if ($this->users != 'unavailable')
			{
				$html .= '  <tr>' . "\n";
				$html .= '   <th scope="row">' . Lang::txt('Users') . ':</th>' . "\n";
				$html .= '   <td>' . $this->users . '</td>' . "\n";
				$html .= '  </tr>' . "\n";
			}
			$html .= ' </tbody>' . "\n";
			$html .= '</table>' . "\n";
		}
		$html .= $this->display_substats();

		return $html;
	}

	/**
	 * Push database results to $this for internal use
	 *
	 * @param   array  $result  Database results
	 * @return  void
	 */
	public function process($result)
	{
		if ($result)
		{
			foreach ($result as $row)
			{
				$this->users    = $row->users;
				$this->views    = $row->jobs;
				$this->datetime = $row->processed_on;

				if ($row->avg_cpu == 0)
				{
					$this->avg_view = $row->avg_wall;
				}
				else
				{
					$this->avg_view = $row->avg_cpu;
				}

				if ($row->tot_cpu == 0)
				{
					$this->tot_view = $row->tot_wall;
				}
				else
				{
					$this->tot_view = $row->tot_cpu;
				}
			}
		}
	}
}
