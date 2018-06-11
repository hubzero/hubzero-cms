<?php 

defined('_HZEXEC_') or die();
$this->css()
     ->css('publications.css', 'com_publications')
     ->js('publications.js', 'com_publications');


?>


<h1><?php echo $this->selectedpub;	 ?> </h1>


<p class="title"><a href="<?php echo $this->row->href; ?>"><?php echo $this->escape(stripslashes($this->row->title)); ?></a></p>
<p class="href"><?php echo Request::base() . ltrim($this->row->href, '/'); ?></p>
<h1><?php	 ?> </h1>