<?php
// A footer band: a long low horizon with a few animals on it, drawn from the
// same shapes the pack uses, saved as a template asset so the template does
// not depend on a hub having run the sample data.
require '/home/su-nkisseberth/hubzero-mesozoic/Mesozoic/Silhouettes.php';

use Hubzero\Sampledata\Packs\Mesozoic\Silhouettes;

const W = 1600;
const H = 150;
const GROUND = 126;

function put($name, $x, $scale, $opacity, $flip = false, $y = null)
{
    $top = ($y === null) ? GROUND - (Silhouettes::GROUND * $scale) : $y;
    $sx  = $flip ? -$scale : $scale;
    $ox  = $flip ? $x + (Silhouettes::WIDTH * $scale) : $x;

    return sprintf(
        '<g opacity="%.2f" transform="translate(%.1f %.1f) scale(%.3f %.3f)">%s</g>',
        $opacity, $ox, $top, $sx, $scale, Silhouettes::draw($name)
    );
}

$g = [];

// No ground under them. A filled horizon reads as a shelf at this height, and
// the footer's own edge is a better line to stand on.

// Spaced across the band, small, facing both ways
$g[] = put('stegosaur',        60, 0.34, 0.16);
$g[] = put('pterosaur',       230, 0.26, 0.11, true, 18);
$g[] = put('ankylosaur',      330, 0.31, 0.14, true);
$g[] = put('sauropod',        560, 0.40, 0.18);
$g[] = put('hadrosaur',       820, 0.33, 0.15, true);
$g[] = put('pterosaur',       980, 0.22, 0.10, false, 30);
$g[] = put('ceratopsian',    1060, 0.32, 0.16);
$g[] = put('raptor',         1270, 0.28, 0.13, true);
$g[] = put('theropod',       1420, 0.36, 0.17);

file_put_contents(
    __DIR__ . '/frieze.svg',
    '<?xml version="1.0" encoding="UTF-8"?>' . "\n"
    . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . W . ' ' . H . '"'
    . ' preserveAspectRatio="xMidYMax slice" role="presentation">' . "\n"
    . '  <g fill="#5C4A3A">' . "\n    " . implode("\n    ", $g) . "\n"
    . '  </g>' . "\n</svg>\n"
);

echo "wrote ", round(filesize(__DIR__ . '/frieze.svg')/1024), "KB\n";
