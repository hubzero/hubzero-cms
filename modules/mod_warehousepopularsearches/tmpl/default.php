<?php // no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div id="popularSearches" style="margin-top:30px;">
  <p style="font-size:16px;font-weight:bold;color:#999999">Popular Searches</p>
  <ol>
    <?php 
      foreach($oPopularSearchArray as $oSearchArray){?>
        <li><a href="/warehouse/find?keywords=<?php echo urlencode($oSearchArray["KEYWORD"]); ?>"><?php echo $oSearchArray["KEYWORD"]; ?></a> (<?php echo $oSearchArray["COUNT"]; ?>)</li>
    <?php  	
      }
    ?>
  </ol>
</div>