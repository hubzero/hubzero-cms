<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Wiki\Parserdefault\Math;

/**
 * Math Renderer
 * Renders TeX using standard tools
 */
class MathRenderer
{
    public const MATH_PNG = 0;
    public const MATH_SIMPLE = 1;
    public const MATH_HTML = 2;
    public const MATH_SOURCE = 3;
    public const MATH_MODERN = 4;
    public const MATH_MATHML = 5;

    /**
     * Parameters
     *
     * @var  array
     */
    protected $params = array();

    /**
     * Component configuration
     *
     * @var  object
     */
    protected $config;

    /**
     * MD5 hash of TeX input
     *
     * @var  string
     */
    protected $md5 = '';

    /**
     * Operation mode
     *
     * @var  integer
     */
    public $mode = self::MATH_MODERN;

    /**
     * TeX string
     *
     * @var  string
     */
    public $tex = '';

    /**
     * Input hash
     *
     * @var  string
     */
    public $inputhash = '';

    /**
     * Hash
     *
     * @var  string
     */
    public $hash = '';

    /**
     * HTML output
     *
     * @var  string
     */
    public $html = '';

    /**
     * MathML output
     *
     * @var  string
     */
    public $mathml = '';

    /**
     * Conservativeness
     *
     * @var  integer
     */
    public $conservativeness = 0;

    /**
     * Constructor
     *
     * @param      string $tex    LaTeX formula
     * @param      array  $params Parameters (not used?)
     * @return     void
     */
    public function __construct($tex, $params = array())
    {
        $this->tex    = $tex;
        $this->params = $params;
        $this->config = \Hubzero\Facades\Component::params('com_wiki');
    }

    /**
     * Set the output mode (0 - 5)
     *
     * @param      integer $mode Output mode to set
     * @return     void
     */
    public function setOutputMode($mode)
    {
        $this->mode = $mode;
    }

    /**
     * Create directories in a path if they don't exist
     *
     * @param      string  $path Path
     * @param      integer $mode chmod
     * @return     boolean False if errors, True on success
     */
    private function makePath($path, $mode = 0777)
    {
        if (file_exists($path)) {
            return true;
        }
        $path = str_replace('\\', '/', $path);
        $path = str_replace('//', '/', $path);
        $parts = explode('/', $path);

        $n = count($parts);
        if ($n < 1) {
            return mkdir($path, $mode);
        } else {
            $path = '';
            for ($i = 0; $i < $n; $i++) {
                $path .= $parts[$i] . '/';
                if (!file_exists($path)) {
                    if (!mkdir($path, $mode)) {
                        return false;
                    }
                }
            }
            return true;
        }
    }

