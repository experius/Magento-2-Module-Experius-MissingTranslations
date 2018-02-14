<?php
/**
 * A Magento 2 module named Experius/MissingTranslations
 * Copyright (C) 2018 Experius
 *
 * This file is part of Experius/MissingTranslations.
 *
 * Experius/MissingTranslations is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

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
    )
    {
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

        foreach($stores as $store) {
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