<?php
// Search pages need to use a special paginator that is less specific about how
// many results there are.
// The default site paginator says something like "Viewing 20 results out of 433"
// which is only accurate here in a sense... that is the number of top-level <li>,
// but some of them have nested results in them, so the actual result count shown
// at the top of the page might be something like 470. To keep people from 
// wondering where their missing results are we just show them how many pages
// they have to go through.
// Sometimes it is possible to see the discrepency by multiplying the number of
// pages by the per-page setting, but I don't think anyone has ever bothered to
// to do so.
class SearchPages
{
	private $total, $offset, $limit;

	public function __construct($total, $offset, $limit)
	{
		$this->total = $total;
		$this->offset = $offset;
		$this->limit = $limit;
	}

	private function link_to($update)
	{
		$get = array();
		foreach (array_merge($_GET, array('limit' => $this->limit, 'limitstart' => $this->offset), $update) as $k=>$v)
			if (!in_array($k, array('option', 'Itemid')))
				$get[] = $k.'='.urlencode($v);
			
		return '/'.preg_replace('/^com_/', '', $_GET['option']).'/?'.join('&amp;', $get);
	}

	public function getListFooter()
	{
		$html = array();
		$html[] = '<div class="search-pages">';
		if ($this->limit != 0)
		{
			$html[] = '<p>Pages: ';
			$current_page = $this->offset / $this->limit + 1;
			$total_pages = ceil($this->total / $this->limit);
			for ($page_num = 1; $page_num < $current_page; ++$page_num)
				$html[] = '<a href="'.$this->link_to(array('limitstart' => $this->limit * ($page_num - 1))).'">'.$page_num.'</a>';
			$html[] = '<strong>'.$current_page.'</strong>';
			for ($page_num = $current_page + 1; $page_num <= $total_pages; ++$page_num)
				$html[] = '<a href="'.$this->link_to(array('limitstart' => $this->limit * ($page_num - 1))).'">'.$page_num.'</a>';
			$html[] = '</p>';
		}
		$html[] = '</div>';
	
		$html[] = '<div class="search-per-page">';
		$html[] = '<form action="'.$this->link_to(array()).'" method="get">';
		$html[] = '<p>';
		$html[] = '<select class="search-per-page-selector" name="limit">';
		foreach (array('5', '10', '15', '20', '30', '50', '100') as $per_page)
			$html[] = '<option value="'.$per_page.'"'.($per_page == $this->limit ? ' selected="selected"' : '').'>'.$per_page.' results per page</option>';
		$html[] = '<option value="0"'.($this->limit == 0 ? ' selected="select"' : '').'>Show all results</option>';
		$html[] = '</select>';
		$html[] = '<input type="hidden" name="terms" value="'.(array_key_exists('terms', $_GET) ? str_replace('"', '&quot;', $_GET['terms']) : '').'" />';
		$html[] = '<input type="submit" class="search-per-page-submitter" value="Go" />';
		$html[] = '</p>';
		$html[] = '</form>';
		$html[] = '</div>';
		
		return join("\n", $html);
	}
}
