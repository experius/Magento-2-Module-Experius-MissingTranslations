<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Ajax;

use Experius\MissingTranslations\Helper\Data;
use Magento\Backend\App\Action;
use Magento\Backend\Model\UrlInterface;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Result\PageFactory;

class Phrases extends Action
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var Json
     */
    protected Json $jsonHelper;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var Data
     */
    protected Data $helper;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param Json $jsonHelper
     * @param Data $helper
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Json $jsonHelper,
        Data $helper,
        UrlInterface $urlBuilder
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context);
    }


    public function execute(): ResponseInterface
    {
        try {
            $phrases = $this->helper->getPhrases($this->getRequest()->getParam('locale'));

            $options = [];
            foreach ($phrases as $line => $string) {
                if (key_exists(1, $string) && $string[1] == '') {
                    $options[$line] = $string[0];
                }
            }

            return $this->jsonResponse($phrases);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(__("Failed to get Phrases : %1", [$e->getMessage()]));
            return $this->jsonResponse([]);
        }
    }


    public function jsonResponse(array $response = []): ResponseInterface
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->serialize($response)
        );
    }
}
