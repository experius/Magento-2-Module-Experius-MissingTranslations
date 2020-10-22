<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Locale
 * @package Experius\MissingTranslations\Model\Config\Source
 */
class Locale implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Locale constructor.
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig
    ) {
    
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->getLocaleMapping();
    }

    /**
     * Retrieve an array of enabled (configuration) locales
     *
     * @return array
     */
    public function getLocaleMapping()
    {
        $stores = $this->storeManager->getStores();

        $mapping = [];

        foreach ($stores as $store) {
            $locale = $this->scopeConfig->getValue(
                'general/locale/code',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getStoreId()
            );
            $mapping[$locale] = [
                'label' => $locale,
                'value' => $locale
            ];
        }

        return $mapping;
    }
}
