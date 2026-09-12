<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2025 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Templates\Meridian;

// No Direct Access stuff, whatever, just keep it there
defined('_HZEXEC_') or die();

/**
 * Every shade Meridian needs, worked out from the one colour a hub picked
 *
 * The template asks for a single accent because that is the question somebody
 * setting up a hub can actually answer. Asking for five colours gets five
 * colours that do not agree with each other, and asking for a text colour to
 * go on the accent gets white on pale yellow.
 *
 * So the rest is derived here, and derived so it cannot fail WCAG 1.4.3: the
 * accent is darkened until it clears 4.5:1 on white before anything sets text
 * in it, the darker shade until it clears 7:1, and the ink over a coloured
 * ground is whichever of white and near-black the ground can carry. A hub can
 * pick any colour it likes and still end up with a page that can be read.
 */
class Palette
{
    /**
     * The colour the hub picked, as three channels
     *
     * @var  array
     */
    protected $rgb;

    /**
     * Set up from a hex string in any of the forms a colour field yields
     *
     * @param   string  $hex
     * @return  void
     */
    public function __construct($hex)
    {
        $hex = strtolower(trim((string) $hex));
        $hex = ltrim($hex, '#');

        if (preg_match('/^[0-9a-f]{3}$/', $hex)) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        if (!preg_match('/^[0-9a-f]{6}$/', $hex)) {
            // Not a colour. The stylesheet's own fallbacks are better than a
            // guess, and returning nothing is how index.php says so.
            $this->rgb = null;
            return;
        }

        $this->rgb = array(
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2))
        );
    }

    /**
     * Whether the colour was one
     *
     * @return  bool
     */
    public function isValid()
    {
        return $this->rgb !== null;
    }

    /**
     * Relative luminance, per WCAG 2.2
     *
     * @param   array  $rgb
     * @return  float
     */
    protected static function luminance($rgb)
    {
        $parts = array(0.2126, 0.7152, 0.0722);
        $total = 0.0;

        foreach ($rgb as $i => $channel) {
            $c = $channel / 255;
            $c = ($c <= 0.03928) ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4);
            $total += $parts[$i] * $c;
        }

        return $total;
    }

    /**
     * Contrast ratio between two colours
     *
     * @param   array  $a
     * @param   array  $b
     * @return  float
     */
    protected static function ratio($a, $b)
    {
        $la = self::luminance($a);
        $lb = self::luminance($b);

        return (max($la, $lb) + 0.05) / (min($la, $lb) + 0.05);
    }

    /**
     * One colour moved towards another
     *
     * @param   array  $a       the colour to move
     * @param   array  $b       where it is moving
     * @param   float  $amount  0 leaves it alone, 1 arrives
     * @return  array
     */
    protected static function mix($a, $b, $amount)
    {
        $out = array();

        foreach ($a as $i => $channel) {
            $out[$i] = (int) round($channel + ($b[$i] - $channel) * $amount);
        }

        return $out;
    }

    /**
     * The colour moved towards black until it carries text on a given ground
     *
     * Stepping rather than solving keeps the hue: the channels move together,
     * so a blue that has to darken stays blue instead of turning slate.
     *
     * The ground is a parameter rather than always white because the band at
     * the head of a component is a wash of the accent, not white, and an
     * accent checked only against white fails on its own wash - a green at
     * 4.53:1 on white came out at 4.18:1 on the band it paints.
     *
     * @param   array  $rgb
     * @param   array  $ground  what the text will sit on
     * @param   float  $want    the ratio it has to reach against that ground
     * @return  array
     */
    protected static function darkenTo($rgb, $ground, $want)
    {
        $black = array(0, 0, 0);

        for ($step = 0; $step <= 100; $step++) {
            $out = self::mix($rgb, $black, $step / 100);

            if (self::ratio($out, $ground) >= $want) {
                return $out;
            }
        }

        return $black;
    }

    /**
     * Hex for a colour
     *
     * @param   array  $rgb
     * @return  string
     */
    protected static function hex($rgb)
    {
        return sprintf('#%02X%02X%02X', $rgb[0], $rgb[1], $rgb[2]);
    }

    /**
     * The custom properties the stylesheet reads, keyed by property name
     *
     * @return  array
     */
    public function properties()
    {
        if (!$this->isValid()) {
            return array();
        }

        $white = array(255, 255, 255);
        $ink   = array(31, 35, 40);

        // The band across the head of a component is a wash of the accent, and
        // the accent's own hover colour lands on it.
        $wash = self::mix($this->rgb, $white, 0.92);
        $edge = self::mix($this->rgb, $white, 0.70);

        // Accent-coloured text lands on two near-white grounds: that wash, and
        // the surface tint panels are painted in - @colorSurface, which has to
        // be repeated here because LESS cannot hand a value to PHP. Either can
        // be the darker one depending on the hue, so clear both; clearing the
        // darker of them clears plain white as well.
        $surface = array(0xF6, 0xF8, 0xFA);

        $onWash    = self::darkenTo($this->rgb, $wash, 4.5);
        $onSurface = self::darkenTo($this->rgb, $surface, 4.5);

        $accent = (self::luminance($onWash) <= self::luminance($onSurface))
            ? $onWash
            : $onSurface;

        // The shade under it carries links, buttons and the footer's ground,
        // where a little more room is worth having.
        $dark  = self::darkenTo($this->rgb, $white, 7.0);
        $light = self::mix($this->rgb, $white, 0.25);

        // Over the darker shade, which is at least 7:1 on white, white always
        // wins - but say so by measuring rather than by assuming, because the
        // same call decides the footer.
        $over = (self::ratio($white, $dark) >= self::ratio($ink, $dark)) ? $white : $ink;

        // The footer's quieter text: the most muted mix of the ink into the
        // ground that still reads at 4.5:1.
        $muted = $over;

        for ($step = 30; $step >= 0; $step--) {
            $try = self::mix($dark, $over, 1 - ($step / 100));

            if (self::ratio($try, $dark) >= 4.5) {
                $muted = $try;
                break;
            }
        }

        return array(
            '--hub-accent'       => self::hex($accent),
            '--hub-accent-dark'  => self::hex($dark),
            '--hub-accent-light' => self::hex($light),
            '--hub-accent-ink'   => self::hex($over),
            '--hub-accent-wash'  => self::hex($wash),
            '--hub-accent-edge'  => self::hex($edge),
            '--hub-footer-ink'   => self::hex($over),
            '--hub-footer-text'  => self::hex($muted),
            '--hub-footer-link'  => self::hex(self::mix($muted, $over, 0.5))
        );
    }
}
