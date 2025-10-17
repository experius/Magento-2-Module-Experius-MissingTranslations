<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Controller\Adminhtml\Ajax;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\App\ResourceConnection;

class Phrases extends Action
{
    const ADMIN_RESOURCE = 'Experius_MissingTranslations::Translation_view';

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        ResourceConnection $resourceConnection
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->resourceConnection = $resourceConnection;
        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        
        try {
            $localeToTranslate = $this->getRequest()->getParam('locale_to_translate');
            
            if (!$localeToTranslate) {
                return $resultJson->setData([]);
            }

            $connection = $this->resourceConnection->getConnection();
            $tableName = $this->resourceConnection->getTableName('translation');
            
            $select = $connection->select()
                ->from($tableName, ['string', 'translate', 'store_id', 'locale'])
                ->where('locale = ?', $localeToTranslate)
                ->where('(translate IS NULL OR translate = "" OR translate = string)');
                
            $results = $connection->fetchAll($select);
            
            $data = [];
            foreach ($results as $row) {
                $data[] = [
                    $row['string'],
                    $row['translate'],
                    $row['store_id'],
                    $row['locale']
                ];
            }
            
            return $resultJson->setData($data);
            
        } catch (\Exception $e) {
            return $resultJson->setData([]);
        }
    }
}
