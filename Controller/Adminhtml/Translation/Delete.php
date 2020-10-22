<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Translation;

class Delete extends \Experius\MissingTranslations\Controller\Adminhtml\Translation
{
    const ADMIN_RESOURCE = 'Experius_MissingTranslations::Translation_delete';

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('key_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Experius\MissingTranslations\Model\Translation');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccess(__('You deleted the Translation.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addError($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['key_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addError(__('We can\'t find a Translation to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
