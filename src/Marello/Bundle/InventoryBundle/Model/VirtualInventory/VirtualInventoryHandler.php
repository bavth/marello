<?php

namespace Marello\Bundle\InventoryBundle\Model\VirtualInventory;

use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\OrganizationBundle\Entity\OrganizationAwareInterface;
use Oro\Bundle\OrganizationBundle\Entity\OrganizationInterface;

use Marello\Bundle\InventoryBundle\Entity\VirtualInventoryLevel;
use Marello\Bundle\ProductBundle\Entity\Product;
use Marello\Bundle\ProductBundle\Entity\ProductInterface;
use Marello\Bundle\SalesBundle\Entity\SalesChannelGroup;
use Marello\Bundle\InventoryBundle\Entity\Repository\VirtualInventoryRepository;
use Marello\Bundle\InventoryBundle\Model\VirtualInventory\VirtualInventoryFactory;

class VirtualInventoryHandler
{
    /** @var VirtualInventoryFactory $virtualInventoryFactory */
    protected $virtualInventoryFactory;

    /** @var ObjectManager $objectManager */
    protected $objectManager;

    /** @var VirtualInventoryRepository $virtualInventoryRepository */
    protected $virtualInventoryRepository;

    /**
     * @param ObjectManager $objectManager
     * @param VirtualInventoryFactory $virtualInventoryFactory
     * @param VirtualInventoryRepository $virtualInventoryRepository
     */
    public function __construct(
        ObjectManager $objectManager,
        VirtualInventoryFactory $virtualInventoryFactory,
        VirtualInventoryRepository $virtualInventoryRepository
    ) {
        $this->objectManager = $objectManager;
        $this->virtualInventoryRepository = $virtualInventoryRepository;
        $this->virtualInventoryFactory = $virtualInventoryFactory;
    }

    /**
     * @param Product $product
     * @param SalesChannelGroup $group
     * @param $inventoryQty
     * @return VirtualInventoryLevel
     */
    public function createVirtualInventory(Product $product, SalesChannelGroup $group, $inventoryQty)
    {
        return $this->virtualInventoryFactory->create($product, $group, $inventoryQty);
    }

    /**
     * Save virtual inventory
     * @param VirtualInventoryLevel $level
     * @param bool $force
     */
    public function saveVirtualInventory(VirtualInventoryLevel $level, $force = false)
    {
        $existingLevel = $this->findExistingVirtualInventory($level->getProduct(), $level->getSalesChannelGroup());

        if ($existingLevel) {
            if (!$this->isLevelChanged($existingLevel, $level) && !$force) {
                return;
            }

            $existingLevel->setInventory($level->getInventory());
            $level = $existingLevel;
        }

        if (!$level->getOrganization()) {
            $organization = $this->getOrganization($level->getProduct());
            $level->setOrganization($organization);
        }

        $this->objectManager->persist($level);
        $this->objectManager->flush();
    }

    /**
     * Find existing VirtualInventoryLevel
     * @param ProductInterface $product
     * @param SalesChannelGroup $group
     * @return VirtualInventoryLevel|object
     */
    public function findExistingVirtualInventory(ProductInterface $product, SalesChannelGroup $group)
    {
        /** @var VirtualInventoryRepository $repository */
        $repository = $this->objectManager->getRepository(VirtualInventoryLevel::class);
        return $repository->findExistingVirtualInventory($product, $group);
    }

    /**
     * Check whether the existing level has changed inventory
     * @param VirtualInventoryLevel $existingLevel
     * @param VirtualInventoryLevel $level
     * @return bool
     */
    private function isLevelChanged($existingLevel, $level)
    {
        return ((float)$existingLevel->getInventory() !== (float)$level->getInventory());
    }

    /**
     * Get organization from entity
     * @param OrganizationAwareInterface $entity
     * @return OrganizationInterface
     */
    private function getOrganization(OrganizationAwareInterface $entity)
    {
        return $entity->getOrganization();
    }
}
