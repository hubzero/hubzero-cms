<?php

/**
 * This is view file for cpanel
 *
 * PHP version 5
 *
 * @category   JFusion
 * @package    ViewsFront
 * @subpackage Wrapper
 * @author     JFusion Team <webmaster@jfusion.org>
 * @copyright  2008 JFusion. All rights reserved.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link       http://www.jfusion.org

 * Mofified by Antonio Duran to work with Joomdle
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<script language="javascript" type="text/javascript">
    function getElement(aID)
    {
        return (document.getElementById) ?
            document.getElementById(aID) : document.all[aID];
    }

    function getIFrameDocument(aID)
    {
        var rv = null;
        var frame=getElement(aID);
        // if contentDocument exists, W3C compliant (e.g. Mozilla)

        if (frame.contentDocument)
            rv = frame.contentDocument;
        else // bad IE  ;)

            rv = document.frames[aID].document;
        return rv;
    }



function print_r(x, max, sep, l) {

	l = l || 0;
	max = max || 10;
	sep = sep || ' ';

	if (l > max) {
		return "[WARNING: Too much recursion]\n";
	}

	var
		i,
		r = '',
		t = typeof x,
		tab = '';

	if (x === null) {
		r += "(null)\n";
	} else if (t == 'object') {

		l++;

		for (i = 0; i < l; i++) {
			tab += sep;
		}

		if (x && x.length) {
			t = 'array';
		}

		r += '(' + t + ") :\n";

		for (i in x) {
			try {
				r += tab + '[' + i + '] : ' + print_r(x[i], max, sep, (l + 1));
			} catch(e) {
				return "[ERROR: " + e + "]\n";
			}
		}

	} else {

		if (t == 'string') {
			if (x == '') {
				x = '(empty)';
			}
		}

		r += '(' + t + ') ' + x + "\n";

	}

	return r;

};



    function adjustMyFrameHeight()
    {
    	$jQ("#iframeloading").css("background", "");
        var frame = getElement("blockrandom");
        var frameDoc = getIFrameDocument("blockrandom");
		height = "XX";
		frameDoc1 = frame.contentDocument;
		frame.height = frameDoc.body.scrollHeight + 50; //XXX works for me with this margin, withoout it, bars are shown, even in Jfusion
        
    }



</script>
<!--  <a href='#' onClick='adjustMyFrameHeight()'>ajustar</a> -->  
<!-- <div class="contentpane">-->
<div id="iframeloading" style="background: transparent url(/components/com_resources/images/loading.gif) no-repeat 50% 35%;">
<!-- <h2 id="joomdlesectionheader" class="joomdleiframe">NEEShub assesment</h2>  -->
<!-- <h2 id="joomdlesectionheader" class="joomdleiframe"></h2> -->
<iframe
  
<?php if ($this->params->get('autoheight', 1)) { ?>
    onload="adjustMyFrameHeight();"
<?php 
} 
?>

id="blockrandom"
name="iframe"
src="<?php echo $this->wrapper->url; ?>"
width="<?php echo $this->params->get('width', '100%'); ?>"

<?php if (!$this->params->get('autoheight', 1)) { ?>
height="<?php echo $this->params->get('height', '500'); ?>"
<?php 
} 
?>

scrolling="<?php echo $this->params->get('scrolling', 'auto'); ?>"
<?php if ($this->params->get('transparency')) { ?>
    allowtransparency="true"
    <?php
} else { ?>
    allowtransparency="false"
    <?php
} ?>

align="top" frameborder="0" class="wrapper">
<?php echo JText::_('OLD_BROWSER'); ?>
</iframe>
</div> <!-- loading div -->

