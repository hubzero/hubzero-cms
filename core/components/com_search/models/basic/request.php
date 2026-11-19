<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic;

use Components\Search\Models\Basic\Result\Sql;
use Hubzero\Facades\App;

/**
 * Search request model
 */
class Request
{
    /**
     * Description for 'terms'
     *
     * @var \Components\Search\Models\Basic\Terms
     */
    private $terms;
    /**
     * @var  array<string, mixed>|null
     */
    private $term_ar;
    /**
     * @var  \Components\Search\Models\Basic\Result\AssocList
     */
    private $tags;
    /**
     * @var  array<mixed>
     */
    private $object_tags = [];

    /**
     * Short description for 'get_terms'
     *
     * Long description (if any) ...
     *
     * @return     \Components\Search\Models\Basic\Terms Return description (if any) ...
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Short description for 'get_term_ar'
     *
     * Long description (if any) ...
     *
     * @return     mixed Return description (if any) ...
     */
    public function getTermAr()
    {
        return $this->term_ar;
    }

    /**
     * Short description for 'get_tags'
     *
     * Long description (if any) ...
     *
     * @return     mixed Return description (if any) ...
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Short description for 'get_tagged_ids'
     *
     * Long description (if any) ...
     *
     * @param      mixed $tbl Parameter description (if any) ...
     * @return     array<mixed> Return description (if any) ...
     */
    public function getTaggedIds($tbl)
    {
        return array_key_exists($tbl, $this->object_tags) ? $this->object_tags[$tbl] : [];
    }

    /**
     * Short description for 'get_tag_ids_by_table'
     *
     * Long description (if any) ...
     *
     * @param      mixed $tbl Parameter description (if any) ...
     * @return     array<mixed> Return description (if any) ...
     */
    public function getTagIdsByTable($tbl)
    {
        return array_key_exists($tbl, $this->object_tags) ? $this->object_tags[$tbl] : [];
    }

    /**
     * Constructor
     *
     * @param      \Components\Search\Models\Basic\Terms|null $terms Parameter description (if any) ...
     * @return     void
     */
    public function __construct($terms)
    {
        $this->terms = $terms;
        $this->term_ar = [
            'mandatory' => $this->termsOrFail()->getMandatoryChunks(),
            'optional'  => $this->termsOrFail()->getOptionalChunks(),
            'forbidden' => $this->termsOrFail()->getForbiddenChunks(),
            'stemmed'   => $this->termsOrFail()->getStemmedChunks(),
        ];
        $this->loadTags();
    }

    /**
     * Short description for 'loadTags'
     *
     * Long description (if any) ...
     *
     * @return     void
     */
    private function loadTags()
    {
        $dbh = \App::get('db');

        $stemmed = $dbh->escape(join(' ', $this->term_ar['stemmed']));
        $weight = 'match(t.raw_tag, t.description) against (\'' . $stemmed . '\')';

        $addtl_where = [];
        foreach ($this->term_ar['mandatory'] as $mand) {
            $mand = $dbh->escape($mand);
            $addtl_where[] = "(t.raw_tag LIKE '%$mand%' OR t.tag LIKE '%$mand%' OR t.description LIKE '%$mand%')";
        }
        foreach ($this->term_ar['forbidden'] as $forb) {
            $forb = $dbh->escape($forb);
            $addtl_where[] = "(t.raw_tag NOT LIKE '%$forb%' AND t.tag NOT LIKE '%$forb%' AND t.description NOT LIKE "
                . "'%$forb%')";
        }

        $tags = new Sql(
            "SELECT
				id,
				t.raw_tag AS title,
				description,
				concat('index.php?option=com_tags&tag=', t.tag) AS link,
				$weight AS weight,
				NULL AS date,
				'Tags' AS section
			FROM `#__tags` t
			WHERE
				$weight > 0"
                . ($addtl_where ? ' AND ' . join(' AND ', $addtl_where) : '')
            . " ORDER BY $weight DESC",
        );
        $this->tags = $tags->toAssociative();

        $tag_ids = [];
        foreach ($this->tags as $tag) {
            $tag_ids[] = $tag->get('id');
        }

        if ($tag_ids) {
            $dbh = App::get('db');
            $dbh->setQuery(
                'SELECT objectid, tbl FROM `#__tags_object` WHERE tagid IN (' . join(',', $tag_ids) . ')',
            );
            foreach ($dbh->loadAssocList() ?? [] as $row) {
                if (!array_key_exists($row['tbl'], $this->object_tags)) {
                    $this->object_tags[$row['tbl']] = [];
                }
                $this->object_tags[$row['tbl']][] = $row['objectid'];
            }
        }
    }

    /**
     * The terms, which the calls using it need set
     *
     * @return  \Components\Search\Models\Basic\Terms
     * @throws  \LogicException  When it is not
     */
    protected function termsOrFail()
    {
        if ($this->terms === null) {
            throw new \LogicException(static::class . '::$terms is not set');
        }

        return $this->terms;
    }
}
