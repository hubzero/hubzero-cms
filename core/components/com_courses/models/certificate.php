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
use Hubzero\Facades\App;
use Hubzero\Facades\Config;
use Hubzero\Facades\Date;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

/**
 * Courses model class for a certificate
 */
class Certificate extends Base
{
    /**
     * Table class name
     *
     * @var class-string
     */
    protected $tableClass = '\\Components\\Courses\\Tables\\Certificate';

    /**
     * Table, an instance of $tableClass
     *
     * @var \Components\Courses\Tables\Certificate
     */
    protected $table;

    /**
     * Object scope
     *
     * @var string
     */
    protected $scope = 'certificate';

    /**
     * Properties object
     *
     * @var \stdClass|null
     */
    protected $properties = null;

    /**
     * Base file path
     *
     * @var string
     */
    protected $base;

    /**
     * Constructor
     *
     * @param   mixed $oid Integer (ID), string (alias), object or array
     * @param   mixed  $course_id
     * @return  void
     */
    public function __construct($oid = null, $course_id = null)
    {
        $this->db = App::get('db');

        if ($this->tableClass) {
            $cls = $this->tableClass;
            $table = new $cls($this->db);

            if (!($table instanceof \Components\Courses\Tables\Certificate)) {
                $errorMsg = Lang::txt(
                    'Table class must be an instance of Components\\Courses\\Tables\\Certificate.',
                );
                $logMsg = __CLASS__ . '::' . __FUNCTION__ . '(); ' . $errorMsg;
                $this->logError($logMsg);
                throw new \LogicException($errorMsg);
            }
            $this->table = $table;

            if ($course_id) {
                $this->table->load(['course_id' => $course_id]);
            } elseif (is_numeric($oid) || is_string($oid)) {
                // Make sure $oid isn't empty
                // This saves a database call
                if ($oid) {
                    $this->table->load($oid);
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
     * @param   mixed  $course_id
     * @return  self \Components\Courses\Models\Certificate
     */
    public static function &getInstance($oid = 0, $course_id = 0)
    {
        static $instances;

        if (!isset($instances)) {
            $instances = [];
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
     * @return bool
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
                if (!Filesystem::makeDirectory($base)) {
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
     * @param   \Closure $fun Called with each page image's path and number
     * @return  void
     */
    public function eachPage($fun)
    {
        if (!$this->exists()) {
            App::abort(422, 'No pages exist for nonexistent certificate.');
        }

        $base   = $this->path('system');
        $dir    = opendir($base);
        // A directory that cannot be read lists nothing
        $images = [];

        while ($dir !== false && $file = readdir($dir)) {
            if (preg_match('/^\d+[.]png$/', $file)) {
                $images[] = $file;
            }
        }

        if ($dir !== false) {
            closedir($dir);
        }
        natsort($images);

        $base  = $this->path('web');
        $sessionId = App::get('session')->getId();
        $secret = Config::get('secret');
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
        if (!$this->base) {
            $path = $this->config('uploadpath', '/site/courses');
            $uploadPath = DS . trim($path, DS);
            $this->base = $uploadPath . DS . $this->get('course_id')
                . DS . 'certificates' . DS . $this->get('id');
        }

        switch ($type) {
            case 'sys':
            case 'system':
            case 'upload':
                return PATH_APP . $this->base;

            case 'web':
                $reqBase = trim(Request::base(true), '/');
                $base = str_replace('administrator', '', $reqBase);
                $appPath = substr(PATH_APP, strlen(PATH_ROOT));
                return rtrim($base, '/') . $appPath . $this->base;

            default:
                return $this->base;
        }
    }

    /**
     * Get entry properties
     *
     * Properties are stored as en encoded string. This retrieves the
     * string and decodes it or creates an object with base values if
     * no stored value is found.
     *
     * @return  \stdClass
     */
    public function properties()
    {
        if (!$this->properties) {
            $default = '{width:900,height:694,elements:[]}';
            $props = $this->get('properties', $default);
            $this->properties = json_decode($props);
            if (!$this->properties) {
                $this->properties = new \stdClass();
                $this->properties->width    = 900;
                $this->properties->height   = 694;
                $this->properties->elements = [];
            }
        }
        return $this->properties;
    }

    /**
     * Render a certificate
     *
     * @param   \Hubzero\User\User|null  $user  User
     * @param   string|null  $path  Path to store rendered file to
     * @return  boolean True on success, false on error
     */
    public function render($user = null, $path = null)
    {
        if (!$user) {
            $user = User::getInstance();
        }

        if (!class_exists('\Components\Courses\Models\Course')) {
        }
        $course = Course::getInstance($this->get('course_id'));

        $img = $this->path('system') . '/1.png';

        list($width, $height) = getimagesize($img) ?: [null, null];

        // 300 dots per inch
        // 1 inch = 25.4mm
        $w = ($width / 300);
        $h = ($height / 300);
        $mm = 25.4;
        $size = [
            'width' => ($w * $mm),
            'height' => ($h * $mm),
        ];

        $pdf = new CertificatePdf(
            PDF_PAGE_ORIENTATION,
            PDF_UNIT,
            $size,
            true,
            'UTF-8',
            false,
        );
        $pdf->img_file = $img;

        $pdf->AddPage('L', [$size['height'], $size['width']]);

        $pdf->SetFillColor(0, 0, 0);

        foreach ($this->properties()->elements as $element) {
            // Convert pixel values to percents
            $element->x /= $this->properties()->width;
            $element->y /= $this->properties()->height;

            $element->w /= $this->properties()->width;
            $element->h /= $this->properties()->height;

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
                    $val = Date::of('now')->format(Lang::txt('d M Y'));
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
            die();
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
            if (!Filesystem::deleteDirectory($path)) {
                $msg = Lang::txt(
                    'Unable to remove upload directory and files for certificate.',
                );
                $this->setError($msg);
                return false;
            }
        }

        // Remove this record from the database and log the event
        return parent::delete();
    }
}
