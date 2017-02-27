<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Experius\MissingTranslations\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Experius\MissingTranslations\Module\I18n\Parser\Contextual;

/**
 * Class TranslatableString
 */
class TranslatableString implements OptionSourceInterface
{
    protected $options;

    protected $phrases = [];

    public function __construct(\Magento\Framework\App\Filesystem\DirectoryList $directory_list) {
        $this->phrases = array_map('str_getcsv', file($directory_list->getRoot() . '/app/i18n/Experius/nl_NL/nl_NL.csv'));
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = array();
            foreach($this->phrases as $line => $string) {
                if (key_exists(1,$string) && $string[1] == '') {
                    $this->options[] = array('value' => $string[0], 'label' => $string[0]);
                }
            }
        }
        return $this->options;
    }

}
