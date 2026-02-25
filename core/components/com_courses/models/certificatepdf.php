<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models;

class CertificatePdf extends \TCPDF
{
    /**
     * Background image file path
     *
     * @var  string
     */
    public $img_file;

    //Page header
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function Header()
    {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        list($width, $height) = getimagesize($this->img_file);
        $imgWidth = (($width / 300) * 25.4);
        $imgHeight = (($height / 300) * 25.4);
        $this->Image(
            $this->img_file,
            0,
            0,
            $imgWidth,
            $imgHeight,
            '',
            '',
            '',
            false,
            300,
            '',
            false,
            false,
            0
        );
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}
