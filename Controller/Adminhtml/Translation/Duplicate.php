<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Translation;

use Experius\MissingTranslations\Controller\Adminhtml\Translation;
use Experius\MissingTranslations\Model\TranslationRepository;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

class Duplicate extends Translation
{
    const ADMIN_RESOURCE = 'Experius_MissingTranslations::Translation_update';

    /**
     * @var TranslationRepository
     */
    protected TranslationRepository $translationRepository;

    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param TranslationRepository $translationRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        TranslationRepository $translationRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context, $coreRegistry);
        $this->translationRepository = $translationRepository;
        $this->resultPageFactory = $resultPageFactory;
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
                return $this->returnError();
            }
        } else {
            return $this->returnError();
        }
        $this->_coreRegistry->register('experius_missingtranslations_translation', $model);

        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            __('Duplicate Translation'),
            __('Duplicate Translation')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Duplicate Translation'));
        return $resultPage;
    }

    /**
     * @return Redirect
     */
    protected function returnError(): Redirect {
        $this->messageManager->addErrorMessage(__('This Translation no longer exists.'));
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
