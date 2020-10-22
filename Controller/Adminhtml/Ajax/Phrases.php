<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Ajax;

class Phrases extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $jsonHelper;
    protected $urlBuilder;
    protected $helper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Experius\MissingTranslations\Helper\Data $helper,
        \Magento\Backend\Model\UrlInterface $urlBuilder
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context);
    }


    public function execute()
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
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            return $this->jsonResponse($e->getMessage());
        }
    }


    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
