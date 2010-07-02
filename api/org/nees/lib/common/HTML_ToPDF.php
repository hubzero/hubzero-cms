<?php
/** $Id: HTML_ToPDF.php 2063 2006-01-10 17:09:37Z jrust $ */
// {{{ license

// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/3_0.txt                                   |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Jason Rust <jrust@rustyparts.com>                           |
// +----------------------------------------------------------------------+

// }}}
// {{{ includes

require_once 'PEAR.php';


// }}}
// {{{ HTML_ToPDF class

/**
 * A class to convert a local html file to a pdf file on the fly.
 *
 * Will take a local or remote html file and convert it to a PDF file.  Note
 * that you can add encryption or permissions to the PDF file by using the helper
 * PDFEncryptor class that comes with this package.  See the README and
 * examples for more information.
 *
 * @author Jason Rust <jrust@rustyparts.com>
 * @version 3.4
 * @package HTML_ToPDF
 * @copyright The PHP License
 */

// }}}
class HTML_ToPDF {
    // {{{ properties

    /**
     * The full path to the file we are parsing
     * @var string
     */
    var $htmlFile = '';

    /**
     * The full path to the output file
     * @var string
     */
    var $pdfFile = '';

    /**
     * The temporary directory to save intermediate files
     * @var string
     */

   var $tmpDir = '/tmp';
 // 	var $tmpDir = ini_get("upload_temp_dir");

    /**
     * Whether or not we are in debug mode
     * @var bool
     */
    var $debug = false;

    /**
     * Whether we output html errors.
     * @var bool
     */
    var $htmlErrors = false;

    /**
     * The default domain for relative and absolute paths in
     * images, css, etc.
     * @var string
     */
    var $defaultDomain = '';

    /**
     * The default path for relative paths in images, css etc.
     * @var string
     */
    var $defaultPath = '/';

    /**
     * The path to the html2ps executable
     * @var string
     */
     // set this in the calling program using setHtml2Ps()
     var $html2psPath = '';

    /**
     * The path to the ps2pdf executable
     * @var string
     */

     // set this in the calling program using setPs2Pdf()
	 var $ps2pdfPath = '';

    /**
     * The path to your get URL program, including options to get headers
     * @var string
     */
    var $getUrlPath = '/usr/bin/curl -i';

    /**
     * Whether or not to try and parse the CSS in the html file and use it in
     * creating the pdf
     * @var bool
     */
    var $useCSS = true;

    /**
     * Other styles to use when parsing the page
     * @var string
     */
    var $additionalCSS = '';

    /**
     * Show the page in color?
     * @var bool
     */
    var $pageInColor = true;

    /**
     * Show the images be in grayscale?
     * @var bool
     */
    var $grayScale = false;

    /**
     * Scale factore for the page
     * @var int
     */
    var $scaleFactor = 1;

    /**
     * Whether to underline links or not
     * @var bool
     */
    var $underlineLinks = null;

    /**
     * The header information
     * @var array
     */
    var $headers = array('left' => '$T', 'right' => '$[author]');

    /**
     * The footer information
     * @var array
     */
    var $footers = array('center' => '- $N -');

    /**
     * Default html2ps configuration that we use (is parsed before being used, though)
     * @var string
     */
    var $html2psrc = '
        option {
          titlepage: 0;         /* do not generate a title page */
          toc: 0;               /* no table of contents */
          colour: %pageInColor%; /* create the page in color */
          underline: %underlineLinks%;         /* underline links */
          grayscale: %grayScale%; /* Make images grayscale? */
          scaledoc: %scaleFactor%; /* Scale the document */
        }
        package {
          geturl: %getUrlPath%; /* path to the geturl */
          ImageMagick:1;
          PerlMagick:1;
        }
        showurl: 0;             /* do not show the url next to links */';

    /**
     * Whether HTML_ToPDF should replace all relative image paths in the
     * input HTML document with the default domain or not. Switch this to
     * false if you want to convert a HTML file which is located locally in the
     * file system and is not reachable via HTTP but all the images used
     * in the HTML file are located correctly according to their relative
     * paths.
     * @var bool
     */
    var $makeAbsoluteImageUrls = true;

