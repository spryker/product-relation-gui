<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Communication;

use Generated\Shared\Transfer\ProductRelationTransfer;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Orm\Zed\Product\Persistence\SpyProductAttributeKeyQuery;
use Orm\Zed\ProductRelation\Persistence\SpyProductRelationQuery;
use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use Spryker\Zed\Kernel\Communication\Form\FormTypeInterface;
use Spryker\Zed\ProductRelationGui\Communication\Form\Constraint\ProductAbstractNotBlankConstraint;
use Spryker\Zed\ProductRelationGui\Communication\Form\Constraint\ProductRelationKeyUniqueConstraint;
use Spryker\Zed\ProductRelationGui\Communication\Form\Constraint\UniqueProductRelationByProductAbstractAndRelationTypeAndStoresConstraint;
use Spryker\Zed\ProductRelationGui\Communication\Form\Constraint\UniqueRelationTypeForProductAbstractAndQuerySet;
use Spryker\Zed\ProductRelationGui\Communication\Form\DataProvider\ProductRelationTypeDataProvider;
use Spryker\Zed\ProductRelationGui\Communication\Form\ProductRelationDeleteForm;
use Spryker\Zed\ProductRelationGui\Communication\Form\ProductRelationFormType;
use Spryker\Zed\ProductRelationGui\Communication\Form\ProductRelationToggleIsActiveForm;
use Spryker\Zed\ProductRelationGui\Communication\Form\Transformer\RuleQuerySetTransformer;
use Spryker\Zed\ProductRelationGui\Communication\Provider\FilterProvider;
use Spryker\Zed\ProductRelationGui\Communication\Provider\FilterProviderInterface;
use Spryker\Zed\ProductRelationGui\Communication\Provider\MappingProvider;
use Spryker\Zed\ProductRelationGui\Communication\Provider\MappingProviderInterface;
use Spryker\Zed\ProductRelationGui\Communication\QueryCreator\RuleQueryCreator;
use Spryker\Zed\ProductRelationGui\Communication\QueryCreator\RuleQueryCreatorInterface;
use Spryker\Zed\ProductRelationGui\Communication\Table\ProductRelationTable;
use Spryker\Zed\ProductRelationGui\Communication\Table\ProductRuleTable;
use Spryker\Zed\ProductRelationGui\Communication\Table\ProductTable;
use Spryker\Zed\ProductRelationGui\Communication\Tabs\ProductRelationTabs;
use Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToLocaleFacadeInterface;
use Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToProductAttributeFacadeInterface;
use Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToProductFacadeInterface;
use Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToProductRelationFacadeInterface;
use Spryker\Zed\ProductRelationGui\Dependency\QueryContainer\ProductRelationGuiToPropelQueryBuilderQueryContainerInterface;
use Spryker\Zed\ProductRelationGui\Dependency\Service\ProductRelationGuiToUtilEncodingServiceInterface;
use Spryker\Zed\ProductRelationGui\ProductRelationGuiDependencyProvider;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraint;

/**
 * @method \Spryker\Zed\ProductRelationGui\ProductRelationGuiConfig getConfig()
 */
class ProductRelationGuiCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \Symfony\Component\Form\DataTransformerInterface<\Generated\Shared\Transfer\PropelQueryBuilderRuleSetTransfer|null, string|null>
     */
    public function createRuleSetTransformer(): DataTransformerInterface
    {
        return new RuleQuerySetTransformer($this->getUtilEncodingService());
    }

    public function createProductRelationFormTypeDataProvider(): ProductRelationTypeDataProvider
    {
        return new ProductRelationTypeDataProvider($this->getProductRelationFacade());
    }

    public function createUniqueRelationTypeForProductAbstractAndQuerySetConstraint(): Constraint
    {
        return new UniqueRelationTypeForProductAbstractAndQuerySet([
            UniqueRelationTypeForProductAbstractAndQuerySet::OPTION_PRODUCT_RELATION_FACADE => $this->getProductRelationFacade(),
            'groups' => [
                ProductRelationFormType::GROUP_AFTER,
            ],
        ]);
    }

    public function createProductRelationKeyUniqueConstraint(): Constraint
    {
        return new ProductRelationKeyUniqueConstraint([
            ProductRelationKeyUniqueConstraint::OPTION_PRODUCT_RELATION_FACADE => $this->getProductRelationFacade(),
        ]);
    }

    public function createUniqueProductRelationByProductAbstractAndRelationTypeAndStoresConstraint(): Constraint
    {
        return new UniqueProductRelationByProductAbstractAndRelationTypeAndStoresConstraint([
            UniqueProductRelationByProductAbstractAndRelationTypeAndStoresConstraint::OPTION_PRODUCT_RELATION_FACADE => $this->getProductRelationFacade(),
        ]);
    }

    public function createProductAbstractNotBlankConstraint(): Constraint
    {
        return new ProductAbstractNotBlankConstraint();
    }

    /**
     * @param \Generated\Shared\Transfer\ProductRelationTransfer $data
     * @param array<string, mixed> $options
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    public function createRelationForm(
        ProductRelationTransfer $data,
        array $options
    ): FormInterface {
        return $this->getFormFactory()->create(
            ProductRelationFormType::class,
            $data,
            $options,
        );
    }

    public function createProductRelationDeleteForm(): FormInterface
    {
        return $this->getFormFactory()->create(ProductRelationDeleteForm::class);
    }

    public function createProductRelationTabs(): ProductRelationTabs
    {
        return new ProductRelationTabs();
    }

    public function createProductRuleTable(ProductRelationTransfer $productRelationTransfer): ProductRuleTable
    {
        return new ProductRuleTable(
            $this->getProductFacade(),
            $this->createRuleQueryCreator(),
            $this->getUtilEncodingService(),
            $this->getLocaleFacade(),
            $this->getConfig(),
            $productRelationTransfer,
        );
    }

    public function createProductRelationTable(): ProductRelationTable
    {
        return new ProductRelationTable(
            $this->getProductRelationPropelQuery(),
            $this->getProductFacade(),
            $this->getConfig(),
            $this->getLocaleFacade(),
        );
    }

    public function createProductTable(?int $idProductRelation = null): ProductTable
    {
        return new ProductTable(
            $this->getProductAbstractPropelQuery(),
            $this->getLocaleFacade(),
            $this->getUtilEncodingService(),
            $idProductRelation,
        );
    }

    public function createMappingProvider(): MappingProviderInterface
    {
        return new MappingProvider($this->getProductAttributeKeyPropelQuery());
    }

    public function createFilterProvider(): FilterProviderInterface
    {
        return new FilterProvider($this->getProductAttributeFacade());
    }

    public function createRuleQueryCreator(): RuleQueryCreatorInterface
    {
        return new RuleQueryCreator(
            $this->getLocaleFacade(),
            $this->getProductAbstractPropelQuery(),
            $this->createMappingProvider(),
            $this->getPropelQueryBuilderQueryContainer(),
        );
    }

    public function createProductRelationToggleIsActiveForm(): FormInterface
    {
        return $this->getFormFactory()->create(ProductRelationToggleIsActiveForm::class);
    }

    public function getProductRelationFacade(): ProductRelationGuiToProductRelationFacadeInterface
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::FACADE_PRODUCT_RELATION);
    }

    public function getUtilEncodingService(): ProductRelationGuiToUtilEncodingServiceInterface
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::SERVICE_UTIL_ENCODING);
    }

    public function getProductFacade(): ProductRelationGuiToProductFacadeInterface
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::FACADE_PRODUCT);
    }

    public function getLocaleFacade(): ProductRelationGuiToLocaleFacadeInterface
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::FACADE_LOCALE);
    }

    public function getPropelQueryBuilderQueryContainer(): ProductRelationGuiToPropelQueryBuilderQueryContainerInterface
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::QUERY_CONTAINER_PROPEL_QUERY_BUILDER);
    }

    public function getProductRelationPropelQuery(): SpyProductRelationQuery
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::PROPEL_QUERY_PRODUCT_RELATION);
    }

    public function getProductAbstractPropelQuery(): SpyProductAbstractQuery
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::PROPEL_QUERY_PRODUCT_ABSTRACT);
    }

    public function getProductAttributeKeyPropelQuery(): SpyProductAttributeKeyQuery
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::PROPEL_QUERY_PRODUCT_ATTRIBUTE_KEY);
    }

    public function getProductAttributeFacade(): ProductRelationGuiToProductAttributeFacadeInterface
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::FACADE_PRODUCT_ATTRIBUTE);
    }

    public function getStoreRelationFormTypePlugin(): FormTypeInterface
    {
        return $this->getProvidedDependency(ProductRelationGuiDependencyProvider::PLUGIN_STORE_RELATION_FORM_TYPE);
    }
}
