<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic;

use Hubzero\Facades\Route;

/**
 * Abstract search result
 *
 * Fields are set from query rows and highlighted copies of them, so the
 * set of properties is data-driven.
 */
// Results carry per-plugin fields (link, sql, *_highlighted, ...) set as
// dynamic properties by the plugins and the highlighter
#[\AllowDynamicProperties]
abstract class Result
{
    /**
     * Description for 'intro_excerpt_len'
     *
     * @var integer
     */
    private static $intro_excerpt_len = 350;

    /**
     * Description for 'types'
     *
     * @var array
     */
    private static $types = [];

    /**
     * Description for 'excerpt'
     *
     * @var unknown
     */
    private $excerpt;
    private $plugin;
    private $canonicalized_link;
    private $highlight_regex;

    /**
     * Description for 'id'
     *
     * @var unknown
     */
    protected $id;
    protected $title;
    protected $description;
    protected $tag_count;
    protected $author;
    protected $weight;
    protected $section;
    protected $date;
    protected $contributors;
    protected $contributor_ids;
    protected $children = [];
    protected $weight_log = [];

    /**
     * Where the result points
     *
     * Public, and not for tidiness: the search plugins read and append to it
     * directly on the result they are about to add - the forum's builds a
     * thread's path onto it a segment at a time - and until it was declared
     * here it was a dynamic property, which is public. Declaring it protected
     * made both the wiki and the forum search plugins fatal on any term that
     * matched, so the search page returned an empty document.
     *
     * @var  string
     */
    public $link;
    protected $description_highlighted;
    protected $title_highlighted;
    protected $author_highlighted;
    protected $excerpt_highlighted;

    /**
     * Description for 'has_parent'
     *
     * @var boolean
     */
    protected $has_parent = false;

    /**
     * Short description for 'is_in_section'
     *
     * Long description (if any) ...
     *
     * @param      array $section Parameter description (if any) ...
     * @param      unknown $plugin Parameter description (if any) ...
     * @return     mixed Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function isInSection($section, $plugin = null)
    {
        if (is_null($plugin)) {
            $plugin = $this->plugin;
        }

        if (!$section) {
            return true;
        }

        if (count($section) == 2) {
            return strtolower($section[0]) == strtolower($plugin) && strtolower($section[1]) == $this->getSection();
        }

        return strtolower($section[0]) == strtolower($plugin);
    }

    /**
     * Short description for 'get_weight_log'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getWeightLog()
    {
        return $this->weight_log;
    }

    /**
     * Short description for 'has_parent'
     *
     * Long description (if any) ...
     *
     * @return     boolean Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function hasParent()
    {
        return $this->has_parent;
    }

    /**
     * Short description for 'set_has_parent'
     *
     * Long description (if any) ...
     *
     * @param      unknown $bool Parameter description (if any) ...
     * @return     void
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function setHasParent($bool)
    {
        $this->has_parent = $bool;
    }

    /**
     * Short description for 'add_child'
     *
     * Long description (if any) ...
     *
     * @param      object $child Parameter description (if any) ...
     * @return     void
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function addChild($child)
    {
        $this->children[] = $child;
        $child->setHasParent(true);
    }

    /**
     * Short description for 'get_children'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Short description for 'sort_children'
     *
     * Long description (if any) ...
     *
     * @param      unknown $callback Parameter description (if any) ...
     * @return     void
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function sortChildren($callback)
    {
        usort($this->children, $callback);
    }

    /**
     * Short description for 'get'
     *
     * Long description (if any) ...
     *
     * @param      unknown $key Parameter description (if any) ...
     * @return     unknown Return description (if any) ...
     */
    public function get($key)
    {
        return $this->$key;
    }

