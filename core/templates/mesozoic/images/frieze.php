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
 * Takes its placement as a row - name, x, scale, opacity, and optionally a
 * flip and a line of its own - so that the placements can be written as a
 * table and read down a column. Which is the whole job: a band is arranged by
 * comparing one shape's numbers against its neighbours'.
 *
 * @param   array  $shape   name, x, scale, opacity, [flip], [y]
 * @param   float  $ground  The line the shapes stand on
 * @return  string
 */
function put(array $shape, $ground)
{
    list($name, $x, $scale, $opacity) = $shape;

    $flip = isset($shape[4]) ? $shape[4] : false;
    $y    = isset($shape[5]) ? $shape[5] : null;

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
 * @param   float   $ground  The line the shapes stand on
 * @param   array   $shapes  What is on it, a row each
 * @return  void
 */
function band($file, $width, $height, $ground, array $shapes)
{
    $drawn = array();

    foreach ($shapes as $shape) {
        $drawn[] = put($shape, $ground);
    }

    file_put_contents(
        __DIR__ . '/' . $file,
        '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' ' . $height . '"'
        . ' preserveAspectRatio="xMidYMax slice" role="presentation">' . "\n"
        . '  <g fill="' . INK . '">' . "\n    "
        . implode("\n    ", $drawn) . "\n"
        . '  </g>' . "\n</svg>\n"
    );

    echo sprintf("  %-14s %4dKB\n", $file, round(filesize(__DIR__ . '/' . $file) / 1024));
}

// The footer: a horizon with animals on it and plants among them. No ground
// under them - a filled horizon reads as a shelf at this height, and the
// footer's own edge is a better line to stand on.
band('frieze.svg', 1600, 150, 126, array(
    // name            x   scale  opacity  flip   own line
    array('fern',            10,  0.30,    0.11,  true),
    array('stegosaur',       84,  0.34,    0.16),
    array('horsetail',      196,  0.26,    0.10),
    array('pterosaur',      248,  0.26,    0.11,  true,  18),
    array('ankylosaur',     332,  0.31,    0.14,  true),
    array('cycad',          452,  0.30,    0.12),
    array('sauropod',       566,  0.40,    0.18),
    array('conifer',        722,  0.40,    0.11),
    array('hadrosaur',      826,  0.33,    0.15,  true),
    array('pterosaur',      984,  0.22,    0.10,  false, 30),
    array('ceratopsian',   1064,  0.32,    0.16),
    array('fern',          1204,  0.26,    0.10),
    array('raptor',        1276,  0.28,    0.13,  true),
    array('cycad',         1372,  0.24,    0.11),
    array('theropod',      1436,  0.36,    0.17),
));

// The header: plants only, and fainter. The navigation sits over this, so
// nothing here may compete with it - no animal, because an animal is a shape
// the eye goes to.
//
// A short tile rather than a tall one: the band is scaled to its own height,
// so a 96-tall drawing in a 56px band renders everything at little more than
// half size and the plants come out as specks.
band('canopy.svg', 1600, 64, 60, array(
    // name            x   scale  opacity  flip
    array('fern',            16,  0.36,    0.10,  true),
    array('horsetail',      150,  0.32,    0.09),
    array('conifer',        272,  0.42,    0.09),
    array('fern',           412,  0.30,    0.10),
    array('cycad',          548,  0.32,    0.09),
    array('horsetail',      690,  0.36,    0.09,  true),
    array('fern',           818,  0.32,    0.10),
    array('conifer',        948,  0.38,    0.09),
    array('cycad',         1086,  0.30,    0.09,  true),
    array('fern',          1216,  0.34,    0.10,  true),
    array('horsetail',     1348,  0.32,    0.09),
    array('conifer',       1470,  0.40,    0.09),
));
