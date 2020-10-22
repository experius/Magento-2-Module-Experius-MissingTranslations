<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Experius\MissingTranslations\Module\I18n\Dictionary\Options;

/**
 * Generator options resolver interface
 */
interface ResolverInterface
{
    /**
     * @return array
     */
    public function getOptions();
}
