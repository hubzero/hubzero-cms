<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Helpers;

use Components\Search\Helpers\MockProxy;
use Hubzero\Utility\Arr;
use Component;

class BoostDocumentTypeMap
{
    protected $lang;

    /**
     * @param   array<mixed>  $args
     */
    public function __construct($args = [])
    {
        $this->lang = Arr::getValue(
            $args,
            'lang',
            new MockProxy(['class' => 'Lang']),
        );
    }

    /**
     * @param   string  $documentType
     * @return  array<string, mixed>
     */
    public function documentTypeToFieldData($documentType)
    {
        switch ($documentType) {
            case $this->getFormattedCitationType():
                $field = 'hubtype';
                $fieldValue = 'citation';
                break;
            default:
                $field = 'type';
                $fieldValue = $documentType;
                break;
        }

        return [
            'field' => $field,
            'field_value' => $fieldValue,
        ];
    }

    /**
     * @param   string  $fieldValue
     * @return  mixed
     */
    public function getFormattedFieldValue($fieldValue)
    {
        switch ($fieldValue) {
            case 'citation':
                $formattedValue = $this->getFormattedCitationType();
                break;
            default:
                $formattedValue = $fieldValue;
                break;
        }

        return $formattedValue;
    }

    /**
     * @return  mixed
     */
    public function getFormattedCitationType()
    {
        $langKey = 'COM_SEARCH_BOOST_DOCUMENT_TYPE_CITATION';

        return $this->lang->txt($langKey);
    }
}