    /**
     * Render a formula
     * This will return either an image tag with link to image (complicated formulas)
     * or html (simple)
     *
     * @return     string
     */
    public function render()
    {
        $tmpDirectory = PATH_APP . DS . trim($this->config->get('tmppath', '/site/wiki/tmp'), DS);
        $inputEncoding = 'UTF-8';

        // To use inline TeX, you need to compile 'texvc' (in the 'math' subdirectory of
        // the MediaWiki package and have latex, dvips, gs (ghostscript), andconvert
        // (ImageMagick) installed and available in the PATH.
        // Please see math/README for more information.

        // Location of the texvc binary
        $b = '/usr/bin'; // dirname(__FILE__);
        $texvc = $b . DS . 'texvc';

        if ($this->mode == self::MATH_SOURCE) {
            // No need to render or parse anything more!
            return ('$ ' . htmlspecialchars($this->tex) . ' $');
        }
        if ($this->tex == '') {
            return;
        }

        if (!$this->recall()) {
            // Ensure that the temp and output directories are available before continuing...
            if (!file_exists($tmpDirectory)) {
                if (!$this->makePath($tmpDirectory)) {
                    return $this->error('math_bad_tmpdir');
                }
            } elseif (!is_dir($tmpDirectory) || !is_writable($tmpDirectory)) {
                return $this->error('math_bad_tmpdir');
            }
            // Ensure we have the texvc executable
            if (function_exists('is_executable') && !is_executable($texvc)) {
                return $this->error('math_notexvc');
            }
            $cmd = $texvc . ' ' .
                    escapeshellarg($tmpDirectory) . ' ' .
                    escapeshellarg($tmpDirectory) . ' ' .
                    escapeshellarg($this->tex) . ' ' .
                    escapeshellarg($inputEncoding);

            //echo("TeX: $cmd\n");
            $contents = `$cmd`;
            //echo("TeX output:\n $contents\n---\n");

            /*
                Status codes and HTML/MathML transformations are returned on stdout.
                A rasterized PNG file will be written to the output directory, named
                for the MD5 hash code.

                texvc output format is like this:
                    +%5     ok, but not html or mathml
                    c%5%h   ok, conservative html, no mathml
                    m%5%h   ok, moderate html, no mathml
                    l%5%h   ok, liberal html, no mathml
                    C%5%h\0%m   ok, conservative html, with mathml
                    M%5%h\0%m   ok, moderate html, with mathml
                    L%5%h\0%m   ok, liberal html, with mathml
                    X%5%m   ok, no html, with mathml
                    S       syntax error
                    E       lexing error
                    F%s     unknown function %s
                    -       other error

                 \0 - null character
                 %5 - md5, 32 hex characters
                 %h - html code, without \0 characters
                 %m - mathml code, without \0 characters
            */

            if (strlen($contents) == 0) {
                return $this->error('math_unknown_error1');
            }

            $retval = substr($contents, 0, 1);
            $errmsg = '';
            if (($retval == 'C') || ($retval == 'M') || ($retval == 'L')) {
                if ($retval == 'C') {
                    $this->conservativeness = 2;
                } elseif ($retval == 'M') {
                    $this->conservativeness = 1;
                } else {
                    $this->conservativeness = 0;
                }
                $outdata = substr($contents, 33);

                $i = strpos($outdata, "\000");

                $this->html = substr($outdata, 0, $i);
                $this->mathml = substr($outdata, $i + 1);
            } elseif (($retval == 'c') || ($retval == 'm') || ($retval == 'l')) {
                $this->html = substr($contents, 33);
                if ($retval == 'c') {
                    $this->conservativeness = 2;
                } elseif ($retval == 'm') {
                    $this->conservativeness = 1;
                } else {
                    $this->conservativeness = 0;
                }
                $this->mathml = null;
            } elseif ($retval == 'X') {
                //$this->html = null;
                $this->mathml = substr($contents, 33);
                $this->conservativeness = 0;
            } elseif ($retval == '+') {
                //$this->html = null;
                //$this->mathml = null;
                $this->conservativeness = 0;
            } else {
                $errbit = htmlspecialchars(substr($contents, 1));
                switch ($retval) {
                    case 'E':
                        $errmsg = $this->error('math_lexing_error', $errbit);
                        break;
                    case 'S':
                        $errmsg = $this->error('math_syntax_error', $errbit);
                        break;
                    case 'F':
                        $errmsg = $this->error('math_unknown_function', $errbit);
                        break;
                    default:
                        $errmsg = $this->error('math_unknown_error2', $errbit);
                }
            }

            if (!$errmsg) {
                 $this->hash = substr($contents, 1, 32);
            }

            if ($errmsg) {
                return $errmsg;
            }

            if (!preg_match("/^[a-f0-9]{32}$/", $this->hash)) {
                return $this->error('math_unknown_error3');
            }

            if (!file_exists("$tmpDirectory/{$this->hash}.png")) {
                return $this->error('math_image_error');
            }

            $hashpath = $this->getHashPath();

            if (!file_exists($hashpath)) {
                if (!$this->makePath($hashpath)) {
                    return $this->error('math_bad_output');
                }
            } elseif (!is_dir($hashpath) || !is_writable($hashpath)) {
                return $this->error('math_bad_output');
            }

            if (!rename("$tmpDirectory/{$this->hash}.png", "$hashpath/{$this->hash}.png")) {
                return $this->error('math_output_error');
            }

            // Now save it back to the DB:
            $outmd5_sql = $this->hash; //pack('H32', $this->hash);
            $md5_sql    = $this->md5; //pack('H32', $this->md5); // Binary packed, not hex

            $wm = \Components\Wiki\Models\Formula::oneByInputhash($md5_sql);
            if (!$wm->get('id')) {
                $wm->set('inputhash', $this->encodeBlob($md5_sql));
                $wm->set('outputhash', $this->encodeBlob($outmd5_sql));
                $wm->set('conservativeness', (int)$this->conservativeness);
                $wm->set('html', (string)$this->html);
                $wm->set('mathml', (string)$this->mathml);

                if (!$wm->save()) {
                    return $wm->getError();
                }
            }
        }

        return $this->doRender();
    }

