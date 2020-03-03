<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Communication\Form;

use Generated\Shared\Transfer\ProductRelationTransfer;
use Generated\Shared\Transfer\ProductRelationTypeTransfer;
use Spryker\Zed\Kernel\Communication\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Required;

/**
 * @method \Spryker\Zed\ProductRelationGui\Communication\ProductRelationGuiCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductRelationGui\ProductRelationGuiConfig getConfig()
 */
class ProductRelationFormType extends AbstractType
{
    public const FIELD_RELATION_TYPE = 'productRelationType';
    public const FIELD_FK_PRODUCT_ABSTRACT = 'fkProductAbstract';
    public const FIELD_ID_PRODUCT_RELATION = 'idProductRelation';
    public const FIELD_QUERY_SET = 'querySet';
    public const FIELD_IS_REBUILD_SCHEDULED = 'isRebuildScheduled';
    public const FIELD_STORE_RELATION = 'storeRelation';
    public const FIELD_PRODUCT_RELATION_KEY = 'productRelationKey';

    public const OPTION_RELATION_CHOICES = 'productRelationType';

    public const GROUP_AFTER = 'After';
    public const GROUP_DEFAULT = 'Default';

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addRelationTypeField($builder, $options)
            ->addProductRelationKeyField($builder, $options)
            ->addIdProductRelationField($builder)
            ->addQuerySetField($builder)
            ->addUpdateWithSchedulerField($builder)
            ->addFkProductAbstractField($builder)
            ->addStoreRelationForm($builder);
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     *
     * @return void
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(static::OPTION_RELATION_CHOICES);

        $resolver->setDefaults([
            'validation_groups' => new GroupSequence([
                static::GROUP_DEFAULT,
                static::GROUP_AFTER,
            ]),
            'constraints' => [
                $this->getFactory()->createUniqueRelationTypeForProductAbstractConstraint(),
            ],
        ]);
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addQuerySetField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_QUERY_SET, HiddenType::class, [
            'constraints' => [
                new NotBlank(['message' => 'Query not defined.']),
            ],
        ]);

        $builder->get(static::FIELD_QUERY_SET)
            ->addModelTransformer($this->getFactory()->createRuleSetTransformer());

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addUpdateWithSchedulerField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_IS_REBUILD_SCHEDULED, CheckboxType::class, [
            'label' => 'Update regularly: When you have this selected, this product relation will be updated automatically on regular basis according to the relation\'s conditions.',

        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addIdProductRelationField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_ID_PRODUCT_RELATION, HiddenType::class);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addRelationTypeField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_RELATION_TYPE, ChoiceType::class, [
            'label' => 'Relation type',
            'property_path' => ProductRelationTransfer::PRODUCT_RELATION_TYPE . '.' . ProductRelationTypeTransfer::KEY,
            'choices' => array_flip($options[static::OPTION_RELATION_CHOICES]),
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addFkProductAbstractField(FormBuilderInterface $builder)
    {
        $builder->add(static::FIELD_FK_PRODUCT_ABSTRACT, TextType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Abstract product is not selected.',
                    'groups' => [
                        static::GROUP_DEFAULT,
                    ],
                ]),
            ],
        ]);

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     *
     * @return $this
     */
    protected function addStoreRelationForm(FormBuilderInterface $builder)
    {
        $builder->add(
            static::FIELD_STORE_RELATION,
            $this->getFactory()->getStoreRelationFormTypePlugin()->getType(),
            [
                'label' => false,
                'required' => false,
            ]
        );

        return $this;
    }

    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array $options
     *
     * @return $this
     */
    protected function addProductRelationKeyField(FormBuilderInterface $builder, array $options)
    {
        $builder->add(static::FIELD_PRODUCT_RELATION_KEY, TextType::class, [
            'label' => 'Delivery Method Key',
//            'disabled' => $options[ShipmentMethodFormDataProvider::OPTION_DELIVERY_KEY_DISABLED],
            'constraints' => [
                new NotBlank(),
                new Required(),
//                $this->getFactory()->createShipmentMethodKeyUniqueConstraint(),
            ],
        ]);

        return $this;
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'product_relation';
    }
}
