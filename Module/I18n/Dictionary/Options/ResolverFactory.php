<?php
/**
 * Collect missing translations in specified folder or the entire Magento 2 Root
 * Copyright (C) 2016 Experius
 *
 * This file included in Experius/MissingTranslations is licensed under OSL 3.0
 *
 * http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * Please see LICENSE.txt for the full text of the OSL 3.0 license
 */

namespace Experius\MissingTranslations\Module\I18n\Dictionary\Options;

use Magento\Framework\Component\ComponentRegistrar;

/**
 * Options resolver factory
 */
class ResolverFactory
{
    /**
     * Default option resolver class
     */
    const DEFAULT_RESOLVER = 'Experius\MissingTranslations\Module\I18n\Dictionary\Options\Resolver';

    /**
     * @var string
     */
    protected $resolverClass;

    /**
     * @param string $resolverClass
     */
    public function __construct($resolverClass = self::DEFAULT_RESOLVER)
    {
        $this->resolverClass = $resolverClass;
    }

    /**
     * @param string $directory
     * @param bool $withContext
     * @return ResolverInterface
     * @throws \InvalidArgumentException
     */
    public function create($directory, $withContext)
    {
        $resolver = new $this->resolverClass(new ComponentRegistrar(), $directory, $withContext);
        if (!$resolver instanceof ResolverInterface) {
            throw new \InvalidArgumentException($this->resolverClass . ' doesn\'t implement ResolverInterface');
        }
        return $resolver;
    }
}