    /**
     * Include path for ps2pdf (-I option), for example to specify where
     * to search for font files etc.
     * @var string
     */
    var $ps2pdfIncludePath = '';

    /**
     * We use this to store the html file to a string for manipulation
     * @var string
     */
    var $_htmlString = '';

    // }}}
    // {{{ constructor

    /**
     * Initializes the class
     *
     * @param string $in_htmlFile The full path to the html file to convert
     * @param string $in_domain The default domain name for images that have a absolute or relative path
     * @param string $in_pdfFile (optional) The full path to the pdf file to output.
     *               If not given then we create a temporary name.
     *
     * @access public
     * @return void
     */
    function HTML_ToPdf($in_htmlFile, $in_domain, $in_pdfFile = null)
    {
        $this->htmlFile = $in_htmlFile;
        $this->defaultDomain = $in_domain;
        // We'll set it to a temporary name later, if needed, so that tmpDir can be set.
        $this->pdfFile = $in_pdfFile;
        $this->htmlErrors = (php_sapi_name() != 'cli' && !(substr(php_sapi_name(),0,3)=='cgi' &&
                    !isset($_SERVER['GATEWAY_INTERFACE'])));
    }

    // }}}
    // {{{ addHtml2PsSettings()

    /**
     * Adds on more html2ps settings to the end of the default set of settings
     *
     * @param string $in_settings The additional settings
     *
     * @access public
     * @return void
     */
    function addHtml2PsSettings($in_settings) {
        $this->html2psrc .= "\n" . $in_settings;
    }

    // }}}
    // {{{ setDefaultPath()

    /**
     * Sets the default path for relative paths in images, css etc.
     *
     * @param string $in_path A web-path, default is /
     *
     * @access public
     * @return void
     */
    function setDefaultPath($in_path)
    {
        // Default paths should always have a trailing slash...
        if ($in_path{strlen($in_path) - 1} != '/') {
            $in_path .= '/';
        }

        $this->defaultPath = $in_path;
    }

    // }}}
    // {{{ setDebug()

    /**
     * Sets the debug variable
     *
     * @param bool $in_debug Turn debugging on or off?
     *
     * @access public
     * @return void
     */
    function setDebug($in_debug)
    {
        $this->debug = $in_debug;
    }

    // }}}
    // {{{ setHeader()

    /**
     * Sets a header
     *
     * @param string $in_attribute One of the header attributes that html2ps accepts.  Most
     *               common are left, center, right, font-family, font-size, color.
     * @param string $in_value The attribute value.  Special values that can be set are $T
     *               (document title), $N (page number), $D (current date/time), $U (current
     *               url or filename), $[meta-name] (A meta-tag, such as $[author] to get
     *               author meta tag)
     *
     * @access public
     * @return void
     */
    function setHeader($in_attribute, $in_value)
    {
        $this->headers[$in_attribute] = $in_value;
    }

    // }}}
    // {{{ setFooter()

    /**
     * Sets a footer
     *
     * @param string $in_attribute One of the header attributes that html2ps accepts.  Most
     *               common are left, center, right, font-family, font-size, color.
     * @param string $in_value The attribute value.  Special values that can be set are $T
     *               (document title), $N (page number), $D (current date/time), $U (current
     *               url or filename), $[meta-name] (A meta-tag, such as $[author] to get
     *               author meta tag)
     *
     * @access public
     * @return void
     */
    function setFooter($in_attribute, $in_value)
    {
        $this->footers[$in_attribute] = $in_value;
    }

    // }}}
    // {{{ setTmpDir()

    /**
     * Set the temporary directory path
     *
     * @param string $in_path The full path to the tmp dir
     *
     * @access public
     * @return void
     */
    function setTmpDir($in_path) {
        $this->tmpDir = $in_path;
    }

    // }}}
    // {{{ setUseColor()

