<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Translation;

/**
 * Class InlineEdit
 * @package Experius\MissingTranslations\Controller\Adminhtml\Translation
 */
class InlineEdit extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Experius_MissingTranslations::Translation_update';
    
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Experius\MissingTranslations\Model\TranslationFactory
     */
    protected $translationFactory;

    /**
     * @var \Experius\MissingTranslations\Helper\Data
     */
    protected $helper;
    /**
     * InlineEdit constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Experius\MissingTranslations\Model\TranslationFactory $translationFactory
     * @param \Experius\MissingTranslations\Helper\Data $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Experius\MissingTranslations\Model\TranslationFactory $translationFactory,
        \Experius\MissingTranslations\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->translationFactory = $translationFactory;
        $this->helper = $helper;
    }

    /**
     * Inline edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        
        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach (array_keys($postItems) as $modelId) {
                    $model = $this->translationFactory->create()->load($modelId);
                    $data = $model->getData();
                    $data['different'] = 1;
                    if (isset($data['string']) && isset($data['translate'])
                        && $data['string'] == $data['translate']
                    ) {
                        $data['different'] = 0;
                    }

                    try {
                        $model->setData(array_merge($data, $postItems[$modelId]));
                        $model->save();

                        $this->helper->updateJsTranslationJsonFiles($data['locale']);
                    } catch (\Exception $e) {
                        $messages[] = "[Translation ID: {$modelId}]  {$e->getMessage()}";
                        $error = true;
                    }
                }
            }
        }
        
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
