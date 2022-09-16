<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Translation;

use Experius\MissingTranslations\Controller\Adminhtml\Translation;
use Experius\MissingTranslations\Model\TranslationFactory;
use Experius\MissingTranslations\Model\TranslationRepository;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Translate extends Translation
{
    const ADMIN_RESOURCE = 'Experius_MissingTranslations::Translation_view';

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var TranslationRepository
     */
    protected TranslationRepository $translationRepository;

    /**
     * @var TranslationFactory
     */
    protected TranslationFactory $translationFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param TranslationRepository $translationRepository
     * @param TranslationFactory $translationFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory $resultPageFactory,
        TranslationRepository $translationRepository,
        TranslationFactory $translationFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->resultPageFactory = $resultPageFactory;
        $this->translationRepository = $translationRepository;
        $this->translationFactory =$translationFactory;
    }

    /**
     * Edit action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        $id = $this->getRequest()->getParam('key_id');

        if ($id) {
            try {
                $model = $this->translationRepository->getById((int) $id);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This Translation no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        } else {
            $model = $this->translationFactory->create();
        }
        $this->_coreRegistry->register('experius_missingtranslations_translation', $model);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            __('Add Translation'),
            __('Add Translation')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Add Translation'));
        return $resultPage;
    }
}
