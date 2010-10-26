<?php // no direct access
defined('_JEXEC') or die('Restricted access');

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="introduction" class="section">
	<div class="aside">
		<h3>Questions?</h3>
		<ul>
			<li><a href="/kb/resources/faq">Resources FAQ</a></li>
			<li><a href="/contribute">Submit a resource</a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<div class="two columns first">
			<h3>What are resources?</h3>
			<p>Resources are user-submitted pieces of content that range from video presentations to publications to simulation tools.</p>
		</div>
		<div class="two columns second">
			<h3>Who can submit a resource?</h3>
			<p>Anyone can submit a resource! Resources must be relevant to the community and may undergo a short approval process to ensure all appropriate files and information are included.</p>
		</div>
		<div class="clear"></div>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">
	
	<div class="four columns first">
		<h2>Find a resource</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<form method="get" action="/search" class="search">
				<fieldset>
					<p>
						<input type="text" name="terms" id="searchword" size="50" value="" style="height:30px;-moz-border-radius: 5px-webkit-border-radius: 5px;	-khtml-border-radius: 5px;border-radius: 5px;"/>
						<input type="submit" value="Search" style="width: 108px;margin: 5px 0px 0px 100px " />
					</p>
				</fieldset>
			</form>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<!-- <div class="browse">
				<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>">Browse the list of available resources</a></p>
			</div> -->
		</div><!-- / .two columns second -->
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
<?php
$eotcategories = "(learningobjects|series|multimedia)";
$ignorecategories = "(notes)"; //categories to ignore
$categories = $this->categories;
if ($categories) {
?>
	<div class="four columns first">
	<img id="eot-img-header" src="templates/fresh/images/logos/neesacademysmall.jpg">
	<h2 id="eot-h2">Categories</h2>
		
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
	<?php
	$i = 0;
	$clm = '';
	/*if (count($categories)%3!=0) { 
	    ;
	}*/
	foreach ($categories as $category) 
	{
		$i++;
		
		
		
		
		$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $category->type);
		$normalized = strtolower($normalized);
		
		if (substr($normalized, -3) == 'ies') {
			$cls = $normalized;
		} else {
			$cls = substr($normalized, 0, -1);
		}
		
		if (!preg_match($eotcategories,strtolower($normalized))) {
					continue;
		}
		
		switch ($clm) 
		{
			case 'second': $clm = 'third'; break;
			case 'first': $clm = 'second'; break;
			case '':
			default: $clm = 'first'; break;
		}
		echo ResourcesHtml::writeIntroPageCategory( $clm, $cls,  $this->option, $normalized, $category, true);
		//( $clm, $option, $normalized, $category) 

		if ($clm == 'third') {
			echo '<div class="clear"></div>';
			$clm = '';
			$i = 0;
		}
	}
	if ($i == 1) {
		?>
		<div class="three columns second">
			<p> </p>
		</div><!-- / .three columns second -->
		<?php
	}
	if ($i == 1 || $i == 2) {
		?>
		<div class="three columns third">
			<p> </p>
		</div><!-- / .three columns third -->
		<?php
	}
?>
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>	
<?php
} //if categories
?>
<?php
$categories = $this->categories;
if ($categories) {
?>
	<div class="four columns first">
	<img id="eot-img-header" src="/templates/fresh/images/logos/neeshubsmall.jpg">
	<h2 id="eot-h2">Categories</h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
<?php
	$i = 0;
	$clm = '';
	/*if (count($categories)%3!=0) { 
	    ;
	}*/
	foreach ($categories as $category) 
	{
		$i++;
		
		$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $category->type);
		$normalized = strtolower($normalized);
		
		if (substr($normalized, -3) == 'ies') {
			$cls = $normalized;
		} else {
			$cls = substr($normalized, 0, -1);
		}
		
		if (preg_match($eotcategories,strtolower($normalized))) { // || preg_match($ignorecategories,strtolower($normalized))) { //This is where we can ignore categories
					continue;
		}
		
		switch ($clm) 
		{
			case 'second': $clm = 'third'; break;
			case 'first': $clm = 'second'; break;
			case '':
			default: $clm = 'first'; break;
		}
		echo ResourcesHtml::writeIntroPageCategory( $clm, $cls,  $this->option, $normalized, $category, false);

		if ($clm == 'third') {
			echo '<div class="clear"></div>';
			$clm = '';
			$i = 0;
		}
	}
	if ($i == 1) {
		?>
		<div class="three columns second">
			<p> </p>
		</div><!-- / .three columns second -->
		<?php
	}
	if ($i == 1 || $i == 2) {
		?>
		<div class="three columns third">
			<p> </p>
		</div><!-- / .three columns third -->
		<?php
	}
?>
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
<?php
}
?>

</div><!-- / .section -->

<script type="text/javascript">
$jQ("a.get-hidden-resource-info").fancybox( 
		{
			'hideOnContentClick': false,
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'overlayShow' : true,
			'autoDimensions' : false,
			'overlayOpacity' : 0.7,
			'width' : '80%',
			'height' : 500
		}
); 
</script>