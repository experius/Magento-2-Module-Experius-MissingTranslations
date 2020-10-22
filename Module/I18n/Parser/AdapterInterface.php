<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Module\I18n\Parser;

/**
 * Adapter Interface
 */
interface AdapterInterface
{
    /**
     * Parse file
     *
     * @param string $file
     * @return array
     */
    public function parse($file);

    /**
     * Get parsed phrases
     *
     * @return array
     */
    public function getPhrases();
}
