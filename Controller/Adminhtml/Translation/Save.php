<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Translation;

use Experius\MissingTranslations\Api\TranslationRepositoryInterface;
use Experius\MissingTranslations\Helper\Data;
use Experius\MissingTranslations\Model\TranslationFactory;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Translation\Model\Inline\CacheManager;

/**
 * Class Save
 * @package Experius\MissingTranslations\Controller\Adminhtml\Translation
 */
class Save extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Experius_MissingTranslations::Translation_save';

    /**
     * @var DataPersistorInterface
     */
    protected DataPersistorInterface $dataPersistor;

    /**
     * @var TranslationRepositoryInterface
     */
    protected TranslationRepositoryInterface $translationRepository;

    /**
     * @var TranslationFactory
     */
    protected TranslationFactory $translationFactory;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * @var Session
     */
    protected Session $authSession;
    /**
     * @array
     */
    protected array $phrases;

    /**
     * @var CacheManager
     */
    protected CacheManager $cacheManager;

    /**
     * @var ResolverInterface
     */
    protected ResolverInterface $localeResolver;

    /**
     * Save constructor.
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param TranslationRepositoryInterface $translationRepository
     * @param TranslationFactory $translationFactory
     * @param Data $helper
     * @param Session $authSession
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        TranslationRepositoryInterface $translationRepository,
        TranslationFactory $translationFactory,
        Data $helper,
        Session $authSession
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->translationRepository = $translationRepository;
        $this->translationFactory = $translationFactory;
        $this->helper = $helper;
        $this->authSession = $authSession;
        $this->localeResolver = $context->getLocaleResolver();

        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /**
         * Set session locale back to user interface locale to prevent interface language change after post
         */
        $userLocale = $this->authSession->getUser()->getInterfaceLocale();
        $sessionLocale = $this->_getSession()->getData('session_locale');

        if ($userLocale && $userLocale !== $sessionLocale) {
            $this->_getSession()->setData('session_locale', $userLocale);
        }

        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('key_id');

            if ($id) {
                try {
                    $model = $this->translationRepository->getById((int)$id);
                }  catch (NoSuchEntityException $e) {
                    $this->messageManager->addErrorMessage(__('This Translation no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }

                //Identify a request coming from /duplicate by submitted store_id not matching to the model's store_id
                //This also means that if you duplicate a translation but don't change the store_id it will act as if it was an edit
                if($this->getRequest()->getParam('store_id') != $model->getData('store_id')) {
                    $model = $this->translationFactory->create();
                    unset($data['key_id']);
                }

            } else {
                $model = $this->translationFactory->create();
            }

            $data['different'] = 1;
            if (isset($data['string']) && isset($data['translate'])
                && $data['string'] == $data['translate']
            ) {
                $data['different'] = 0;
            }

            $model->setData($data);

            try {
                $this->translationRepository->save($model);
                if ($model->isObjectNew()) {
                    $this->helper->removeFromFile($data['string'], $data['locale']);
                }
                $this->messageManager->addSuccessMessage(__('You saved the Translation.'));
                $this->dataPersistor->clear('experius_missingtranslations_translation');

                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['key_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (CouldNotSaveException $e) {
                $this->messageManager->addError("You cannot have 2 translations set for the same string on the same store ID");
                return $resultRedirect->setPath('*/*/duplicate', ['key_id' => $this->getRequest()->getParam('key_id')]);
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
