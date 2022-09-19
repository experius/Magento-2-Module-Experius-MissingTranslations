<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Translation;

use Experius\MissingTranslations\Helper\Data;
use Experius\MissingTranslations\Model\TranslationFactory;
use Experius\MissingTranslations\Model\TranslationRepository;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class InlineEdit
 * @package Experius\MissingTranslations\Controller\Adminhtml\Translation
 */
class InlineEdit extends Action
{
    const ADMIN_RESOURCE = 'Experius_MissingTranslations::Translation_update';

    /**
     * @var JsonFactory
     */
    protected JsonFactory $jsonFactory;

    /**
     * @var TranslationRepository
     */
    protected TranslationRepository $translationRepository;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * InlineEdit constructor.
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param TranslationRepository $translationRepository
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        TranslationRepository $translationRepository,
        Data $helper
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->translationRepository = $translationRepository;
        $this->helper = $helper;
    }

    /**
     * Inline edit action
     *
     * @return ResultInterface
     */
    public function execute(): ResultInterface
    {
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
                    try {
                        $model = $this->translationRepository->getById((int) $modelId);
                    } catch (NoSuchEntityException $e) {
                        $messages[] = "[Translation ID: {$modelId}]  {$e->getMessage()}";
                        $error = true;
                        continue;
                    }

                    $data = $model->getData();
                    $data['different'] = 1;
                    if (isset($data['string']) && isset($data['translate'])
                        && $data['string'] == $data['translate']
                    ) {
                        $data['different'] = 0;
                    }

                    try {
                        $model->setData(array_merge($data, $postItems[$modelId]));
                        $this->translationRepository->save($model);

                        $this->helper->updateJsTranslationJsonFiles($data['locale']);
                    } catch (LocalizedException $e) {
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
