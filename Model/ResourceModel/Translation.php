<?php
/**
 * Collect missing translations in specified folder or the entire Magento 2 Root
 * Copyright (C) 2016 Lewis Voncken
 * 
 * This file included in Experius/MissingTranslations is licensed under OSL 3.0
 * 
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Experius\MissingTranslations\Model\ResourceModel;

class Translation extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('translation', 'key_id');
    }
}