    /**
     * Set whether to use color or not when creating the page
     *
     * @param bool $in_useColor Use color?
     *
     * @access public
     * @return void
     */
    function setUseColor($in_useColor) {
        $this->pageInColor = $in_useColor;
    }

    // }}}
    // {{{ setUseCSS()

    /**
     * Set whether to try and use the CSS in the html page when creating
     * the pdf file
     *
     * @param bool $in_useCSS Use CSS found in html file?
     *
     * @access public
     * @return void
     */
    function setUseCSS($in_useCSS) {
        $this->useCSS = $in_useCSS;
    }

    // }}}
    // {{{ setAdditionalCSS()

    /**
     * Set additional CSS to use when parsing the html file
     *
     * @param string $in_css The additional css
     *
     * @access public
     * @return void
     */
    function setAdditionalCSS($in_css) {
        $this->additionalCSS = $in_css;
    }

    // }}}
    // {{{ setGetUrlPath()

    /**
     * Sets the get url which is used for retrieving images from the html file
     * needs to be the full path to the file with options to retrive the headers
     * as well.
     *
     * @param string $in_getUrl The get url program
     *
     * @access public
     * @return void
     */
    function setGetUrl($in_getUrl) {
        $this->getUrlPath = $in_getUrl;
    }

    // }}}
    // {{{ setGrayScale()

    /**
     * Sets the gray scale option for images
     *
     * @param bool $in_grayScale Images should be in grayscale?
     *
     * @access public
     * @return void
     */
    function setGrayScale($in_grayScale) {
        $this->grayScale = $in_grayScale;
    }

    // }}}
    // {{{ setUnderlineLinks()

    /**
     * Sets the option to underline links or not
     *
     * @param bool $in_underline Links should be underlined?
     *
     * @access public
     * @return void
     */
    function setUnderlineLinks($in_underline) {
        $this->underlineLinks = $in_underline;
    }

    // }}}
    // {{{ setScaleFactor()

    /**
     * Sets the scale factor for the page.  Less than one makes it smaller,
     * greater than one enlarges it.
     *
     * @param int $in_scale Scale factor
     *
     * @access public
     * @return void
     */
    function setScaleFactor($in_scale) {
        $this->scaleFactor = $in_scale;
    }

    // }}}
    // {{{ setHtml2Ps()

    /**
     * Sets the path to the html2ps program
     *
     * @param string $in_html2ps The html2ps program
     *
     * @access public
     * @return void
     */
    function setHtml2Ps($in_html2ps=null) {
    	if ($in_html2ps == null) {
    		 $thispath = dirname(__FILE__);
        	$this->html2psPath = $thispath."/html2ps";
    	} else {
        	$this->html2psPath = $in_html2ps;
    	}
    }


    // }}}
    // {{{ setPs2Pdf()

    /**
     * Sets the path to the ps2pdf program
     *
     * @param string $in_ps2pdf The ps2pdf program
     *
     * @access public
     * @return void
     */
    function setPs2Pdf($in_ps2pdf=null) {
    	if ($in_ps2pdf == null) {
    		 $thispath =dirname(__FILE__);
       	 	$this->ps2pdfPath = $thispath.'/ps2pdf';
    	} else {
        	$this->ps2pdfPath = $in_ps2pdf;}
    }



    // }}}
    // {{{ setMakeAbsoluteImageUrls()

    /**
     * Sets the makeAbsoluteImageUrls variable
     *
     * @param bool $in_makeAbsoluteImageUrls Replace relative image
     *                                       URLs in the input HTML
     *                                       file with default domain?
     *
     * @access public
     * @return void
     */
    function setMakeAbsoluteImageUrls($in_makeAbsoluteImageUrls)
    {
        $this->makeAbsoluteImageUrls = $in_makeAbsoluteImageUrls;
    }

    // }}}
    // {{{ setPs2pdfIncludePath()

    /**
     * Sets the ps2pdfIncludePath() variable
     *
     * @param string $in_ps2pdfIncludePath The include path for ps2pdf
     *
     * @access public
     * @return void
     */
    function setPs2pdfIncludePath($in_ps2pdfIncludePath)
    {
        $this->ps2pdfIncludePath = $in_ps2pdfIncludePath;
    }

