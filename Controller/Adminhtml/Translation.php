<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;

abstract class Translation extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Experius_MissingTranslations::Translation';

    /**
     * @var Registry
     */
    protected Registry $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Init page
     *
     * @param Page $resultPage
     * @return Page
     */
    public function initPage(Page $resultPage): Page
    {
        $resultPage->setActiveMenu('Experius::experius_translation')
            ->addBreadcrumb(__('Experius'), __('Experius'))
            ->addBreadcrumb(__('Translation'), __('Translation'));
        return $resultPage;
    }
}
