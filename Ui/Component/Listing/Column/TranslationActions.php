<?php
/**
 * Copyright © Experius B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Experius\MissingTranslations\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class TranslationActions
 * @package Experius\MissingTranslations\Ui\Component\Listing\Column
 */
class TranslationActions extends Column
{

    const URL_PATH_EDIT = 'experius_missingtranslations/translation/edit';
    const URL_PATH_DELETE = 'experius_missingtranslations/translation/delete';
    const URL_PATH_DUPLICATE = 'experius_missingtranslations/translation/duplicate';

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['key_id'])) {
                    $item[$this->getData('name')] = [
                        'edit' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_EDIT,
                                [
                                    'key_id' => $item['key_id']
                                ]
                            ),
                            'label' => __('Edit')
                        ],
                        'delete' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DELETE,
                                [
                                    'key_id' => $item['key_id']
                                ]
                            ),
                            'label' => __('Delete'),
                            'confirm' => [
                                'title' => __('Delete Translation'),
                                'message' => __('Are you sure you want to delete this translation?')
                            ]
                        ],
                        'duplicate' => [
                            'href' => $this->urlBuilder->getUrl(
                                static::URL_PATH_DUPLICATE,
                                [
                                    'key_id' => $item['key_id']
                                ]
                            ),
                            'label' => __('Duplicate'),
                        ]

                    ];
                }
            }
        }

        return $dataSource;
    }
}