    // }}}
    // {{{ convert()

    /**
     * Convert the html file into a pdf file
     *
     * @access public
     * @return string The path to the pdf file
     */
    function convert()
    {
        // make sure html file exists
        if (!file_exists($this->htmlFile) && !preg_match(':^(f|ht)tps?\://:i', $this->htmlFile)) {
            return new HTML_ToPDFException("Error: The HTML file does not exist: $this->htmlFile");
        }

        // first make sure we can execute the programs
        // html2ps is just a perl script on windows though
        if (!OS_WINDOWS && !@is_executable($this->html2psPath)) {
            return new HTML_ToPDFException("Error: html2ps [$this->html2psPath] not executable");
        }

        if (!@is_executable($this->ps2pdfPath)) {
            return new HTML_ToPDFException("Error: ps2pdf [$this->ps2pdfPath] not executable");
        }

        // this can take a while with large files
        set_time_limit(160);

        // read the html file in so we can modify it
        $this->_htmlString = @implode('', @file($this->htmlFile));
        // grab extra CSS
        $this->additionalCSS .= $this->_getCSSFromFile();
        // modify the conf file
        $this->_modifyConfFile();
        $paperSize = $this->_getPaperSize();
        $orientation = $this->_getOrientation();

        if ($this->makeAbsoluteImageUrls) {
            // prepend relative image paths with the default domain and path
//            $this->_htmlString = preg_replace(':<img (.*?)src=["\']((?!/)(?!http\://).*?)["\']:i', '<img \\1 src="http://'.$this->defaultDomain.$this->defaultPath.'\\2?GAsession='.$_COOKIE['GAsession'].'&PHPSESSID='.$_COOKIE['PHPSESSID'].'"', $this->_htmlString);
            $this->_htmlString = preg_replace(':<img (.*?)src=["\']((?!/)(?!http\://).*?)["\']:i', '<img \\1 src="http://'.$this->defaultDomain.$this->defaultPath.'\\2"', $this->_htmlString);
            // prepend absolute image paths with the default domain
//            $this->_htmlString = preg_replace(':<img (.*?)src=["\'](/.*?)["\']:i', '<img \\1 src="http://'.$this->defaultDomain.'\\2?GAsession='.$_COOKIE['GAsession'].'&PHPSESSID='.$_COOKIE['PHPSESSID'].'"', $this->_htmlString);
            $this->_htmlString = preg_replace(':<img (.*?)src=["\'](/.*?)["\']:i', '<img \\1 src="http://'.$this->defaultDomain.'\\2"', $this->_htmlString);
        }

        // html2ps messes up on several form elements
        $this->_htmlString = preg_replace(':<input (.*?)type=["\']?(hidden|submit|button|image|reset|file)["\']?.*?>:i', '<input />', $this->_htmlString);

        $a_tmpFiles = array();
        // the conf file has to be an actual file
        $a_tmpFiles['config'] = tempnam($this->tmpDir, 'CONF-');

        if (!@is_writable($a_tmpFiles['config'])) {
            return new HTML_ToPDFException("Error: the tmp directory is not writable.");
        }

        $fp = fopen($a_tmpFiles['config'], 'w');
        fwrite($fp, $this->html2psrc);
        fclose($fp);
        $this->_dumpDebugInfo("html2ps config: $this->html2psrc");

        // make the temporary html file.  We need an html extension for
        // at least one version of html2ps
        $a_tmpFiles['html'] = tempnam($this->tmpDir, 'HTML-');
        while (is_file($a_tmpFiles['html'] . '.html')) {
            unlink($a_tmpFiles['html']);
            $a_tmpFiles['html'] = tempnam($this->tmpDir, 'HTML-');
        }

        @unlink($a_tmpFiles['html']);
        $a_tmpFiles['html'] .= '.html';
        $fp = fopen($a_tmpFiles['html'], 'w');
        fwrite($fp, $this->_htmlString);
        fclose($fp);

        // need a temporary postscript file as well
        $a_tmpFiles['ps'] = tempnam($this->tmpDir, 'PS-');

        $tmp_result = array();
        $cmd = $this->html2psPath . ' ' . $orientation . ' -f ' . $a_tmpFiles['config'] . ' -o ' .
                $a_tmpFiles['ps'] . ' ' . $a_tmpFiles['html'] .  ' 2>&1';
        exec($cmd, $tmp_result, $retCode);
        $this->_dumpDebugInfo("html2ps command run: $cmd");
        $this->_dumpDebugInfo("html2ps output: " . @implode("\n", $tmp_result));

        // Windows exec returns no error codes
        if ($retCode != 0 && !OS_WINDOWS) {
            $this->_cleanup($a_tmpFiles);
            return new HTML_ToPDFException("Error: there was a problem running the html2ps command.  Error code returned: $retCode.  setDebug() for more information.");
        }

        $tmp_result = array();
        $this->pdfFile = is_null($this->pdfFile) ? tempnam($this->tmpDir, 'PDF-') : $this->pdfFile;
        // In case the windows path has spaces in it
        $this->ps2pdfPath = OS_WINDOWS ? '"' . $this->ps2pdfPath . '"' : $this->ps2pdfPath;
        $cmd = $this->ps2pdfPath . ' -sPAPERSIZE=' . $paperSize . ' -I' . $this->ps2pdfIncludePath . ' ' .
            ' -dAutoFilterColorImages=false -dColorImageFilter=/FlateEncode ';
        if (OS_WINDOWS) {
            // Because \ gets eaten by escapeshellcmd()
            $this->pdfFile = str_replace(DIRECTORY_SEPARATOR, '/', $this->pdfFile);
            $cmd .= '-dCompatibilityLevel=1.2 -q -dNOPAUSE -dBATCH -sDEVICE=pdfwrite -sOutputFile=' .
                    escapeshellcmd($this->pdfFile) . ' -c .setpdfwrite -f ' . $a_tmpFiles['ps'];
        }
        else {
            $cmd .= $a_tmpFiles['ps'] .  ' \'' . escapeshellcmd($this->pdfFile) .  '\' 2>&1';
        }

        exec($cmd, $tmp_result, $retCode);

        $this->_dumpDebugInfo("ps2pdf command run: $cmd");
        $this->_dumpDebugInfo("ps2pdf output: " . @implode("\n", $tmp_result));
        if ($retCode != 0 && !OS_WINDOWS) {
            $this->_cleanup($a_tmpFiles);
            return new HTML_ToPDFException("Error: there was a problem running the ps2pdf command.  Error code returned: $retCode.  setDebug() for more information.");
        }

        $this->_cleanup($a_tmpFiles);
        return $this->pdfFile;
    }

