<?php

namespace Marello\Bundle\InventoryBundle\Manager;

use Marello\Bundle\InventoryBundle\Model\InventoryUpdateContext;

interface InventoryManagerInterface
{
    /**
     * @deprecated use updateInventoryLevels instead
     * @param InventoryUpdateContext $context
     */
    public function updateInventoryItems(InventoryUpdateContext $context);

    /**
     * @param InventoryUpdateContext $context
     */
    public function updateInventoryLevels(InventoryUpdateContext $context);
}
