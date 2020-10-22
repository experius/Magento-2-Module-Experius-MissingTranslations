<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Model;

use Experius\MissingTranslations\Api\Data\TranslationInterface;

class Translation extends \Magento\Framework\Model\AbstractModel implements TranslationInterface
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Experius\MissingTranslations\Model\ResourceModel\Translation');
    }

    /**
     * Get translation_id
     * @return string
     */
    public function getTranslationId()
    {
        return $this->getData(self::TRANSLATION_ID);
    }

    /**
     * Set translation_id
     * @param string $translationId
     * @return Experius\MissingTranslations\Api\Data\TranslationInterface
     */
    public function setTranslationId($translationId)
    {
        return $this->setData(self::TRANSLATION_ID, $translationId);
    }

    /**
     * Get key_id
     * @return string
     */
    public function getKeyId()
    {
        return $this->getData(self::KEY_ID);
    }

    /**
     * Set key_id
     * @param string $key_id
     * @return Experius\MissingTranslations\Api\Data\TranslationInterface
     */
    public function setKeyId($key_id)
    {
        return $this->setData(self::KEY_ID, $key_id);
    }
}
