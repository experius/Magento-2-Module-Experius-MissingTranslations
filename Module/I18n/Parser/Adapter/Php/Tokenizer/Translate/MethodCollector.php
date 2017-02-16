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
namespace Experius\MissingTranslations\Module\I18n\Parser\Adapter\Php\Tokenizer\Translate;

use Experius\MissingTranslations\Module\I18n\Parser\Adapter\Php\Tokenizer\PhraseCollector;

/**
 * MethodCollector
 */
class MethodCollector extends PhraseCollector
{
    /**
     * Extract phrases from given tokens. e.g.: __('phrase', ...)
     *
     * @return void
     */
    protected function _extractPhrases()
    {
        $token = $this->_tokenizer->getNextRealToken();
        if ($token && $token->isObjectOperator()) {
            $phraseStartToken = $this->_tokenizer->getNextRealToken();
            if ($phraseStartToken && $this->_isTranslateFunction($phraseStartToken)) {
                $arguments = $this->_tokenizer->getFunctionArgumentsTokens();
                $phrase = $this->_collectPhrase(array_shift($arguments));
                $this->_addPhrase($phrase, count($arguments), $this->_file, $phraseStartToken->getLine());
            }
        }
    }

    /**
     * Check if token is translated function
     *
     * @param \Experius\MissingTranslations\Module\I18n\Parser\Adapter\Php\Tokenizer\Token $token
     * @return bool
     */
    protected function _isTranslateFunction($token)
    {
        $nextToken = $this->_tokenizer->getNextRealToken();
        return $nextToken && $token->isEqualFunction('__') && $nextToken->isOpenBrace();
    }
}
