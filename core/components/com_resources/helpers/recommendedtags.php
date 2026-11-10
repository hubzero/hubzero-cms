<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Helpers;

use Wamania\Snowball\Stemmer\English as EnglishStemmer;
use Hubzero\Facades\App;

class RecommendedTags
{
    private $tags = [];
    private $existing_tags = [];
    private $existing_map = [];
    private $focus_areas = [];
    private $focus_areas_map = [];
    private $fa_properties = [];
    private $existing_fa_map = [];

    public const ENDORSED_TAG = 2;
    public const REGULAR_TAG  = 1;

    public function __construct($rid, $existing, $opts = [])
    {
        $opts = array_merge([
            'min_len' => 4,
            'count'   => 20,
        ], $opts);

        $dbh = App::get('db');

        $dbh->setQuery(
            'SELECT t.raw_tag, fa.*
			FROM #__focus_areas fa
			INNER JOIN #__tags t ON t.id = fa.tag_id',
        );
        $this->fa_properties = $dbh->loadAssocList('raw_tag');

        $dbh->setQuery(
            'SELECT raw_tag, (label IS NOT NULL AND label != "") AS is_focus_area
			FROM #__tags_object to1
			INNER JOIN #__tags t ON t.id = to1.tagid
			WHERE to1.tbl = \'resources\' AND to1.objectid = ' . $rid,
        );
        if (!$existing) {
            foreach ($dbh->loadAssocList() as $tag) {
                if ($tag['is_focus_area']) {
                    $this->focus_areas[] = $tag['raw_tag'];
                    $this->existing_fa_map[strtolower($tag['raw_tag'])] = true;
                } else {
                    $this->existing_tags[] = $tag['raw_tag'];
                    $this->existing_map[strtolower($tag['raw_tag'])] = true;
                }
            }
        } else {
            foreach ($existing as $tag) {
                if (!is_null($tag[2])) {
                    $this->existing_fa_map[strtolower($tag[0])] = true;
                } else {
                    $this->existing_tags[] = $tag[0];
                    $this->existing_map[strtolower($tag[0])] = true;
                }
            }
        }

        $dbh->setQuery('SELECT lower(raw_tag) AS raw_tag, CASE WHEN to1.id IS NULL THEN 0 ELSE 1 END AS is_endorsed
			FROM #__tags t
			LEFT JOIN #__tags_object to1 ON to1.tbl = \'tags\' AND to1.objectid = t.id AND to1.label = \'label\' AND to1.tagid = (SELECT id FROM #__tags WHERE tag = \'endorsed\')');

        $tags = [];
        foreach ($dbh->loadAssocList() as $row) {
            $tags[\Hubzero\Utility\Inflector::singularize($row['raw_tag'])] = $row['is_endorsed']
                ? self::ENDORSED_TAG
                : self::REGULAR_TAG;
            $tags[\Hubzero\Utility\Inflector::pluralize($row['raw_tag'])] = $row['is_endorsed']
                ? self::ENDORSED_TAG
                : self::REGULAR_TAG;
        }

        $dbh->setQuery(
            'SELECT body FROM #__resource_assoc ra
			LEFT JOIN #__document_resource_rel drr ON drr.resource_id = ra.child_id
			INNER JOIN #__document_text_data dtd ON dtd.id = drr.document_id
			WHERE ra.parent_id = ' . $rid,
        );
        $words = preg_split('/\W+/', join(' ', $dbh->loadColumn()));
        $word_count = count($words);
        if (!$words[$word_count - 1]) {
            array_pop($words);
            --$word_count;
        }

        $stemmer = new EnglishStemmer();
        $freq = [];
        $last = [];
        foreach ($words as $idx => $word) {
            if (self::isStopWord($word, $opts['min_len'])) {
                continue;
            }
            $stems = [[$stemmer->stem($word), strtolower($word)]];
            if (isset($words[$idx + 1]) && !self::isStopWord($words[$idx + 1], $opts['min_len'])) {
                $stems[] = [
                    $stems[0][0] . ' ' . $stemmer->stem($words[$idx + 1]),
                    strtolower($word) . ' ' . strtolower($words[$idx + 1]),
                ];
            }
            if (isset($words[$idx + 2]) && !self::isStopWord($words[$idx + 2], $opts['min_len'])) {
                $stems[] = [
                    $stems[0][0] . ' ' . $stemmer->stem($words[$idx + 1]) . ' ' . $stemmer->stem($words[$idx + 2]),
                    \Hubzero\Utility\Inflector::singularize(strtolower($word))
                        . ' '
                        . strtolower($words[$idx + 1])
                        . ' '
                        . strtolower($words[$idx + 2]),
                ];
            }
            foreach ($stems as $set_idx => $set) {
                list($stem, $word) = $set;
                if (isset($this->existing_map[strtolower($word)]) || isset($this->focus_area_map[strtolower($word)])) {
                    continue;
                }
                if (!isset($freq[$stem])) {
                    $freq[$stem] = ['text' => $word, 'count' => 0];
                } else {
                    $freq[$stem]['count'] += ($idx - $last[$stem]) / $word_count * ($set_idx + 1);
                }
                $last[$stem] = $idx;
            }
        }

        foreach ($freq as $stem => $def) {
            foreach ([$stem, $def['text']] as $text) {
                if (isset($tags[$text])) {
                    $freq[$stem]['count'] += $tags[$text] === self::ENDORSED_TAG ? 3 : 1.5;
                    break;
                }
            }
        }
        usort($freq, function ($a, $b) {
            return $a['count'] === $b['count'] ? 0 : ($a['count'] > $b['count'] ? -1 : 1);
        });
        $this->tags = array_slice($freq, 0, $opts['count']);
    }

    private static function isStopWord($word, $word_min_len)
    {
        static $stop_words = [
            "a" =>       true,
            "able" =>    true,
            "about" =>     true,
            "across" =>    true,
            "after" =>   true,
            "akin" =>    true,
            "all" =>     true,
            "almost" =>    true,
            "also" =>      true,
            "am" =>      true,
            "among" =>   true,
            "an" =>      true,
            "and" =>       true,
            "any" =>       true,
            "are" =>     true,
            "as" =>      true,
            "at" =>      true,
            "be" =>        true,
            "because" =>   true,
            "been" =>    true,
            "between" => true,
            "but" =>     true,
            "by" =>        true,
            "can" =>       true,
            "cannot" =>  true,
            "could" =>   true,
            "dear" =>    true,
            "did" =>       true,
            "do" =>        true,
            "does" =>    true,
            "each" =>    true,
            "either" =>  true,
            "else" =>      true,
            "ever" =>      true,
            "every" =>   true,
            "for" =>     true,
            "from" =>    true,
            "get" =>       true,
            "got" =>       true,
            "had" =>     true,
            "has" =>     true,
            "have" =>    true,
            "he" =>        true,
            "her" =>       true,
            "hers" =>    true,
            "him" =>     true,
            "his" =>     true,
            "how" =>       true,
            "however" =>   true,
            "i" =>       true,
            "if" =>      true,
            "in" =>      true,
            "into" =>      true,
            "is" =>        true,
            "it" =>      true,
            "its" =>     true,
            "just" =>    true,
            "least" =>     true,
            "let" =>       true,
            "like" =>    true,
            "likely" =>  true,
            "may" =>     true,
            "me" =>        true,
            "might" =>     true,
            "more" =>    true,
            "most" =>    true,
            "must" =>    true,
            "my" =>        true,
            "neither" =>   true,
            "no" =>      true,
            "nor" =>     true,
            "not" =>     true,
            "of" =>        true,
            "off" =>       true,
            "often" =>   true,
            "on" =>      true,
            "once" =>    true,
            "only" =>      true,
            "or" =>        true,
            "other" =>   true,
            "our" =>     true,
            "own" =>     true,
            "rather" =>    true,
            "said" =>      true,
            "say" =>     true,
            "says" =>    true,
            "she" =>     true,
            "should" =>    true,
            "since" =>     true,
            "so" =>      true,
            "some" =>    true,
            "than" =>    true,
            "that" =>      true,
            "the" =>       true,
            "their" =>   true,
            "them" =>    true,
            "then" =>    true,
            "there" =>     true,
            "therefore" => true,
            "these" =>   true,
            "they" =>    true,
            "this" =>    true,
            "those" =>     true,
            "though" =>    true,
            "through" => true,
            "tis" =>     true,
            "to" =>      true,
            "too" =>       true,
            "twas" =>      true,
            "twice" =>   true,
            "us" =>      true,
            "wants" =>   true,
            "was" =>       true,
            "we" =>        true,
            "were" =>    true,
            "what" =>    true,
            "when" =>    true,
            "where" =>     true,
            "which" =>     true,
            "while" =>   true,
            "who" =>     true,
            "whoever" => true,
            "whom" =>      true,
            "whomever" =>  true,
            "why" =>     true,
            "well" =>    true,
            "will" =>    true,
            "with" =>      true,
            "would" =>     true,
            "yet" =>     true,
            "you" =>     true,
            "your" =>    true,
            "one" =>       true,
            "two" =>       true,
            "three" =>   true,
            "four" =>    true,
            "five" =>    true,
            "six" =>       true,
            "seven" =>     true,
            "eight" =>   true,
            "nine" =>    true,
            "ten" =>     true,
        ];
        return isset($stop_words[$word]) || strlen($word) < $word_min_len;
    }

    public function getTags()
    {
        return $this->tags;
    }
    public function getExistingTags()
    {
        return $this->existing_tags;
    }
    public function getExistingTagsMap()
    {
        return $this->existing_map;
    }
    public function getExistingTagsValueList()
    {
        static $val_list = [];
        if (!$val_list) {
            foreach ($this->existing_tags as $tag) {
                $val_list[] = str_replace('"', '&quot;', str_replace(',', '&#44;', $tag));
            }
        }
        return implode(',', $val_list);
    }
    public function getFocusAreas()
    {
        return $this->focus_areas;
    }
    public function getFocusAreasMap()
    {
        return $this->focus_areas_map;
    }
    public function getExistingFocusAreasMap()
    {
        return $this->existing_fa_map;
    }
    public function getFocusAreaProperties()
    {
        return $this->fa_properties;
    }
}
