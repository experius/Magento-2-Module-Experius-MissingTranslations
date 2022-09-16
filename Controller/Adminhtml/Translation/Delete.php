<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Translation;

use Experius\MissingTranslations\Model\TranslationRepository;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;

class Delete extends \Experius\MissingTranslations\Controller\Adminhtml\Translation
{
    const ADMIN_RESOURCE = 'Experius_MissingTranslations::Translation_delete';

    /**
     * @var TranslationRepository
     */
    protected TranslationRepository $translationRepository;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param TranslationRepository $translationRepository
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        TranslationRepository $translationRepository
    ) {
        parent::__construct($context, $coreRegistry);
        $this->translationRepository = $translationRepository;
    }

    /**
     * Delete action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('key_id');
        if ($id) {
            try {
                $this->translationRepository->deleteById((int)$id);
                $this->messageManager->addSuccessMessage(__('You deleted the Translation.'));
                return $resultRedirect->setPath('*/*/');
            } catch (CouldNotDeleteException|NoSuchEntityException $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['key_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Translation to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
