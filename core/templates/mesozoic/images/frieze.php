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
 * The world in the Late Cretaceous, about ninety million years ago
 *
 * Approximate, and coarse on purpose: the coastlines are a few dozen points
 * each, taken from the general arrangement of the period rather than plotted
 * from a dataset, and the whole thing is shown at a few hundred pixels and a
 * tenth of an opacity. It is a schematic at the size it is used and would not
 * survive being enlarged.
 *
 * What it gets right is what somebody who knows the period would look for:
 * North America in two pieces either side of the Western Interior Seaway, the
 * South Atlantic open but narrow, Europe an archipelago rather than a
 * continent, India out on its own in the southern ocean, Australia still
 * joined to Antarctica, and the Tethys open between Laurasia and Gondwana.
 * The Cretaceous because it is the period most of this hub's content is
 * about, rather than the Triassic or the Jurassic.
 *
 * Drawn on Mollweide, which is the projection palaeogeography is usually
 * published on: it holds area, which is what a map of where the land was is
 * for, and its ellipse reads as a map of a whole world rather than as a
 * picture of part of one. A globe was tried first and had to leave half the
 * continents round the back.
 *
 * @param   string  $file  What to call it
 * @param   int     $size  How wide it is drawn; the height is half of that
 * @return  void
 */