    // }}}
    // {{{ _modifyConfFile()

    /**
     * Modify the config file and put in our custom variables
     *
     * @access private
     * @return void
     */

    function _modifyConfFile()
    {
        // first determine if we should try and figure out underline link option, based on css
        if (is_null($this->underlineLinks)) {
            if (preg_match(':a\:link {.*?text-decoration\: (.*?);.*?}:is', $this->additionalCSS, $matches) &&
                is_int(strpos($matches[1], 'none'))) {
                $this->underlineLinks = false;
            }
            else {
                $this->underlineLinks = true;
            }
        }

        $ga = isset($_COOKIE['GAsession']) ? $_COOKIE['GAsession'] : null;
        $this->html2psrc = str_replace('%scaleFactor%', $this->scaleFactor, $this->html2psrc);
        $this->html2psrc = str_replace('%getUrlPath%', $this->getUrlPath." -b GAsession=".$ga." ", $this->html2psrc);
        // we convert booleans into numbers
        $this->html2psrc = str_replace('%pageInColor%', (int) $this->pageInColor, $this->html2psrc);
        $this->html2psrc = str_replace('%grayScale%', (int) $this->grayScale, $this->html2psrc);
        $this->html2psrc = str_replace('%underlineLinks%', (int) $this->underlineLinks, $this->html2psrc);

        // Add header and footer information
        $this->html2psrc .= "\nheader {\n" . $this->_processHeaderFooter($this->headers);
        $this->html2psrc .= "}\nfooter {\n" . $this->_processHeaderFooter($this->footers);
        $this->html2psrc .= '}';

        // Add in paper size if not present to ensure that headers/footer will always show
        if (!preg_match('/@page.*?{.*?size:\s*(.*?);/is', $this->additionalCSS)) {
            $this->additionalCSS .= "\n@page {\n";
            $this->additionalCSS .= "  size: 8.5in 11in;\n";
            $this->additionalCSS .= "}\n";
        }

        // add the global container
        $this->html2psrc = '
        @html2ps {
          ' . $this->html2psrc . '
        }
        ' . $this->additionalCSS;
    }

