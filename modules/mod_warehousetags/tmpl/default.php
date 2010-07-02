<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<ol class="tags" style="margin:0;">
<?php 
foreach($strTagArray as $iKey=>$strTag){?>
  <li style="margin:0;"><a href="/warehouse/find?keywords=<?php echo urlencode(str_replace("_", " ", $strTag)); ?>"><?php echo str_replace("_", " ", $strTag); ?></a></li> 
<?php   
} 
?>
</ol>