function world($file, $size)
{
    // Longitude and latitude, degrees, round each coast
    $land = [
        // Laramidia: western North America, west of the seaway
        [[-122, 66], [-114, 69], [-107, 67], [-103, 61], [-100, 54],
         [-101, 47], [-103, 40], [-106, 35], [-111, 30], [-116, 32],
         [-119, 40], [-121, 48], [-122, 57]],

        // Appalachia: the eastern half, an island for most of the period
        [[-95, 50], [-88, 53], [-80, 52], [-72, 48], [-64, 42], [-66, 37],
         [-70, 33], [-77, 31], [-84, 30], [-90, 34], [-93, 42]],

        // Greenland, still against North America
        [[-45, 78], [-25, 76], [-20, 68], [-30, 60], [-45, 62], [-52, 70]],

        // Asia, with the Turgai Strait between it and Europe
        [[60, 70], [78, 75], [100, 77], [124, 73], [148, 70], [162, 62],
         [155, 52], [142, 45], [128, 38], [110, 34], [92, 36], [76, 41],
         [66, 48], [58, 58]],

        // Europe: what was above water, which was not much of it
        [[-8, 40], [0, 42], [3, 37], [-6, 35]],
        [[6, 50], [16, 51], [18, 46], [8, 45]],
        [[12, 66], [28, 68], [32, 60], [18, 56], [10, 60]],

        // Africa, across a South Atlantic that had only just opened
        [[-16, 30], [-4, 33], [10, 34], [24, 33], [34, 28], [40, 18],
         [46, 6], [43, -8], [36, -22], [28, -32], [18, -35], [8, -28],
         [2, -14], [-4, 2], [-12, 14], [-17, 22]],

        // South America
        [[-78, 8], [-66, 11], [-54, 5], [-44, -3], [-38, -12], [-42, -24],
         [-50, -34], [-58, -44], [-68, -48], [-75, -38], [-79, -24],
         [-81, -10], [-80, 0]],

        // India, out in the southern ocean and on its way north
        [[62, -18], [70, -13], [77, -20], [79, -30], [72, -36], [65, -30]],

        // Madagascar
        [[44, -16], [50, -18], [51, -26], [45, -25]],

        // Australia, not yet parted from Antarctica
        [[108, -46], [124, -43], [140, -45], [152, -52], [148, -62],
         [130, -65], [114, -60], [106, -53]],

        // Antarctica: a cap, closed over the pole, where every meridian meets
        [[-180, -64], [-140, -61], [-100, -66], [-60, -62], [-20, -65],
         [20, -63], [60, -60], [100, -64], [140, -62], [180, -64],
         [180, -90], [-180, -90]],
    ];

    $lon0 = 0;

    // Mollweide is an ellipse two wide and one tall, so the drawing is too
    $w  = $size;
    $h  = $size / 2;
    $cx = $w / 2;
    $cy = $h / 2;

    /**
     * One point of the world, on the page
     *
     * @param   float  $lon  Degrees
     * @param   float  $lat  Degrees
     * @return  array  x, y
     */
    $project = function ($lon, $lat) use ($cx, $cy, $lon0) {
        $phi = deg2rad($lat);

        // 2t + sin 2t = pi sin phi, which has no closed form, so Newton it.
        // At the poles the equation is satisfied exactly and the derivative
        // is zero, so they are taken as read rather than iterated toward.
        if (abs($lat) >= 89.999) {
            $theta = ($lat > 0 ? 1 : -1) * M_PI / 2;
        } else {
            $theta = $phi;

            for ($i = 0; $i < 12; $i++) {
                $d = (2 * $theta) + sin(2 * $theta) - (M_PI * sin($phi));

                $theta -= $d / (2 + (2 * cos(2 * $theta)));
            }
        }

        $x = (2 * M_SQRT2 / M_PI) * deg2rad($lon - $lon0) * cos($theta);
        $y = M_SQRT2 * sin($theta);

        // The ellipse is 2*sqrt(2) wide and sqrt(2) tall in those units
        return [
            $cx + ($x * $cx / (2 * M_SQRT2)),
            $cy - ($y * $cy / M_SQRT2),
        ];
    };

    $coasts = [];

    foreach ($land as $coast) {
        $points = [];
        $count  = count($coast);

        // Sampled along each edge rather than corner to corner: a straight
        // line between two points of a sphere is not straight on a map
        for ($i = 0; $i < $count; $i++) {
            $from = $coast[$i];
            $to   = $coast[($i + 1) % $count];

            for ($step = 0; $step < 8; $step++) {
                $t = $step / 8;

                list($x, $y) = $project(
                    $from[0] + (($to[0] - $from[0]) * $t),
                    $from[1] + (($to[1] - $from[1]) * $t)
                );

                $points[] = sprintf('%.1f %.1f', $x, $y);
            }
        }

        $coasts[] = '<path d="M ' . implode(' L ', $points) . ' Z"/>';
    }

    // The graticule, every thirty degrees, drawn over the land so the whole
    // reads as a map of it
    $lines = [];

    for ($lat = -60; $lat <= 60; $lat += 30) {
        $lines[] = '<path d="' . graticule($project, $lat, null) . '"/>';
    }

    for ($lon = -150; $lon <= 150; $lon += 30) {
        $lines[] = '<path d="' . graticule($project, null, $lon) . '"/>';
    }

    file_put_contents(
        __DIR__ . '/' . $file,
        '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
        . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $w . ' ' . $h . '"'
        . ' role="presentation">' . "\n"
        . '  <defs><clipPath id="edge">'
        . sprintf('<ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f"/>', $cx, $cy, $cx, $cy)
        . '</clipPath></defs>' . "\n"
        . '  <g clip-path="url(#edge)" fill="' . MAPINK . '" fill-opacity="0.5">' . "\n    "
        . implode("\n    ", $coasts) . "\n  </g>" . "\n"
        . '  <g clip-path="url(#edge)" fill="none" stroke="' . MAPINK . '"'
        . ' stroke-width="0.9" stroke-opacity="0.8">' . "\n    "
        . implode("\n    ", $lines) . "\n  </g>" . "\n"
        . sprintf(
            '  <ellipse cx="%.1f" cy="%.1f" rx="%.1f" ry="%.1f" fill="none"'
            . ' stroke="%s" stroke-width="1.6"/>',
            $cx,
            $cy,
            $cx - 0.8,
            $cy - 0.8,
            MAPINK
        ) . "\n"
        . '</svg>' . "\n"
    );

    echo sprintf("  %-14s %4dKB\n", $file, max(1, round(filesize(__DIR__ . '/' . $file) / 1024)));
}

/**
 * One line of the graticule
 *
 * @param   callable  $project  Turns a point of the world into a point on the page
 * @param   float     $lat      Fixed, for a parallel
 * @param   float     $lon      Fixed, for a meridian
 * @return  string    Path data
 */
function graticule(callable $project, $lat = null, $lon = null)
{
    $parts = [];

    for ($step = 0; $step <= 120; $step++) {
        $t = $step / 120;

        $point = ($lat === null)
            ? $project($lon, -90 + (180 * $t))
            : $project(-180 + (360 * $t), $lat);

        $parts[] = sprintf('%s %.1f %.1f', $step ? 'L' : 'M', $point[0], $point[1]);
    }

    return implode(' ', $parts);
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

// The world the hub's content comes out of, for a landing page to stand on
world('world.svg', 800);
