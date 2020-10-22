<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Experius\MissingTranslations\Module\I18n\Dictionary\Writer\Csv;

use Experius\MissingTranslations\Module\I18n\Dictionary\Writer\Csv;

/**
 * Stdout writer
 *
 * Output csv format to stdout
 */
class Stdo extends Csv
{
    /**
     * Writer construct
     */
    public function __construct()
    {
        $this->_fileHandler = STDOUT;
    }
}
