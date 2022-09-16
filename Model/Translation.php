<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Model;

use Experius\MissingTranslations\Api\Data\TranslationInterface;
use Magento\Framework\Model\AbstractModel;

class Translation extends AbstractModel implements TranslationInterface
{
    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init('Experius\MissingTranslations\Model\ResourceModel\Translation');
    }

    /**
     * Get translation_id
     * @return int
     */
    public function getTranslationId(): int
    {
        return $this->getData(self::TRANSLATION_ID);
    }

    /**
     * Set translation_id
     * @param int $translationId
     * @return TranslationInterface
     */
    public function setTranslationId(int $translationId): TranslationInterface
    {
        return $this->setData(self::TRANSLATION_ID, $translationId);
    }

    /**
     * Get key_id
     * @return int
     */
    public function getKeyId(): int
    {
        return $this->getData(self::KEY_ID);
    }

    /**
     * Set key_id
     * @param int $key_id
     * @return TranslationInterface
     */
    public function setKeyId(int $key_id): TranslationInterface
    {
        return $this->setData(self::KEY_ID, $key_id);
    }
}