    // }}}
    // {{{ _getCSSFromFile()

    /**
     * Try to get the CSS from the html file and use it in creating the
     * PDF file.  If we find CSS we'll add it to the CSS string
     *
     * @access private
     * @return string Any CSS found
     */
    function _getCSSFromFile()
    {
        if ($this->useCSS) {
            $cssFound = '';
            // first try to find inline styles
/*
            if (preg_match(':<style.*?>(.*?)</style>:is', $this->_htmlString, $matches)) {
                $cssFound = $matches[1];
                // replace it with nothing in the html since it messes up html2ps
                $this->_htmlString = preg_replace(':<style.*?>.*?</style>:is', '', $this->_htmlString);
            }
            elseif (preg_match(':<link .*? href=["\'](.*?)["\'].*?text/css.*?>:i', $this->_htmlString, $matches)) {

                // prepend defaultDomain - additionaly defaultPath, if relative
                // path for css is given
*/
//                $cssFound = preg_replace(':(^(?!/)(?!http\://).*):i', 'http://'.$this->defaultDomain.$this->defaultPath.'\\1', $matches[1]);
//                $cssFound = preg_replace(':(^(/).*):i', 'http://'.$this->defaultDomain.'\\1', $matches[1]);

                $cssFound = preg_replace(':(^(?!/)(?!http\://).*):i', 'http://'.$this->defaultDomain.$this->defaultPath.'xslt/sanserif.css','xslt/sanserif.css');
                $cssFound = preg_replace(':(^(/).*):i', 'http://'.$this->defaultDomain.'xslt/sanserif.css','xslt/sanserif.css');
                $cssFound = implode('', file($cssFound));
//            }

            // only takes a:link attribute
            $cssFound = preg_replace('/a +{/i', 'a:link {', $cssFound);

            // font-size: word causes a crash
            $cssFound = preg_replace('/font-size: *([[:alpha:]-]*);/ie', '$this->_convertFontSize("\\1")', $cssFound);

            return $cssFound;
        }
        else {
            return '';
        }
    }

    // }}}
    // {{{ _convertFontSize()

    /**
     * Converts textual font size to a numberic representation.
     *
     * @param string $in_fontString The font size specification
     *
     * @access private
     * @return string The font-size attribute with size in pt
     */
    function _convertFontSize($in_fontString)
    {
        switch (strtolower($in_fontString)) {
            case 'xx-small':
                $size = 6;
                break;
            case 'x-small':
                $size = 8;
                break;
            case 'small':
                $size = 10;
                break;
            case 'medium':
                $size = 12;
                break;
            case 'large':
                $size = 14;
                break;
            case 'x-large':
                $size = 16;
                break;
            case 'xx-large':
                $size = 18;
                break;
            default:
                $size = 12;
                break;
        }

        return 'font-size: ' . $size . 'pt;';
    }

    // }}}
    // {{{ _getPaperSize()

