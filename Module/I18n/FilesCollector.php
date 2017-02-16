<?php
/**
 * Collect missing translations in specified folder or the entire Magento 2 Root
 * Copyright (C) 2016 Lewis Voncken
 *
 * This file included in Experius/MissingTranslations is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */
namespace Experius\MissingTranslations\Module\I18n;

/**
 *  Files collector
 */
class FilesCollector
{
    /**
     * Get files
     *
     * @param array $paths
     * @param bool $fileMask
     * @return array
     */
    public function getFiles(array $paths, $fileMask = false)
    {
        $files = [];
        foreach ($paths as $path) {
            foreach ($this->_getIterator($path, $fileMask) as $file) {
                $files[] = (string)$file;
            }
        }
        sort($files);
        return $files;
    }

    /**
     * Get files iterator
     *
     * @param string $path
     * @param bool $fileMask
     * @return \RecursiveIteratorIterator|\RegexIterator
     * @throws \InvalidArgumentException
     */
    protected function _getIterator($path, $fileMask = false)
    {
        try {
            $directoryIterator = new \RecursiveDirectoryIterator(
                $path,
                \FilesystemIterator::SKIP_DOTS | \FilesystemIterator::UNIX_PATHS | \FilesystemIterator::FOLLOW_SYMLINKS
            );
            $iterator = new \RecursiveIteratorIterator($directoryIterator);
        } catch (\UnexpectedValueException $valueException) {
            throw new \InvalidArgumentException(sprintf('Cannot read directory for parse phrase: "%s".', $path));
        }
        if ($fileMask) {
            $iterator = new \RegexIterator($iterator, $fileMask);
        }
        return $iterator;
    }
}
