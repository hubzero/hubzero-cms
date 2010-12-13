<?php
require dirname(__FILE__).'/../../models/search_pages.php';

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class YSearchViewYSearch extends JView
{
	protected $terms, $debug = array(), $results, $app;
	
	public function set_terms($terms) { $this->terms = $terms; }
	public function set_results($results) { $this->results = $results; }
	public function set_application(&$app) { $this->app =& $app; }
	
	public function display()
	{
		$this->url_terms = urlencode($this->terms->get_raw_without_section());
		@list($this->plugin, $this->section) = $this->terms->get_section();
		$this->pagination = new SearchPages($this->results->get_plugin_list_count(), $this->results->get_offset(), $this->results->get_limit());
		parent::display();
	}

	protected function attr($key, $val)
	{
		if (!empty($val))
			echo "$key=\"".str_replace('"', '&quot;', $val).'" ';
	}

	protected function html($html) { echo htmlentities($html); }

	public function debug($str)
	{
		$this->debug[] = $str;
	}

	public function debug_var($name, $var)
	{
		$this->debug('<b>'.$name.'</b>: '.var_export($var, true));
	}
}
