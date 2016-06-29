<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Smile\Offer\Model\ResourceModel\Offer;


use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Smile\Offer\Model\Offer', 'Smile\Offer\Model\ResourceModel\Offer');
    }
}
