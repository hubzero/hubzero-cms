<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2018 HUBzero Foundation, LLC.
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
 * @author		M. Drew LaMar <drew.lamar@gmail.com>
 * @copyright Copyright 2005-2018 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */
 
namespace Components\Fmns\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Fmns\Models\Fmn;

use Request;
use Notify;
use Event;
use Lang;
use User;
use App;

/**
 * FMN controller class for entries
 */
class Fmns extends SiteController
{
	/**
	 * Determine task to perform and execute it.
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('add', 'edit');

		parent::execute();
	}

	/**
	 * Default task (main FMN page)
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		$this->view->model = new Fmn();

		$this->view
		     ->setLayout('intro')
		     ->display();
	}
  
  /**
	 * Display specific page
	 *
	 * @return	void
	 */
  public function pageTask()
  {
    $pageName = \Request::getCmd('page', 'intro');
    $this->view
		     ->setLayout($pageName)
		     ->display();    
  }
  
  /**
   * Browse
   *
   * @return	void
   */
  public function browseTask()
  {        
    $this->view->script = $this->drawGanttChart();
    
    $this->view
         ->setLayout('browse')
         ->display();    
  }
  
  /**
	 * Draw a Gantt chart of FMNs
	 *
	 * @return  string   $script JavaScript of chart
	 */
  private function drawGanttChart()
  {
    $fmns = Fmn::whereEquals('state', 1)->
            order('start_date', 'asc');

    $jsObj = '';
    $script = '';

    // num published fmns
    $total = $fmns->total();
    $count = 0;
    foreach ($fmns as $k => $v)
    {
      if ($v->get('state') == 1) {
        $count++;
        $normTime = ceil(100*min(max((time() - strtotime($v->date('start_date', $as='seconds')))/(strtotime($v->date('stop_date', $as='seconds')) - strtotime($v->date('start_date', $as='seconds'))), 0), 1));
        $jsObj .= "[\"{$v->name()}\", 
                    \"{$v->name()}\",
                    \"{$v->get('id')}\",
                    new Date(\"{$v->date()}\"), 
                    new Date(\"{$v->date('stop_date')}\"),
                    null,
                    {$normTime},
                    null,
                    \"{$v->link('group')}\"]";
        if ($count < $total)
        {
          $jsObj .= ", \n \t\t\t\t";
        }
      }      
    }
    
    $script = "
      google.charts.load(\"current\", {
        packages:[\"gantt\"]
      });

      google.charts.setOnLoadCallback(draw);
      
      function draw(data) {
        var data = new google.visualization.DataTable();
        var chart = new google.visualization.Gantt(document.getElementById('browse_gantt_chart'));
        drawChart(data, chart);
      }

      function drawChart(data, chart) {
        data.addColumn('string', 'Task ID');
        data.addColumn('string', 'Task Name');
        data.addColumn('string', 'Resource');
        data.addColumn('date', 'Start Date');
        data.addColumn('date', 'End Date');
        data.addColumn('number', 'Duration');
        data.addColumn('number', 'Percent Complete');
        data.addColumn('string', 'Dependencies');
        data.addColumn('string', 'URL');
        data.addRows([
          {$jsObj}
        ]);

        var options = {
          height: data.getNumberOfRows() * 42 + 50
        }

        // The select handler. Call the chart's getSelection() method
        function selectHandler() {
          var selectedItem = chart.getSelection()[0];
          if (selectedItem) {
            var value = data.getValue(selectedItem.row, 8);
            window.location.href = value;
          }
        }

        // Listen for the 'select' event, and call my function selectHandler() when
        // the user selects something on the chart.
        google.visualization.events.addListener(chart, 'select', selectHandler);
          
        chart.draw(data, options);
      }";

    return $script;
  }
}
