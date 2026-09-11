<?php

/**
 * The two bands the template puts at the top and bottom of a page.
 *
 * Drawn from the same shapes the sample data pack uses, and saved as template
 * assets, because a template has to work on a hub that has never run a pack.
 * Run it again after changing an animal or a plant:
 *
 *     php core/templates/mesozoic/images/frieze.php
 *
 * Both bands repeat across the page, so nothing may cross the edge of the box
 * and anything standing on the ground has to stand on the same line at both
 * ends, or every tile shows its seam.
 */

require getenv('HUBZERO_SAMPLEDATA')
    ? getenv('HUBZERO_SAMPLEDATA') . '/Mesozoic/Silhouettes.php'
    : '/home/su-nkisseberth/hubzero-mesozoic/Mesozoic/Silhouettes.php';

use Hubzero\Sampledata\Packs\Mesozoic\Silhouettes;

const INK = '#5C4A3A';

/**
 * One shape, placed on a band
 *
 * @param   string  $name     Which animal or plant
 * @param   float   $x        Where its box starts
 * @param   float   $scale    How large
 * @param   float   $opacity  How near it looks
 * @param   float   $ground   The line it stands on
 * @param   bool    $flip     Facing the other way
 * @param   float   $y        Its own line, for anything not on the ground
 * @return  string
 */
function put($name, $x, $scale, $opacity, $ground, $flip = false, $y = null)
{
    $top = ($y === null) ? $ground - (Silhouettes::GROUND * $scale) : $y;
    $sx  = $flip ? -$scale : $scale;
    $ox  = $flip ? $x + (Silhouettes::WIDTH * $scale) : $x;

    return sprintf(
        '<g opacity="%.2f" transform="translate(%.1f %.1f) scale(%.3f %.3f)">%s</g>',
        $opacity,
        $ox,
        $top,
        $sx,
        $scale,
        Silhouettes::draw($name)
    );
}

/**
 * Write one band out
 *
 * @param   string  $file    What to call it
 * @param   int     $width   How wide the tile is
 * @param   int     $height  How tall
 * @param   array   $shapes  What is on it
 * @return  void
 */
function band($file, $width, $height, array $shapes)
{
    file_put_contents(
        __DIR__ . '/' . $file,
        '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' ' . $height . '"'
        . ' preserveAspectRatio="xMidYMax slice" role="presentation">' . "\n"
        . '  <g fill="' . INK . '">' . "\n    "
        . implode("\n    ", $shapes) . "\n"
        . '  </g>' . "\n</svg>\n"
    );

    echo sprintf("  %-14s %4dKB\n", $file, round(filesize(__DIR__ . '/' . $file) / 1024));
}

// The footer: a horizon with animals on it and plants among them. No ground
// under them - a filled horizon reads as a shelf at this height, and the
// footer's own edge is a better line to stand on.
band('frieze.svg', 1600, 150, [
    put('fern',            10, 0.30, 0.11, 126, true),
    put('stegosaur',       84, 0.34, 0.16, 126),
    put('horsetail',      196, 0.26, 0.10, 126),
    put('pterosaur',      248, 0.26, 0.11, 126, true, 18),
    put('ankylosaur',     332, 0.31, 0.14, 126, true),
    put('cycad',          452, 0.30, 0.12, 126),
    put('sauropod',       566, 0.40, 0.18, 126),
    put('conifer',        722, 0.40, 0.11, 126),
    put('hadrosaur',      826, 0.33, 0.15, 126, true),
    put('pterosaur',      984, 0.22, 0.10, 126, false, 30),
    put('ceratopsian',   1064, 0.32, 0.16, 126),
    put('fern',          1204, 0.26, 0.10, 126),
    put('raptor',        1276, 0.28, 0.13, 126, true),
    put('cycad',         1372, 0.24, 0.11, 126),
    put('theropod',      1436, 0.36, 0.17, 126),
]);

// The header: plants only, and fainter. The navigation sits over this, so
// nothing here may compete with it - no animal, because an animal is a shape
// the eye goes to.
//
// A short tile rather than a tall one: the band is scaled to its own height,
// so a 96-tall drawing in a 56px band renders everything at little more than
// half size and the plants come out as specks.
band('canopy.svg', 1600, 64, [
    put('fern',            16, 0.36, 0.10, 60, true),
    put('horsetail',      150, 0.32, 0.09, 60),
    put('conifer',        272, 0.42, 0.09, 60),
    put('fern',           412, 0.30, 0.10, 60),
    put('cycad',          548, 0.32, 0.09, 60),
    put('horsetail',      690, 0.36, 0.09, 60, true),
    put('fern',           818, 0.32, 0.10, 60),
    put('conifer',        948, 0.38, 0.09, 60),
    put('cycad',         1086, 0.30, 0.09, 60, true),
    put('fern',          1216, 0.34, 0.10, 60, true),
    put('horsetail',     1348, 0.32, 0.09, 60),
    put('conifer',       1470, 0.40, 0.09, 60),
]);