    /**
     * Tries to determine the specified paper size since ps2pdf needs to be told explicitly
     * in some cases.  Right now handles letter, ledger, 11x17, and legal.
     *
     * @access private
     * @return string The page size string
     */
    function _getPaperSize()
    {
        // :NOTE: We don't support the html2ps paper block since the @page block
        // is the new correct way to do it.
        preg_match('/@page.*?{.*?size:\s*(.*?);/is', $this->html2psrc, $matches);
        if (!isset($matches[1])) {
            $matches[1] = '8.5in 11in';
        }

        // Take out any extra spaces
        $matches[1] = str_replace(' ', '', $matches[1]);
        switch ($matches[1]) {
            case '8.5in14in':
                $size = 'legal';
            break;
            case '11in17in':
                $size = '11x17';
            break;
            case '17in11in':
                $size = 'ledger';
            break;
            case 'a4':
                $size = 'a4';
            break;
            case '8.5in11in':
            default:
                $size = 'letter';
            break;
        }

        return $size;
    }

    // }}}
    // {{{ _getOrientation()

    /**
     * Tries to determine the specified page orientaion since html2ps needs to be told
     * explicitly.
     *
     * @access private
     * @return string The page orientation string
     */

    function _getOrientation()
    {
        preg_match('/@page.*?{.*?orientation:\s*(.*?);/is', $this->html2psrc, $matches);
        if (!isset($matches[1])) {
            $matches[1] = 'portrait';
        }

        switch ($matches[1]) {
            case 'landscape':
                $orientation = '--landscape';
            break;
            default:
                $orientation = '';
            break;
        }

        return $orientation;
    }

    // }}}
    // {{{ _processHeaderFooter()

    /**
     * Process either a set of headers or footers.
     *
     * @param array $in_data The header or footer data
     *
     * @access private
     * @return string The html2ps string of data
     */
    function _processHeaderFooter($in_data)
    {
        $s_data = '';
        // If not using odd/even attributes then override them with the main left/right/center keys
        // to ensure that the desired headers/footers get in
        foreach (array('left', 'right', 'center') as $s_key) {
            if (isset($in_data[$s_key])) {
                if (!isset($in_data["odd-$s_key"])) {
                    $in_data["odd-$s_key"] = $in_data[$s_key];
                }
                if (!isset($in_data["even-$s_key"])) {
                    $in_data["even-$s_key"] = $in_data[$s_key];
                }
            }
        }

        foreach ($in_data as $s_key => $s_val) {
            $s_data .= "  $s_key: \"$s_val\"\n";
        }

        return $s_data;
    }

    // }}}
    // {{{ _cleanup()

    /**
     * Cleans up the files we created during the script.
     *
     * @param array $in_files The array of temporary files
     *
     * @access private
     * @return void
     */
    function _cleanup($in_files)
    {
        foreach ($in_files as $key => $file) {
            if ($this->debug) {
                $this->_dumpDebugInfo("$key file: $file (not removed)");
            }
            else {
                unlink($file);
            }
        }
    }

    // }}}
    // {{{ _dumpDebugInfo()

    /**
     * If debug is on it dumps the specified debug information to screen.  Uses <pre> tags
     * to save formatting of debug information.
     *
     * @param string $in_info The debug info
     *
     * @access public
     * @return void
     */
    function _dumpDebugInfo($in_info)
    {
        if ($this->debug) {
            if ($this->htmlErrors) {
                echo "<pre><span style=\"color: red;\">DEBUG</span>: $in_info</pre>";
            }
            else {
                echo "DEBUG: $in_info\n";
            }
        }
    }

    // }}}
}

// {{{ HTML_ToPDFException

class HTML_ToPDFException extends PEAR_Error {
    var $classname             = 'HTML_ToPDF';
    var $error_message_prepend = 'Error: ';

    function HTML_ToPDFException($message)
    {
        $this->PEAR_Error($message);
    }
}

// }}}
// {{{ is_executable()

if (!function_exists('is_executable')) {
    /**
     * Because is_executable() doesn't exist on windows until php 5.0 we define it as a dummy
     * function here that just runs file_exists.
     *
     * @param string $in_filename The filename to test
     *
     * @access public
     * @return bool If the file exists
     */
    function is_executable($in_filename)
    {
        return file_exists($in_filename);
    }
}

// }}}
