<?php

namespace Marello\Bundle\ReturnBundle\Form\Type;

use Marello\Bundle\ReturnBundle\Validator\Constraints\ReturnRorWarrantyConstraint;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Marello\Bundle\ReturnBundle\Entity\ReturnEntity;
use Marello\Bundle\ReturnBundle\Form\Subscriber\ReturnTypeSubscriber;
use Marello\Bundle\ReturnBundle\Validator\Constraints\ReturnEntityConstraint;
use Marello\Bundle\ReturnBundle\Validator\Constraints\ReturnWarrantyConstraint;

class ReturnType extends AbstractType
{
    const NAME = 'marello_return';

    /** @var ReturnTypeSubscriber */
    protected $returnTypeSubscriber;

    /**
     * ReturnType constructor.
     *
     * @param ReturnTypeSubscriber $returnTypeSubscriber
     */
    public function __construct(ReturnTypeSubscriber $returnTypeSubscriber)
    {
        $this->returnTypeSubscriber = $returnTypeSubscriber;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
            'salesChannel',
            'genemu_jqueryselect2_entity',
            [
                'class' => 'MarelloSalesBundle:SalesChannel',
            ]
        );

        $builder->add('returnItems', ReturnItemCollectionType::NAME);

        $builder->addEventSubscriber($this->returnTypeSubscriber);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'  => ReturnEntity::class,
            'constraints' => [
                new ReturnEntityConstraint(),
                new ReturnWarrantyConstraint(),
                new ReturnRorWarrantyConstraint()
            ],
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return self::NAME;
    }
}
