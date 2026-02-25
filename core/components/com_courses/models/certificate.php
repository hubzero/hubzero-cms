<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

use Hubzero\Database\Table;
use ImagickException;
use Imagick;
use Hubzero\Facades\Lang;

/**
 * Courses model class for a certificate
 */
class Certificate extends Base
{
    /**
     * Table class name
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_tbl_name = '\\Components\\Courses\\Tables\\Certificate';

    /**
     * Object scope
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_scope = 'certificate';

    /**
     * Properties object
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_properties = null;

    /**
     * Base file path
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_base = null;

    /**
     * Constructor
     *
     * @param   mixed $oid Integer (ID), string (alias), object or array
     * @return  void
     */
    public function __construct($oid = null, $course_id = null)
    {
        $this->_db = \Hubzero\Facades\App::get('db');

        if ($this->_tbl_name) {
            $cls = $this->_tbl_name;
            $this->_tbl = new $cls($this->_db);

            if (!($this->_tbl instanceof Table)) {
                $errorMsg = Lang::txt(
                    'Table class must be an instance of Hubzero\\Database\\Table.'
                );
                $logMsg = __CLASS__ . '::' . __FUNCTION__ . '(); ' . $errorMsg;
                $this->_logError($logMsg);
                throw new \LogicException($errorMsg);
            }

            if ($course_id) {
                $this->_tbl->load(array('course_id' => $course_id));
            } elseif (is_numeric($oid) || is_string($oid)) {
                // Make sure $oid isn't empty
                // This saves a database call
                if ($oid) {
                    $this->_tbl->load($oid);
                }
            } elseif (is_object($oid) || is_array($oid)) {
                $this->bind($oid);
            }
        }
    }

