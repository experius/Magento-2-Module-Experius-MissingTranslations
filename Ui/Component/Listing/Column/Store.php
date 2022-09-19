<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class TranslatableString
 */
class Store implements OptionSourceInterface
{
    /**
     * @var array
     */
    protected array $options;

    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManager;

    /**
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        $stores = $this->storeManager->getStores(true);
        if (empty($this->options)) {
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
