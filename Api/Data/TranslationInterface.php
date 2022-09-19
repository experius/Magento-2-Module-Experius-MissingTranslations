<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Api\Data;

interface TranslationInterface
{

    const KEY_ID = 'key_id';
    const TRANSLATION_ID = 'translation_id';


    /**
     * Get translation_id
     * @return int|null
     */
    public function getTranslationId(): ?int;

    /**
     * Set translation_id
     * @param int $translationId
     * @return TranslationInterface
     */
    public function setTranslationId(int $translationId): self;

    /**
     * Get key_id
     * @return int|null
     */
    public function getKeyId(): ?int;

    /**
     * Set translation_id
     * @param int $key_id
     * @return TranslationInterface
     */
    public function setKeyId(int $key_id): self;
}
