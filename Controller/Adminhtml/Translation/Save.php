<?php
/**
 * A Magento 2 module named Experius/MissingTranslations
 * Copyright (C) 2018 Experius
 *
 * This file is part of Experius/MissingTranslations.
 *
 * Experius/MissingTranslations is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Experius\MissingTranslations\Controller\Adminhtml\Translation;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class Save
 * @package Experius\MissingTranslations\Controller\Adminhtml\Translation
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var \Experius\MissingTranslations\Model\TranslationFactory
     */
    protected $translationFactory;

    /**
     * @var \Experius\MissingTranslations\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;
    /**
     * @array
     */
    protected $phrases;

    /**
     * @var \Magento\Translation\Model\Inline\CacheManager
     */
    protected $cacheManager;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Experius\MissingTranslations\Model\TranslationFactory $translationFactory
     * @param \Experius\MissingTranslations\Helper\Data $helper
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Experius\MissingTranslations\Model\TranslationFactory $translationFactory,
        \Experius\MissingTranslations\Helper\Data $helper,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Locale\ResolverInterface $localeResolver
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->translationFactory = $translationFactory;
        $this->helper = $helper;
        $this->authSession = $authSession;
        $this->localeResolver = $localeResolver;

        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /**
         * Set session locale back to user interface locale to prevent interface language change after post
         */
        $userLocale = $this->authSession->getUser()->getInterfaceLocale();
        $sessionLocale = $this->_getSession()->getData('session_locale');

        if ($userLocale && $userLocale !== $sessionLocale) {
            $this->_getSession()->setData('session_locale', $userLocale);
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('key_id');

            $model = $this->translationFactory->create()->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addError(__('This Translation no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }

            if (!$model->getId() && !$id) {
                $locale = $data['locale'];

                $this->phrases = $this->helper->getPhrases($locale);

                $line = $data['string'];
                if (key_exists($line, $this->phrases)) {
                    $data['string'] = $this->phrases[$line][0];
                    $this->helper->removeFromFile($line, $locale);
                }
            }

            $data['different'] = 1;
            if (isset($data['string']) && isset($data['translate'])
                && $data['string'] == $data['translate']
            ) {
                $data['different'] = 0;
            }

            $model->setData($data);

            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the Translation.'));
                $this->dataPersistor->clear('experius_missingtranslations_translation');

                $this->helper->updateJsTranslationJsonFiles($data['locale']);

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['key_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Translation.'));
            }

            $this->dataPersistor->set('experius_missingtranslations_translation', $data);
            return $resultRedirect->setPath('*/*/edit', ['key_id' => $this->getRequest()->getParam('key_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
