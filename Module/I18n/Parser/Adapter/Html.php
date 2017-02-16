<?php
/**
 * Collect missing translations in specified folder or the entire Magento 2 Root
 * Copyright (C) 2016 Experius
 *
 * This file included in Experius/MissingTranslations is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */
namespace Experius\MissingTranslations\Module\I18n\Parser\Adapter;

use Magento\Email\Model\Template\Filter;

/**
 * Html parser adapter
 */
class Html extends AbstractAdapter
{
    /**
     * Covers
     * <span><!-- ko i18n: 'Next'--><!-- /ko --></span>
     * <th class="col col-method" data-bind="i18n: 'Select Method'"></th>
     */
    const HTML_FILTER = "/i18n:\s?'(?<value>[^'\\\\]*(?:\\\\.[^'\\\\]*)*)'/i";

    /**
     * {@inheritdoc}
     */
    protected function _parse()
    {
        $data = file_get_contents($this->_file);
        if ($data === false) {
            throw new \Exception('Failed to load file from disk.');
        }

        $results = [];
        preg_match_all(Filter::CONSTRUCTION_PATTERN, $data, $results, PREG_SET_ORDER);
        for ($i = 0; $i < count($results); $i++) {
            if ($results[$i][1] === Filter::TRANS_DIRECTIVE_NAME) {
                $directive = [];
                if (preg_match(Filter::TRANS_DIRECTIVE_REGEX, $results[$i][2], $directive) !== 1) {
                    continue;
                }
                $quote = $directive[1];
                $this->_addPhrase($quote . $directive[2] . $quote);
            }
        }

        preg_match_all(self::HTML_FILTER, $data, $results, PREG_SET_ORDER);
        for ($i = 0; $i < count($results); $i++) {
            if (!empty($results[$i]['value'])) {
                $this->_addPhrase($results[$i]['value']);
            }
        }
    }
}
