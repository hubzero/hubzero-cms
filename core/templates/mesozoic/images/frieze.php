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

// The map is drawn in the brand's own colour: it is a diagram rather than a
// thing seen, and the page already reads that colour as the hub's own.
const MAPINK = '#8A5A2B';

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



/**
 * The world the Calder Basin sat in
 *
 * A drawing, not a reconstruction. Everything this hub holds is invented -
 * the basin, the quarries, the people - and its geography is invented with
 * it, so this is the shape of a world that never was rather than anybody's
 * palaeogeography. It is here because a map reads as a map: a graticule and a
 * few closed coastlines say "somewhere, long ago" at a glance and at any
 * opacity, which a picture of an animal cannot.
 *
 * Each landmass is a closed curve round a centre, its radius carrying a few
 * harmonics, which gives a coast that bays and juts without any of it being
 * drawn by hand. The shallow sea between them is the gap.
 *
 * @param   string  $file  What to call it
 * @param   int     $size  How big a square it is drawn in
 * @return  void
 */
function world($file, $size)
{
    $c = $size / 2;
    $r = $size * 0.46;

    // The land. Centre, radius, and the harmonics its coast carries. Kept
    // well inside the limb and well apart: the sea between them is the point,
    // and land that fills the disc reads as a stain rather than as a world.
    $lands = [
        [0.34, 0.34, 0.130, [[2, 0.30, 0.4], [3, 0.18, 2.1], [5, 0.10, 1.2]]],
        [0.66, 0.31, 0.085, [[2, 0.34, 2.7], [4, 0.16, 0.9]]],
        [0.41, 0.68, 0.110, [[2, 0.28, 1.6], [3, 0.20, 0.3], [6, 0.09, 2.9]]],
        [0.70, 0.63, 0.070, [[3, 0.30, 1.9], [5, 0.14, 0.7]]],
        [0.56, 0.48, 0.045, [[2, 0.26, 0.8], [4, 0.18, 2.2]]],
    ];

    $coasts = [];

    foreach ($lands as $land) {
        list($lx, $ly, $lr, $waves) = $land;

        $points = [];

        for ($a = 0; $a < 120; $a++) {
            $t   = 2 * M_PI * $a / 120;
            $rad = $size * $lr;

            foreach ($waves as $wave) {
                list($n, $amp, $phase) = $wave;

                $rad *= 1 + ($amp * sin(($n * $t) + $phase));
            }

            $points[] = sprintf(
                '%.1f %.1f',
                ($size * $lx) + ($rad * cos($t)),
                ($size * $ly) + ($rad * sin($t) * 0.82)
            );
        }

        $coasts[] = '<path d="M ' . implode(' L ', $points) . ' Z"/>';
    }

    // The graticule, drawn over the land so the whole reads as a map of it.
    // Meridians are ellipses sharing the frame's height and narrowing toward
    // the limb; parallels are chords of it.
    $lines = [];

    // Meridians as fractions of the radius rather than as evenly spaced
    // longitudes: spacing them evenly puts one at 90 degrees, whose ellipse
    // has no width at all, and draws each of the others twice.
    foreach ([0.30, 0.62, 0.87] as $fraction) {
        $lines[] = sprintf(
            '<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f"/>',
            $c,
            $c,
            $r * $fraction,
            $r
        );
    }

    $lines[] = sprintf(
        '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>',
        $c,
        $c - $r,
        $c,
        $c + $r
    );

    for ($i = 1; $i < 6; $i++) {
        $y  = $c - ($r * cos(M_PI * $i / 6));
        $hw = $r * sin(M_PI * $i / 6);

        $lines[] = sprintf(
            '<line x1="%.1f" y1="%.1f" x2="%.1f" y2="%.1f"/>',
            $c - $hw,
            $y,
            $c + $hw,
            $y
        );
    }

    file_put_contents(
        __DIR__ . '/' . $file,
        '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $size . ' ' . $size . '"'
        . ' role="presentation">' . "\n"
        . '  <defs><clipPath id="limb">'
        . sprintf('<circle cx="%.1f" cy="%.1f" r="%.1f"/>', $c, $c, $r)
        . '</clipPath></defs>' . "\n"
        . '  <g clip-path="url(#limb)" fill="' . MAPINK . '" fill-opacity="0.5">' . "\n    "
        . implode("\n    ", $coasts) . "\n  </g>" . "\n"
        . '  <g clip-path="url(#limb)" fill="none" stroke="' . MAPINK . '" stroke-width="0.9">' . "\n    "
        . implode("\n    ", $lines) . "\n  </g>" . "\n"
        . sprintf(
            '  <circle cx="%.1f" cy="%.1f" r="%.1f" fill="none" stroke="%s" stroke-width="1.6"/>',
            $c,
            $c,
            $r,
            MAPINK
        ) . "\n"
        . '</svg>' . "\n"
    );

    echo sprintf("  %-14s %4dKB\n", $file, max(1, round(filesize(__DIR__ . '/' . $file) / 1024)));
}

/**
 * The line a page's title card is torn along
 *
 * Not a frieze: a mask. Opaque above an irregular line and nothing below it,
 * so the card it is laid over loses its straight bottom edge and gains a
 * weathered one - a bed of rock ends at a contact, not at a ruled line.
 *
 * Built from sines whose periods divide the tile, so the line meets itself
 * where the tile repeats. Anything else shows a seam every few hundred pixels
 * and reads as damage rather than as an edge.
 *
 * @param   string  $file    What to call it
 * @param   int     $width   How wide the tile is
 * @param   int     $height  How tall
 * @param   float   $depth   How far the line moves, top to bottom
 * @return  void
 */
function tear($file, $width, $height, $depth)
{
    $waves = [
        // period divisor, amplitude, phase
        [1, 0.42, 0.0],
        [2, 0.26, 1.1],
        [4, 0.16, 2.3],
        [8, 0.09, 0.4],
        [16, 0.05, 3.7],
    ];

    $points = [];
    $step   = 2;

    for ($x = 0; $x <= $width; $x += $step) {
        $y = $height - $depth;

        foreach ($waves as $wave) {
            list($n, $amp, $phase) = $wave;

            $y += $depth * $amp * sin((2 * M_PI * $n * $x / $width) + $phase);
        }

        $points[] = sprintf('%.1f %.1f', $x, $y);
    }

    // Up the right edge, across the top, and back down the left
    $path = 'M 0 0 L ' . $width . ' 0 L ' . $width . ' '
        . substr(strrchr(' ' . end($points), ' '), 1) . ' L '
        . implode(' L ', array_reverse($points)) . ' Z';

    file_put_contents(
        __DIR__ . '/' . $file,
        '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $width . ' ' . $height . '"'
        . ' preserveAspectRatio="none" role="presentation">' . "\n"
        . '  <path fill="#000" d="' . $path . '"/>' . "\n"
        . '</svg>' . "\n"
    );

    echo sprintf("  %-14s %4dKB\n", $file, max(1, round(filesize(__DIR__ . '/' . $file) / 1024)));
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

// The edge a title card ends on. Shallow: the card is 100px tall and this is
// the last 14 of it, so the line reads as weathering rather than as a rip.
tear('tear.svg', 240, 14, 11);

// The world, for the front page to stand its welcome on
world('world.svg', 600);
