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

namespace Experius\MissingTranslations\Controller\Adminhtml\Translation;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @array
     */
    protected $phrases;

    /**
     * @var \Experius\MissingTranslations\Helper\Data]
     */
    protected $helper;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Experius\MissingTranslations\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Experius\MissingTranslations\Helper\Data $helper
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->helper = $helper;


        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('key_id');
        
            $model = $this->_objectManager->create('Experius\MissingTranslations\Model\Translation')->load($id);
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

            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccess(__('You saved the Translation.'));
                $this->dataPersistor->clear('experius_missingtranslations_translation');
        
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
