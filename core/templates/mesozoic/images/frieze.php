<?php

/**
 * The three bands the template puts behind a page.
 *
 * Drawn from the same shapes the sample data pack uses, and saved as template
 * assets, because a template has to work on a hub that has never run a pack.
 * Run it again after changing an animal or a plant:
 *
 *     php core/templates/mesozoic/images/frieze.php
 *
 * The head and foot bands repeat across the page, so nothing may cross the
 * edge of the box and anything standing on the ground has to stand on the
 * same line at both ends, or every tile shows its seam. The one behind a
 * title card does not repeat and is free of that.
 *
 * How faint each band is decided by what has to be read through it: see
 * tools/screenshots/veneer.mjs, which composites the band over the ground it
 * sits on and reports the worst contrast a reader actually gets, overlapping
 * shapes included.
 */

require getenv('HUBZERO_SAMPLEDATA')
    ? getenv('HUBZERO_SAMPLEDATA') . '/Mesozoic/Silhouettes.php'
    : '/home/su-nkisseberth/hubzero-mesozoic/Mesozoic/Silhouettes.php';

use Hubzero\Sampledata\Packs\Mesozoic\Silhouettes;

// The ink. Plants are a shade toward green and everything else is the brown
// of the rock - which at these opacities is a difference of two or three
// values per channel, so it registers as a change of temperature between
// neighbours rather than as a colour anybody would name.
const INK   = '#5C4A3A';
const GREEN = '#4A5A30';

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

    $ink = in_array($name, Silhouettes::plants(), true) ? GREEN : INK;

    $flip = isset($shape[4]) ? $shape[4] : false;
    $y    = isset($shape[5]) ? $shape[5] : null;

    $top = ($y === null) ? $ground - (Silhouettes::GROUND * $scale) : $y;
    $sx  = $flip ? -$scale : $scale;
    $ox  = $flip ? $x + (Silhouettes::WIDTH * $scale) : $x;

    return sprintf(
        '<g fill="%s" opacity="%.2f" transform="translate(%.1f %.1f) scale(%.3f %.3f)">%s</g>',
        $ink,
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
        . ' preserveAspectRatio="xMidYMax slice" role="presentation">' . "\n  "
        . implode("\n  ", $drawn) . "\n"
        . '</svg>' . "\n"
    );

    echo sprintf("  %-14s %4dKB\n", $file, round(filesize(__DIR__ . '/' . $file) / 1024));
}

// The footer: a few large shapes standing along the bottom of it, behind the
// footer's own text. Large rather than many, because the footer is 500px tall
// and a thin strip of small animals across the top of it read as a rule with
// decoration on it rather than as a place.
//
// Faint enough to read through: the opacities here are what veneer.mjs says
// the footer's text can stand, overlaps included, and not a value more.
band('frieze.svg', 1600, 260, 236, array(
    // name            x   scale  opacity  flip   own line
    array('fern',            10,  0.95,    0.07,  true),
    array('sauropod',       230,  1.25,    0.06),
    array('egg',            470,  0.50,    0.08),
    array('conifer',        560,  1.30,    0.06),
    array('theropod',       830,  1.05,    0.06,  true),
    array('footprint',     1050,  0.38,    0.08),
    array('footprint',     1098,  0.38,    0.08),
    array('footprint',     1146,  0.38,    0.08),
    array('cycad',         1180,  0.95,    0.07),
    array('ceratopsian',   1330,  1.00,    0.06),
    array('horsetail',     1460,  0.55,    0.07),
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

// Behind a page's title card: a band across it rather than a cluster at one
// end, and drawn large enough to be seen through the title rather than only
// beside it. One animal among them - the card is wide and mostly empty, and
// at this weight an animal is an incident rather than a distraction.
//
// This one repeats like the others, so the same rule applies: nothing may
// cross the edge of the box.
band('thicket.svg', 1200, 150, 140, array(
    // name            x   scale  opacity  flip   own line
    array('fern',            20,  0.85,    0.06,  true),
    array('egg',            250,  0.55,    0.07),
    array('cycad',          330,  0.90,    0.06),
    array('hadrosaur',      430,  0.75,    0.05,  true),
    array('footprint',      600,  0.42,    0.07),
    array('footprint',      646,  0.42,    0.07),
    array('footprint',      692,  0.42,    0.07),
    array('conifer',        760,  1.05,    0.06),
    array('horsetail',     1000,  0.80,    0.06),
));
