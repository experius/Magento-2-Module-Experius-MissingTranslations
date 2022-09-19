<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Model\ResourceModel\Translation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            'Experius\MissingTranslations\Model\Translation',
            'Experius\MissingTranslations\Model\ResourceModel\Translation'
        );
    }
}
