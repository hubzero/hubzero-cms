<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers\Usage;

use Components\Resources\Helpers\Usage as Base;
use Lang;
use Date;

/**
 * Extended resource stats class (Tools)
 */
class Tools extends Base
{
	/**
	 * Number of jobs
	 *
	 * @var string
	 */
	var $jobs     = 'unavailable';

	/**
	 * Average wall time
	 *
	 * @var string
	 */
	var $avg_wall = 'unavailable';

	/**
	 * Total wall time
	 *
	 * @var string
	 */
	var $tot_wall = 'unavailable';

	/**
	 * Average CPU time
	 *
	 * @var mixed
	 */
	var $avg_cpu  = 'unavailable';

	/**
	 * Total CPU time
	 *
	 * @var mixed
	 */
	var $tot_cpu  = 'unavailable';

	/**
	 * Average execution time
	 *
	 * @var string
	 */
	var $avg_exec = 'unavailable';

	/**
	 * Total execution time
	 *
	 * @var string
	 */
	var $tot_exec = 'unavailable';

	/**
	 * Constructor
	 *
	 * @param      object  &$db      Database
	 * @param      integer $resid    Resource ID
	 * @param      integer $type     Resource type
	 * @param      integer $rating   Resource rating
	 * @param      integer $cites    Number of citations
	 * @param      string  $lastcite Last citation date
	 * @return     void
	 */
	public function __construct(&$db, $resid, $type, $rating=0, $cites=0, $lastcite='')
	{
		parent::__construct($db, $resid, $type, $rating, $cites, $lastcite);
	}

	/**
	 * Display formatted results for a given time range
	 *
	 * @param      string $disp Time range [curr, last, year, all]
	 * @return     string
	 */
	public function display($disp='ALL')
	{
		list($caption, $period) = $this->fetch($disp);

		$html = '';
		if ($this->users != 'unavailable' && $this->jobs != 'unavailable' && $this->avg_exec != 'unavailable')
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
				$html .= '   <th scope="row">'.Lang::txt('Users').':</th>' . "\n";
				$html .= '   <td>' . $this->users . '</td>' . "\n";
				$html .= '  </tr>' . "\n";
			}
			if ($this->jobs != 'unavailable')
			{
				$html .= '  <tr>' . "\n";
				$html .= '   <th scope="row">'.Lang::txt('Jobs').':</th>' . "\n";
				$html .= '   <td>' . $this->jobs . '</td>' . "\n";
				$html .= '  </tr>' . "\n";
			}
			if ($this->avg_exec != 'unavailable')
			{
				$html .= '  <tr>' . "\n";
				$html .= '   <th scope="row"><abbr title="Average">Avg.</abbr> <abbr title="execution">exec.</abbr> time:</th>' . "\n";
				$html .= '   <td>' . $this->valfmt($this->avg_exec) . '</td>' . "\n";
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
	 * @param      array &$result Database records
	 * @return     boolean False if errors, true on success
	 */
	public function process($result)
	{
		if ($result)
		{
			foreach ($result as $row)
			{
				$this->users    = $row->users;
				$this->jobs     = $row->jobs;
				$this->avg_wall = $row->avg_wall;
				$this->tot_wall = $row->tot_wall;
				$this->avg_cpu  = $row->avg_cpu;
				$this->tot_cpu  = $row->tot_cpu;
				$this->datetime = $row->processed_on;

				// Changed by Swaroop on 06/25/2007: Avg. exec. time = Avg. wall time
				if ($this->avg_cpu == 0)
				{
					$this->avg_exec = $this->avg_wall;
				}
				else
				{
					$this->avg_exec = $this->avg_cpu;
				}

				if ($this->tot_cpu == 0)
				{
					$this->tot_exec = $this->tot_wall;
				}
				else
				{
					$this->tot_exec = $this->tot_cpu;
				}
			}
			return true;
		}
		return false;
	}
}
