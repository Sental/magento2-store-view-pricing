<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MagePsycho\StorePricing\Model\Indexer;

use Magento\Framework\Indexer\Dimension;
use Magento\Store\Model\ResourceModel\Store\CollectionFactory as StoreCollectionFactory;
use Magento\Framework\Indexer\DimensionFactory;
use Magento\Framework\Indexer\DimensionProviderInterface;
use Magento\Store\Model\Store;

class StoreDimensionProvider implements DimensionProviderInterface
{
    /**
     * Name for store dimension for multidimensional indexer
     * 'ws' - stands for 'store_view'
     */
    const DIMENSION_NAME = 'sv';

    /**
     * @var StoreCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var \SplFixedArray
     */
    private $storesDataIterator;

    /**
     * @var DimensionFactory
     */
    private $dimensionFactory;

    /**
     * @param StoreCollectionFactory $collectionFactory
     * @param DimensionFactory $dimensionFactory
     */
    public function __construct(StoreCollectionFactory $collectionFactory, DimensionFactory $dimensionFactory)
    {
        $this->dimensionFactory = $dimensionFactory;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @return Dimension[]|\Traversable
     */
    public function getIterator(): \Traversable
    {
        foreach ($this->getStores() as $store) {
            yield $this->dimensionFactory->create(self::DIMENSION_NAME, (string)$store);
        }
    }

    /**
     * @return array
     */
    private function getStores(): array
    {
        if ($this->storesDataIterator === null) {
            $stores = $this->collectionFactory->create()
                ->addFieldToFilter('code', ['neq' => Store::ADMIN_CODE])
                ->getAllIds();
            $this->storesDataIterator = is_array($stores) ? $stores : [];
        }

        return $this->storesDataIterator;
    }
}
