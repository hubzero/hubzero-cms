<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the submenu style, you would use the following include:
 * <jdoc:include type="module" name="test" style="submenu" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 */

/*
 * Module chrome for rendering the module in a submenu
 */
function modChrome_box($module, &$params, &$attribs)
{
	if ($module->content)
	{
?>
		<div class="box"<?php if (isset($attribs['id']) && $attribs['id']) { echo ' id="' . $attribs['id'] . '"'; } ?>>
<?php if ($module->showtitle != 0) : ?>
			<h3><?php echo $module->title; ?></h3>
<?php endif; ?>
			<div class="box-content">
				<?php echo $module->content; ?>
				<div class="clr"></div>
			</div><!-- / .box-content -->
		</div><!-- / .box -->
<?php
	}
}
