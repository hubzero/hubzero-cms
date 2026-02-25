<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Content\Formathtml\Macros;

use Plugins\Content\Formathtml\Macro;

/**
 * A wiki macro for embedding images
 */
class Image extends Macro
{
    /**
     * Allow macro in partial parsing?
     *
     * @var string
     */
    public $allowPartial = true;

    /**
     * Container for element attributes
     *
     * @var array
     */
    private $attr = array();

    /**
     * Returns description of macro, use, and accepted arguments
     *
     * @return     string
     */
    public function description()
    {
        $txt = array();
        $txt['wiki'] = "Embed an image in wiki-formatted text. The first argument is the " .
            "file specification. The remaining arguments are optional and allow configuring " .
            "the attributes and style of the rendered `img` element:\n" .
            " * digits and unit are interpreted as the size (ex. 120, 25%) for the image\n" .
            " * `right`, `left`, `top` or `bottom` are interpreted as the alignment\n" .
            " * `link=some Link...` replaces the link to the image source\n" .
            " * `nolink` means without link to image source (deprecated, use `link=`)\n" .
            " * `key=value` style are interpreted as HTML attributes or CSS style indications\n" .
            " * align, border, width, height, alt, title, longdesc, class, id and usemap\n" .
            " * `border` can only be a number";
        $txt['html'] = '<p>Embed an image in wiki-formatted text. The first argument is the ' .
            'file specification. The remaining arguments are optional and allow configuring ' .
            'the attributes and style of the rendered <code>&lt;img&gt;</code> element:</p>
<ul>
<li>digits and unit are interpreted as the size (ex. 120, 25%) for the image</li>
<li><code>right</code>, <code>left</code>, <code>top</code> or <code>bottom</code> ' .
        'are interpreted as the alignment for the image</li>
<li><code>link=some Link...</code> replaces the link to the image source by the one ' .
        'specified using Link. If no value is specified, the link is simply removed.</li>
<li><code>nolink</code> means without link to image source (deprecated, use ' .
        '<code>link=</code>)</li>
<li><code>key=value</code> style are interpreted as HTML attributes or CSS style ' .
        'indications for the image. Valid keys are:</li>
<li>align, border, width, height, alt, title, longdesc, class, id and usemap</li>
<li><code>border</code> can only be a number</li>
</ul>
<p>Examples:</p>
<ul>
<li><code>[[Image(photo.jpg)]]</code> # simplest</li>
<li><code>[[Image(photo.jpg, desc="My caption here")]]</code> # caption text</li>
<li><code>[[Image(photo.jpg, 120px)]]</code> # with image width size</li>
<li><code>[[Image(photo.jpg, right)]]</code> # aligned by keyword</li>
<li><code>[[Image(photo.jpg, nolink)]]</code> # without link to source</li>
<li><code>[[Image(photo.jpg, align=right)]]</code> # aligned by attribute</li>
<li><code>[[Image(photo.jpg, 120px, class=mypic)]]</code> # with image width size and a CSS class</li>
</ul>';

        return $txt['html'];
    }

    /**
     * Generate macro output based on passed arguments
     *
     * @return     string HTML image tag on success or error message on failure
     */
    public function render()
    {
        $content = strip_tags($this->args);

        // args will be null if the macro is called without parenthesis.
        if (!$content) {
            return '';
        }

        // Parse arguments
        // We expect the 1st argument to be a filename
        $args   = explode(',', $content);
        $file   = array_shift($args);

        $this->attr   = array();
        $this->attr['href']  = '';
        $this->attr['style'] = array();

        // Get single attributes
        // EX: [[Image(myimage.png, nolink, right)]]
        $singlePattern = '/[, ](left|right|top|center|bottom|[0-9]+(px|%|em)?)(?:[, ]|$)/i';
        $argues = preg_replace_callback($singlePattern, array(&$this, 'parseSingleAttribute'), $content);
        // Get quoted attribute/value pairs
        // EX: [[Image(myimage.png, desc="My description, contains, commas")]]
        $quotedPattern = '/[, ](alt|altimage|desc|title|width|height|align|border|longdesc|class|id|usemap|link)' .
            '=(?:["\'])([^"]*)(?:["\'])/i';
        $argues = preg_replace_callback($quotedPattern, array(&$this, 'parseAttributeValuePair'), $content);
        // Get non-quoted attribute/value pairs
        // EX: [[Image(myimage.png, width=100)]]
        $unquotedPattern = '/[, ](alt|altimage|desc|title|width|height|align|border|longdesc|class|id|usemap|link)' .
            '=([^"\',]*)(?:[, ]|$)/i';
        $argues = preg_replace_callback($unquotedPattern, array(&$this, 'parseAttributeValuePair'), $content);

        $attr = $this->attr;

        // Get wiki config
        $this->config = \Hubzero\Facades\Component::params('com_wiki');
        if ($this->filepath != '') {
            $this->config->set('filepath', $this->filepath);
        }
        $imgs = explode(',', $this->config->get('img_ext', 'jpg, jpeg, jpe, gif, png'));
        $imgs = array_map('trim', $imgs);
        $imgs = array_map('strtolower', $imgs);
        $this->imgs = $imgs;

        $ret = false;
        // Is it numeric?
        if (is_numeric($file)) {
            include_once \Hubzero\Facades\Component::path('com_wiki') . DS . 'models' . DS . 'attachment.php';

            // Get resource by ID
            $attach = \Components\Wiki\Models\Attachment::oneOrNew(intval($file));

            // Check for file existence
            $fileExists = $attach->filename &&
                (file_exists($this->path($attach->filename)) || file_exists($this->path($attach->filename, true)));
            if ($fileExists) {
                $attr['desc'] = (isset($attr['desc'])) ? $attr['desc'] : '';
                if (!$attr['desc']) {
                    $attr['desc'] = ($attach->description) ? stripslashes($attach->description) : '';
                }

                $ret = true;
            }
        } elseif (file_exists($this->path($file)) || file_exists($this->path($file, true))) {
            // Check for file existence
            $attr['desc'] = (isset($attr['desc'])) ? $attr['desc'] : ''; //$file;

            $ret = true;
        }

        // Does the file exist?
        if ($ret) {
            if (!in_array(strtolower(\Hubzero\Facades\Filesystem::extension($file)), $this->imgs)) {
                return '(Image(' . $content . ') failed - File provided is not an allowed image type)';
            }

            // Return HTML
            return $this->embed($file, $attr);
        } else {
            // Return error message
            return '(Image(' . $content . ') failed - File not found)'; // . $this->path($file);
        }
    }

    /**
     * Parse attribute=value pairs
     * EX: [[Image(myimage.png, desc="My description, contains, commas", width=200)]]
     *
     * @param      array $matches Values matching attr=val pairs
     * @return     void
     */
    public function parseAttributeValuePair($matches)
    {
        $key = strtolower(trim($matches[1]));
        $val = trim($matches[2]);

        $size   = '/^[0-9]+(%|px|em)+$/';
        $attrs  = '/(alt|altimage|desc|title|width|height|align|border|longdesc|class|id|usemap)=(.+)/';
        $quoted = "/(?:[\"'])(.*)(?:[\"'])$/";

        // Set width if just a pixel size is given
        // e.g., [[File(myfile.jpg, width=120px)]]
        if (preg_match($size, $val, $matches) && $key != 'border') {
            if ($matches[0]) {
                $this->attr['style']['width'] = $val;
                //$this->attr['width'] = $val;
                return;
            }
        }

        if (is_numeric($val)) {
            $this->attr['style']['width'] = $val . 'px';
            $this->attr['width'] = $val;
        }
        // Specific call to NOT link an image
        // Links images by default
        if ($key == 'nolink') {
            $this->attr['href'] = 'none';
            return;
        }
        // Check for a specific link given
        if ($key == 'link') {
            $this->attr['href'] = 'none';

            if ($val) {
                $this->attr['href'] = $val;

                $urlPtrn = "[^=\"\']*(https?:|mailto:|ftp:|gopher:|news:|file:)" .
                    "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
                if (preg_match("/$urlPtrn/", $val)) {
                    $this->attr['rel']  = 'external';
                }
            }
            return;
        }
        // Check for alignment, no key given
        // e.g., [[File(myfile.jpg, left)]]
        if (in_array($key, array('left', 'right', 'top', 'bottom', 'center'))) {
            if ($key == 'center') {
                $this->attr['style']['display'] = 'block';
                $this->attr['style']['margin-right'] = 'auto';
                $this->attr['style']['margin-left'] = 'auto';
            } else {
                $this->attr['style']['float'] = $key;
                if ($key == 'left') {
                    $this->attr['style']['margin-right'] = '1em';
                } elseif ($key == 'right') {
                    $this->attr['style']['margin-left'] = '1em';
                }
            }
            return;
        }

        // Look for any other attributes
        if ($key == 'align') {
            if ($val == 'center') {
                $this->attr['style']['display'] = 'block';
                $this->attr['style']['margin-right'] = 'auto';
                $this->attr['style']['margin-left'] = 'auto';
            } else {
                $this->attr['style']['float'] = $val;
                if ($val == 'left') {
                    $this->attr['style']['margin-right'] = '1em';
                } elseif ($val == 'right') {
                    $this->attr['style']['margin-left'] = '1em';
                }
            }
        } elseif ($key == 'border') {
            $this->attr['style']['border'] = '#ccc ' . intval($val) . 'px solid';
        } else {
            $this->attr[$key] = $val;
        }
        return;
    }

    /**
     * Handle single attribute values
     * EX: [[Image(myimage.png, nolink, right)]]
     *
     * @param      array $matches Values matching the single attribute pattern
     * @return     void
     */
    public function parseSingleAttribute($matches)
    {
        $key = strtolower(trim($matches[1]));

        // Set width if just a pixel size is given
        // e.g., [[File(myfile.jpg, 120px)]]
        $size   = '/[0-9+](%|px|em)+$/';
        if (preg_match($size, $key, $matches)) {
            if ($matches[0]) {
                $this->attr['style']['width'] = $key;
                //$this->attr['width'] = $key;
                return;
            }
        }

        if (is_numeric($key)) {
            $this->attr['style']['width'] = $key . 'px';
            $this->attr['width'] = $key;
        }

        // Specific call to NOT link an image
        // Links images by default
        if ($key == 'nolink') {
            $this->attr['href'] = 'none';
            return;
        }

        // Check for alignment, no key given
        // e.g., [[File(myfile.jpg, left)]]
        if (in_array($key, array('left', 'right', 'top', 'bottom', 'center'))) {
            if ($key == 'center') {
                $this->attr['style']['display'] = 'block';
                $this->attr['style']['margin-right'] = 'auto';
                $this->attr['style']['margin-left'] = 'auto';
            } else {
                $this->attr['style']['display'] = 'block';
                $this->attr['style']['float'] = $key;
                if ($key == 'left') {
                    $this->attr['style']['margin-right'] = '1em';
                } elseif ($key == 'right') {
                    $this->attr['style']['margin-left'] = '1em';
                }
            }
            return;
        }

        return;
    }

    /**
     * Generate an absolute path to a file stored on the system
     * Assumes $file is relative path but, if $file starts with / then assumes absolute
     *
     * @param      string  $file  Filename
     * @param      boolean $alt
     * @return     string
     */
    private function path($file, $alt = false)
    {
        if (substr($file, 0, 1) == DS) {
            $path = PATH_APP . $file;
        } else {
            if ($alt) {
                $nid = null;
                $bits = explode('/', $this->config->get('filepath', '/site/wiki'));
                foreach ($bits as $bit) {
                    if (is_numeric($bit)) {
                        $nid = $bit;
                        $id = preg_replace('~^[0]*([1-9][0-9]*)$~', '$1', intval($bit));
                        break;
                    }
                }
                if ($nid) {
                    $this->config->set('filepath', str_replace($nid, $id, $this->config->get('filepath')));
                }
            }
            $path  = PATH_APP . DS . trim($this->config->get('filepath', '/site/wiki'), DS);
            $path .= ($this->pageid) ? DS . $this->pageid : '';
            $path .= DS . $file;
        }

        return $path;
    }

    /**
     * Generate a link to a file
     * If $file starts with (http|https|mailto|ftp|gopher|feed|news|file), then it's an external URL and returned
     *
     * @param      string $file Filename
     * @return     string
     */
    private function link($file)
    {
        $urlPtrn = "[^=\"\'](https?:|mailto:|ftp:|gopher:|feed:|news:|file:)" .
            "([^ |\\/\"\']*\\/)*([^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_])";
        if (preg_match("/$urlPtrn/", $file) || substr($file, 0, 1) == DS) {
            return $file;
        }

        $file = trim($file, DS);

        if (\Hubzero\Facades\Request::getString('format') == 'pdf') {
            return $this->path($file);
        }
        $link  = DS . substr($this->option, 4, strlen($this->option)) . DS;
        if ($this->scope) {
            $scope = trim($this->scope, DS);

            $link .= $scope . DS;
        }
        $link .= $this->pagename . DS . 'Image:' . $file;

        return \Hubzero\Facades\Route::url($link);
    }

    /**
     * Generates HTML to embed an <img>
     *
     * @param      string $file File to embed
     * @param      array  $attr Attributes to apply to the HTML
     * @return     string
     */
    private function embed($file, $attr = array())
    {
        $attr['alt'] = (isset($attr['alt'])) ? htmlentities($attr['alt'], ENT_COMPAT, 'UTF-8') : $attr['desc'];
        if (!$attr['alt']) {
            $attr['alt'] = $file;
        }

        $styles = '';
        if (count($attr['style']) > 0) {
            $s = array();
            foreach ($attr['style'] as $k => $v) {
                $s[] = strtolower($k) . ':' . $v;
            }
            $styles = implode('; ', $s);
        }
        $attr['style'] = '';

        $attribs = array();
        foreach ($attr as $k => $v) {
            $k = strtolower($k);
            if ($k != 'href' && $k != 'rel' && $k != 'desc' && $v) {
                $attribs[] = $k . '="' . trim($v, '"') . '"';
            }
        }

        $html  = '<span class="figure"' . ($styles ? ' style="' . $styles . '"' : '') . '>';

        $img = '<img src="' . $this->link($file) . '" ' . implode(' ', $attribs) . ' />';

        if ($attr['href'] == 'none') {
            $html .= $img;
        } else {
            $attr['href'] = ($attr['href']) ? $attr['href'] : $this->link($file);
            $attr['rel']  = (isset($attr['rel'])) ? $attr['rel'] : 'lightbox';

            $html .= '<a rel="' . $attr['rel'] . '" href="' . $attr['href'] . '">' . $img . '</a>';
        }
        if (isset($attr['desc']) && $attr['desc']) {
            $html .= '<span class="figcaption">' . $attr['desc'] . '</span>';
        }
        $html .= '</span>';

        return $html;
    }
}
