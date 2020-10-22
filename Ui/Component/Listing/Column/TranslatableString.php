<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

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

    protected $helper;

    public function __construct(
        \Experius\MissingTranslations\Helper\Data $helper
    ) {
        $this->helper = $helper;
        $this->phrases = $this->helper->getPhrases();
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            foreach ($this->phrases as $line => $string) {
                if (key_exists(1, $string) && $string[1] == '') {
                    $this->options[] = ['value' => $line, 'label' => $string[0]];
                }
            }
        }
        return $this->options;
    }
}
