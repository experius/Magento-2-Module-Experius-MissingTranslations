<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Experius\MissingTranslations\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class TranslatableString
 */
class Store implements OptionSourceInterface
{
    protected $options;

    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $stores = $this->storeManager->getStores(true);
        if ($this->options === null) {
            $this->options = [];
            foreach ($stores as $store) {
                if ($store->getStoreId() == 0) {
                    $label = 'Global Level';
                } else {
                    $label = ' - ' . $store->getStoreId() . '. '   . $store->getName() . ' ( ' . $store->getCode() . ' )';
                }
                $this->options[$store->getStoreId()] = ['value' => $store->getStoreId(), 'label' => $label];
            }
        }
        ksort($this->options);
        return $this->options;
    }
}
