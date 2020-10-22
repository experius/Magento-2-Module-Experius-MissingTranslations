<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);


namespace Experius\MissingTranslations\Module\I18n\Dictionary\Loader\File;

use Experius\MissingTranslations\Module\I18n\Dictionary;

/**
 *  Dictionary loader from csv
 */
class Csv extends AbstractFile
{
    /**
     * {@inheritdoc}
     */
    protected function _readFile()
    {
        return fgetcsv($this->_fileHandler, null, ',', '"');
    }
}