    /**
     * Returns a reference to a certificate model
     *
     * @param   mixed  $oid ID (int) or alias (string)
     * @return  object \Components\Courses\Models\Certificate
     */
    public static function &getInstance($oid = 0, $course_id = 0)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = array();
        }

        if (!isset($instances[$oid . $course_id])) {
            $instances[$oid . $course_id] = new self($oid, $course_id);
        }

        return $instances[$oid . $course_id];
    }

    /**
     * Check if the certificate has the needed file
     *
     * @return  boolean
     */
    public function hasFile()
    {
        $path = $this->path('system');

        if (file_exists($path)) {
            $filename = $this->get('filename', 'certificate.pdf');
            if (file_exists($path . DS . $filename)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create images
     *
     * @return void
     */
    public function renderPageImages()
    {
        if (!class_exists('Imagick')) {
            // nothing
            $this->setError(Lang::txt('Imagick extension required.'));
            return false;
        }

        try {
            if (!$this->exists()) {
                $msg = Lang::txt('No pages exist for nonexistent certificate.');
                $this->setError($msg);
                return false;
            }

            $base = $this->path('system');

            if (!file_exists($base)) {
                if (!\Hubzero\Facades\Filesystem::makeDirectory($base)) {
                    $this->setError(Lang::txt('Unable to create directory.'));
                    return false;
                }
            }

            $fname = $base . DS . $this->get('filename', 'certificate.pdf');

            // Get the number of images for our for-loop
            $im = new Imagick($fname);
            $num = $im->getNumberImages();

            // Now actually do the image creation and cropping based on min margin
            for ($pages = 0; $pages < $num; ++$pages) {
                $im = new Imagick();
                $im->setResolution(300, 300);
                $im->readImage($fname . '[' . ($pages) . ']');
                $im->setImageFormat('png');
                $im->setImageUnits(Imagick::RESOLUTION_PIXELSPERINCH);

                $im->writeImage($base . DS . ($pages + 1) . '.png');
            }

            return true;
        } catch (ImagickException $ex) {
            // nothing
            $this->setError($ex->getMessage());
            return false;
        }
    }

    /**
     * Process the PDF pages to images
     *
     * @param   object $fun Closure
     * @return  void
     */
    public function eachPage($fun)
    {
        if (!$this->exists()) {
            \Hubzero\Facades\App::abort(422, 'No pages exist for nonexistent certificate.');
            return;
        }

        $base   = $this->path('system');
        $dir    = opendir($base);
        $images = array();

        while ($file = readdir($dir)) {
            if (preg_match('/^\d+[.]png$/', $file)) {
                $images[] = $file;
            }
        }

        closedir($dir);
        natsort($images);

        $base  = $this->path('web');
        $sessionId = \Hubzero\Facades\App::get('session')->getId();
        $secret = \Hubzero\Facades\Config::get('secret');
        $token = hash('sha256', $sessionId . ':' . $secret);

        $idx = 0;
        foreach ($images as $img) {
            $path = $base . DS . $img . '?token=' . $token;

            $fun($path, ++$idx);
        }
    }

    /**
     * Build the path to the certificate
     *
     * @param   string $type Path type to return
     * @return  string
     */
    public function path($type = '')
    {
        if (!$this->_base) {
            $path = $this->config('uploadpath', '/site/courses');
            $uploadPath = DS . trim($path, DS);
            $this->_base = $uploadPath . DS . $this->get('course_id')
                . DS . 'certificates' . DS . $this->get('id');
        }

        switch ($type) {
            case 'sys':
            case 'system':
            case 'upload':
                return PATH_APP . $this->_base;
            break;

            case 'web':
                $reqBase = trim(\Hubzero\Facades\Request::base(true), '/');
                $base = str_replace('administrator', '', $reqBase);
                $appPath = substr(PATH_APP, strlen(PATH_ROOT));
                return rtrim($base, '/') . $appPath . $this->_base;
            break;

            default:
                return $this->_base;
            break;
        }
    }

    /**
     * Get entry properties
     *
     * Properties are stored as en encoded string. This retrieves the
     * string and decodes it or creates an object with base values if
     * no stored value is found.
     *
     * @return  object
     */
    public function properties()
    {
        if (!$this->_properties) {
            $default = '{width:900,height:694,elements:[]}';
            $props = $this->get('properties', $default);
            $this->_properties = json_decode($props);
            if (!$this->_properties) {
                $this->_properties = new \stdClass();
                $this->_properties->width    = 900;
                $this->_properties->height   = 694;
                $this->_properties->elements = array();
            }
        }
        return $this->_properties;
    }

    /**
     * Render a certificate
     *
     * @param   object  $user  User
     * @param   string  $path  Path to store rendered file to
     * @return  boolean True on success, false on error
     */
    public function render($user = null, $path = null)
    {
        if (!$user) {
            $user = \Hubzero\Facades\User::getInstance();
        }

        if (!class_exists('\Components\Courses\Models\Course')) {
            require_once __DIR__ . DS . 'course.php';
        }
        $course = Course::getInstance($this->get('course_id'));

        require_once __DIR__ . DS . 'certificatepdf.php';

        $img = $this->path('system') . '/1.png';

        list($width, $height) = getimagesize($img);

        // 300 dots per inch
        // 1 inch = 25.4mm
        $w = ($width / 300);
        $h = ($height / 300);
        $mm = 25.4;
        $size = array(
            'width' => ($w * $mm),
            'height' => ($h * $mm)
        );

        $pdf = new CertificatePdf(
            PDF_PAGE_ORIENTATION,
            PDF_UNIT,
            $size,
            true,
            'UTF-8',
            false
        );
        $pdf->img_file = $img;

        $pdf->AddPage('L', array($size['height'], $size['width']));

        $pdf->SetFillColor(0, 0, 0);

        foreach ($this->properties()->elements as $element) {
            // Convert pixel values to percents
            $element->x = $element->x / $this->properties()->width;
            $element->y = $element->y / $this->properties()->height;

            $element->w = $element->w / $this->properties()->width;
            $element->h = $element->h / $this->properties()->height;

            $val = '';
            switch ($element->id) {
                case 'name':
                case 'email':
                case 'username':
                    $val = $user->get($element->id);
                    break;

                case 'course':
                    $val = $course->get('title');
                    break;

                case 'offering':
                    $val = $course->offering()->get('title');
                    break;

                case 'section':
                    $val = $course->offering()->section()->get('title');
                    break;

                case 'date':
                    $val = \Hubzero\Facades\Date::of('now')->format(Lang::txt('d M Y'));
                    break;
            }

            $pdf->SetFont('Helvetica', '', 30);

            $posX = $element->x * $size['width'];
            $posY = $element->y * $size['height'];
            $pdf->SetXY($posX, $posY);
            $cellW = $element->w * $size['width'];
            $cellH = $element->h * $size['height'];
            $pdf->Cell($cellW, $cellH, $val, '', 1, 'C');
        }

        if (!$path) {
            $pdf->Output();
            die;
        }

        $pdf->Output($path, 'F');

        return true;
    }

    /**
     * Store record in the database
     *
     * @param   boolean $check Perform data validation?
     * @return  boolean
     */
    public function store($check = true)
    {
        if (is_object($this->get('properties'))) {
            $this->set('properties', json_encode($this->get('properties')));
        }

        return parent::store($check);
    }

    /**
     * Delete a certificate
     *
     * @return  boolean True on success, false on error
     */
    public function delete()
    {
        // Remove files
        $path = $this->path('system');
        if (is_dir($path)) {
            // Attempt to delete the file
            if (!\Hubzero\Facades\Filesystem::deleteDirectory($path)) {
                $msg = Lang::txt(
                    'Unable to remove upload directory and files for certificate.'
                );
                $this->setError($msg);
                return false;
            }
        }

        // Remove this record from the database and log the event
        return parent::delete();
    }
}
