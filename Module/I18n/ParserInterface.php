<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Module\I18n;

/**
 * Parser Interface
 */
interface ParserInterface
{
    /**
     * Parse by parser options
     *
     * @param array $parseOptions
     * @return array
     */
    public function parse(array $parseOptions);

    /**
     * Get parsed phrases
     *
     * @return array
     */
    public function getPhrases();
}