    /**
     * Return an error message
     *
     * @param      string $msg    Message
     * @param      string $append Data to append
     * @return     string HTML
     */
    private function error($msg, $append = '')
    {
        $mf = htmlspecialchars('math_failure');
        $errmsg = htmlspecialchars($msg);
        $source = htmlspecialchars(str_replace("\n", ' ', $this->tex));
        return '<p class="error">' . $mf . ' (' . $errmsg . $append . '): ' . $source . '</p>' . "\n";
    }

    /**
     * Detect if a formula exists
     *
     * @return     boolean True if image exists
     */
    private function recall()
    {
        $this->md5 = md5($this->tex);

        $wm = \Components\Wiki\Models\Formula::oneByInputhash($this->encodeBlob($this->md5));

        if ($wm->get('id')) {
            // Tailing 0x20s can get dropped by the database, add it back on if necessary:
            // Tailing 0x20s can get dropped by the database, add it back on if necessary:
            //$xhash = $wm->outputhash; //$this->decodeBlob($wm->outputhash);
            //unpack('H32md5', $this->decodeBlob($wm->outputhash) . "                ");
            $this->hash = $wm->get('outputhash');

            $this->conservativeness = $wm->get('conservativeness');
            $this->html   = $wm->get('html');
            $this->mathml = $wm->get('mathml');

            if (file_exists($this->getHashPath() . DS . "{$this->hash}.png")) {
                return true;
            }
        }

        // Missing from the database and/or the render cache
        return false;
    }

    /**
     * Select among PNG, HTML, or MathML output depending on
     *
     * @return     string
     */
    private function doRender()
    {
        if ($this->mode == self::MATH_MATHML && $this->mathml != '') {
            return '<math xmlns="http://www.w3.org/1998/Math/MathML">' . $this->mathml . '</math>';
        }
        if (
            ($this->mode == self::MATH_PNG) || ($this->html == '')
            || (($this->mode == self::MATH_SIMPLE) && ($this->conservativeness != 2))
            || (($this->mode == self::MATH_MODERN || $this->mode == self::MATH_MATHML)
                && ($this->conservativeness == 0))
        ) {
            return $this->linkToMathImage();
        } else {
            return '<span class="texhtml">' . $this->html . '</span>';
        }
    }

    /**
     * Generate an image tag for displaying rendered formulas
     *
     * @return     string HTML
     */
    private function linkToMathImage()
    {
        $url = DS
            . 'app'
            . DS
            . trim($this->config->get('mathpath', '/site/wiki/math'), DS)
            . DS
            . substr($this->hash, 0, 1)
            . DS
            . substr($this->hash, 1, 1)
            . DS
            . substr($this->hash, 2, 1)
            . DS
            . "{$this->hash}
            . png";

        return '<img src="' . $url . '" class="tex" alt="' . $this->tex . '" />';
    }

    /**
     * Get the hash path
     *
     * @return     string
     */
    private function getHashPath()
    {
        $path = PATH_APP . DS . trim(
            $this->config->get('mathpath', '/site/wiki/math'),
            DS
        ) . DS . substr(
            $this->hash,
            0,
            1
        ) . DS . substr(
            $this->hash,
            1,
            1
        ) . DS . substr(
            $this->hash,
            2,
            1
        );
        return $path;
    }

    /**
     * Encode blob
     *
     * @param      string $b Blob to encode
     * @return     string
     */
    private function encodeBlob($b)
    {
        return $b;
    }

    /**
     * Decode blob
     *
     * @param      string $b Blob to decode
     * @return     string
     */
    private function decodeBlob($b)
    {
        return $b;
    }

    /**
     * Check of a formula exists, rendering if not
     * This will return either an image tag with link to image (complicated formulas)
     * or html (simple)
     *
     * @param      string $tex    LaTeX formula
     * @param      array  $params Parameters (not used?)
     * @return     string
     */
    public static function renderMath($tex, $params = array())
    {
        $math = new self($tex, $params);
        return $math->render();
    }
}