    /**
     * Short description for 'get_title'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Short description for 'get_highlighted_title'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getHighlightedTitle()
    {
        // return $this->title_highlighted; @FIXME: this property no longer seems to exist
        return $this->title;
    }

    /**
     * Short description for 'get_raw_link'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getRawLink()
    {
        return $this->link;
    }

    /**
     * Short description for 'get_link'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getLink()
    {
        if (!$this->canonicalized_link) {
            if (preg_match('/^https?:\/\//', $this->link)) {
                $this->canonicalized_link = $this->link;
            } else {
                $base = rtrim(\Request::base(), '/');
                $route = ltrim(Route::url($this->link), '/');
                $this->canonicalized_link = $base . '/' . substr($route, strlen(\Request::base(true)));
            }
        }
        return $this->canonicalized_link;
    }

    /**
     * Short description for 'get_links'
     *
     * Long description (if any) ...
     *
     * @return     array Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getLinks()
    {
        $links = [$this->getLink()];
        foreach ($this->children as $child) {
            $links = array_merge($links, $child->getLinks());
        }
        return $links;
    }

    /**
     * Short description for 'set_link'
     *
     * Long description (if any) ...
     *
     * @param      unknown $link Parameter description (if any) ...
     * @return     void
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function setLink($link)
    {
        $this->link = $link;
        $this->canonicalized_link = null;
    }

    /**
     * Short description for 'get_description'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Short description for 'set_description'
     *
     * Long description (if any) ...
     *
     * @param      unknown $descr Parameter description (if any) ...
     * @return     void
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function setDescription($descr)
    {
        $this->description = $descr;
    }

    /**
     * Short description for 'get_highlighted_description'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getHighlightedDescription()
    {
        return $this->description_highlighted;
    }

    /**
     * Short description for 'get_author'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Short description for 'get_weight'
     *
     * Long description (if any) ...
     *
     * @return     integer Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getWeight()
    {
        return is_null($this->weight) ? 1 : $this->weight;
    }

    /**
     * Short description for 'get_section'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getSection()
    {
        return ucwords($this->section);
    }

    /**
     * Short description for 'get_section_key'
     *
     * Long description (if any) ...
     *
     * @return     string Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getSectionKey()
    {
        return trim($this->section) ? str_replace(' ', '_', strtolower(trim($this->section))) : 'uncategorized';
    }

    /**
     * Short description for 'get_date'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Short description for 'get_contributors'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getContributors()
    {
        return $this->contributors;
    }

    /**
     * Short description for 'get_contributor_ids'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getContributorIds()
    {
        return $this->contributor_ids;
    }

    /**
     * Short description for 'has_metadata'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function hasMetadata()
    {
        return !!($this->section || $this->date || $this->contributors);
    }

    /**
     * Short description for 'get_highlighted_excerpt'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getHighlightedExcerpt()
    {
        return $this->getExcerpt();
    }

    /**
     * Short description for 'adjust_weight'
     *
     * Long description (if any) ...
     *
     * @param      mixed $weight Parameter description (if any) ...
     * @param      string $reason Parameter description (if any) ...
     * @return     void
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function adjustWeight($weight, $reason = 'unknown')
    {
        $this->weight *= $weight;
        $this->weight_log[] = 'adjusting by ' . $weight . ' to ' . $this->weight . ': ' . $reason;
    }

    /**
     * Short description for 'scale_weight'
     *
     * Long description (if any) ...
     *
     * @param      mixed $scale Parameter description (if any) ...
     * @param      string $reason Parameter description (if any) ...
     * @return     void
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function scaleWeight($scale, $reason = 'unknown')
    {
        if ($scale != 0) {
            $this->weight /= $scale;
            $this->weight_log[] = 'scaling by ' . $scale . ' to ' . $this->weight . ': ' . $reason;
        }
    }

    /**
     * Short description for 'add_weight'
     *
     * Long description (if any) ...
     *
     * @param      string $weight Parameter description (if any) ...
     * @param      string $reason Parameter description (if any) ...
     * @return     void
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function addWeight($weight, $reason = 'unknown')
    {
        $this->weight += $weight;
        $this->weight_log[] = 'adding ' . $weight . ', total ' . $this->weight . ': ' . $reason;
    }

    /**
     * Short description for 'get_plugin'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * Short description for 'set_plugin'
     *
     * Long description (if any) ...
     *
     * @param      unknown $plg Parameter description (if any) ...
     * @param      boolean $skip_cleanup Parameter description (if any) ...
     * @return     void
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function setPlugin($plg, $skip_cleanup = false)
    {
        $this->plugin = $skip_cleanup ? $plg : strtolower(preg_replace('/^plgSearch/i', '', (string) $plg));
        foreach ($this->children as $child) {
            $child->setPlugin($this->plugin, true);
        }
    }

    /**
     * Short description for 'highlight'
     *
     * Long description (if any) ...
     *
     * @param      unknown $regex Parameter description (if any) ...
     * @return     void
     */
    public function highlight($regex)
    {
        $this->highlight_regex = $regex;
        $hl = "#$regex#ims";
        foreach (['title', 'author', 'excerpt'] as $key) {
            if (!empty($this->$key)) {
                $hlkey = "{$key}_highlighted";
                $this->$hlkey = preg_replace($hl, '<span class="highlight">$1</span>', $this->$key);
            }
        }

        foreach ($this->children as &$child) {
            $child->highlight($regex);
        }
    }

    /**
     * Short description for 'get_excerpt'
     *
     * Long description (if any) ...
     *
     * @return     unknown Return description (if any) ...
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    public function getExcerpt()
    {
        if (!$this->excerpt) {
            $descr = preg_replace('#(?:[{]xhub:.*?[}]|[{}]|[\#][!]html)#ixms', '', $this->description);
            $pattern = '#(?:^|[.?!"])\s*?(.*?' . $this->highlight_regex . '.*?(?:[.?!"]|$))#ims';
            if (preg_match_all($pattern, $descr, $excerpt)) {
                $descr = join('<small>&hellip;</small> ', array_unique($excerpt[1]));
            }

            // A line break in the source stands between two words, so it
            // becomes a space; dropping it ran them together
            $wrapped = wordwrap(trim(preg_replace('/\s+/u', ' ', $descr)), self::$intro_excerpt_len);
            $wrap_pos = strpos($wrapped, "\n");
            $len = $wrap_pos ? $wrap_pos : self::$intro_excerpt_len;
            $ellip = $wrap_pos ? '<small>&hellip;</small>' : '';
            $this->excerpt = substr($wrapped, 0, $len) . $ellip;
            $this->excerpt = preg_replace(
                "#$this->highlight_regex#ims",
                '<span class="highlight">$1</span>',
                $this->excerpt,
            );
        }
        return $this->excerpt;
    }

    /**
     * Short description for 'to_associative'
     *
     * Long description (if any) ...
     *
     */
    // phpcs:ignore PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    abstract public function toAssociative();
}
