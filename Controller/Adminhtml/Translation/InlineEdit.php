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

/**
 * Class InlineEdit
 * @package Experius\MissingTranslations\Controller\Adminhtml\Translation
 */
class InlineEdit extends \Magento\Backend\App\Action
{
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