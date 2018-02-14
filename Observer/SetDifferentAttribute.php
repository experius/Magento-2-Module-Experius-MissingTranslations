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

namespace Experius\MissingTranslations\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetDifferentAttribute
 * @package Experius\MissingTranslations\Observer
 */
class SetDifferentAttribute implements ObserverInterface
{
    /**
     * Set the current date to Special Price From attribute if it empty
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var  $product \Magento\Catalog\Model\Product */
        $translation = $observer->getEvent()->getTranslation();
        var_dump($translation);
        die();
        if ($product->getSpecialPrice() && !$product->getSpecialFromDate()) {
            $product->setData('special_from_date', $this->localeDate->date());
        }

        return $this;
    }
}
