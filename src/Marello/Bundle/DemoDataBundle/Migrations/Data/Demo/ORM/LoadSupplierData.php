<?php

namespace Marello\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Marello\Bundle\AddressBundle\Entity\MarelloAddress;
use Marello\Bundle\SupplierBundle\Entity\Supplier;

class LoadSupplierData extends AbstractFixture
{
    /** @var ObjectManager $manager */
    protected $manager;

    /**
     * @var array
     */
    protected $data = [
        ['name' => 'Supplier 1', 'priority' => 1, 'can_dropship' => true, 'is_active' => true, 'address'=> ['street_address' => 'Street name 1', 'zipcode' => 12345, 'city'=> 'Amsterdam', 'country'=> 'NL', 'state' => 'NB']],
        ['name' => 'Supplier 2', 'priority' => 2, 'can_dropship' => true, 'is_active' => true, 'address'=> ['street_address' => 'Street name 2', 'zipcode' => 67890, 'city'=> 'Eindhoven', 'country'=> 'NL', 'state'=> 'NB']],
        ['name' => 'Supplier 3', 'priority' => 9, 'can_dropship' => false, 'is_active' => true, 'address'=> ['street_address' => 'Street name 3', 'zipcode' => 454545, 'city'=> 'London', 'country'=> 'GB']],
    ];

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadSuppliers();
    }

    /**
     * load and create Suppliers
     */
    protected function loadSuppliers()
    {
        $i = 0;

        foreach ($this->data as $values) {
            $supplier = new Supplier();
            $supplier->setName($values['name']);
            $supplier->setPriority($values['priority']);
            $supplier->setCanDropship($values['can_dropship']);
            $supplier->setIsActive($values['is_active']);

            $address = new MarelloAddress();
            $address->setStreet($values['address']['street_address']);
            $address->setPostalCode($values['address']['zipcode']);
            $address->setCity($values['address']['city']);
            $address->setCountry(
                $this->manager
                    ->getRepository('OroAddressBundle:Country')->find($values['address']['country'])
            );
            $address->setRegion(
                $this->manager
                    ->getRepository('OroAddressBundle:Region')
                    ->findOneBy(['combinedCode' => $values['address']['country'] . '-' . $values['address']['state']])
            );
            $this->manager->persist($address);
            
            $supplier->setAddress($address);
            $this->manager->persist($supplier);
            $this->setReference('marello_supplier_' . $i, $supplier);
            $i++;
        }

        $this->manager->flush();
    }
}
