<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Translation extends AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('translation', 'key_id');
    }
}
