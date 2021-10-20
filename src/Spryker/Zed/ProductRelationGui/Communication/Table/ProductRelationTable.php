<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Communication\Table;

use Orm\Zed\Product\Persistence\Map\SpyProductAbstractLocalizedAttributesTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap;
use Orm\Zed\ProductRelation\Persistence\Map\SpyProductRelationTableMap;
use Orm\Zed\ProductRelation\Persistence\Map\SpyProductRelationTypeTableMap;
use Orm\Zed\ProductRelation\Persistence\SpyProductRelation;
use Orm\Zed\ProductRelation\Persistence\SpyProductRelationQuery;
use Orm\Zed\Store\Persistence\Map\SpyStoreTableMap;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Gui\Communication\Table\AbstractTable;
use Spryker\Zed\Gui\Communication\Table\TableConfiguration;
use Spryker\Zed\ProductRelationGui\Communication\Controller\DeleteController;
use Spryker\Zed\ProductRelationGui\Communication\Controller\EditController;
use Spryker\Zed\ProductRelationGui\Communication\Controller\ViewController;
use Spryker\Zed\ProductRelationGui\Communication\Form\ProductRelationToggleIsActiveForm;
use Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToLocaleFacadeInterface;
use Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToProductFacadeInterface;
use Spryker\Zed\ProductRelationGui\ProductRelationGuiConfig;

class ProductRelationTable extends AbstractTable
{
    /**
     * @var string
     */
    protected const HEADER_AVAILABLE_IN_STORE = 'Store';

    /**
     * @var string
     */
    protected const HEADER_ID_PRODUCT_RELATION = 'ID';

    /**
     * @var string
     */
    protected const HEADER_ABSTRACT_SKU = 'Abstract SKU';

    /**
     * @var string
     */
    protected const HEADER_ABSTRACT_PRODUCT_NAME = 'Abstract product name';

    /**
     * @var string
     */
    protected const HEADER_RELATION_TYPE = 'Relation type';

    /**
     * @var string
     */
    protected const HEADER_STATUS = 'Status';

    /**
     * @var string
     */
    protected const HEADER_NUMBER_OF_PRODUCTS = 'Number of Products';

    /**
     * @var string
     */
    protected const HEADER_PRODUCT_RELATION_KEY = 'Product Relation Key';

    /**
     * @var string
     */
    protected const COL_ACTIONS = 'Actions';

    /**
     * @var string
     */
    protected const COL_NUMBER_OF_RELATED_PRODUCTS = 'numberOfRelatedProducts';

    /**
     * @var string
     */
    protected const COL_LOCALIZED_NAME = 'localized_name';

    /**
     * @var string
     */
    protected const URL_RELATION_DELETE = '/product-relation-gui/delete/index';

    /**
     * @var string
     */
    protected const URL_RELATION_TOGGLE_IS_ACTIVE = '/product-relation-gui/edit/toggle-is-active';

    /**
     * @var string
     */
    protected const URL_PRODUCT_RELATION_LIST = '/product-relation-gui/list/index';

    /**
     * @var string
     */
    protected const URL_PRODUCT_RELATION_VIEW = '/product-relation-gui/view/index';

    /**
     * @var string
     */
    protected const LABEL_PRIMARY = 'label-primary';

    /**
     * @var string
     */
    public const URL_PRODUCT_RELATION_EDIT = '/product-relation-gui/edit/index';

    /**
     * @var \Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToProductFacadeInterface
     */
    protected $productFacade;

    /**
     * @var \Spryker\Zed\ProductRelationGui\ProductRelationGuiConfig
     */
    protected $productRelationGuiConfig;

    /**
     * @var \Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToLocaleFacadeInterface
     */
    protected $localeFacade;

    /**
     * @var \Orm\Zed\ProductRelation\Persistence\SpyProductRelationQuery
     */
    protected $productRelationQuery;

    /**
     * @param \Orm\Zed\ProductRelation\Persistence\SpyProductRelationQuery $productRelationQuery
     * @param \Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToProductFacadeInterface $productFacade
     * @param \Spryker\Zed\ProductRelationGui\ProductRelationGuiConfig $productRelationGuiConfig
     * @param \Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToLocaleFacadeInterface $localeFacade
     */
    public function __construct(
        SpyProductRelationQuery $productRelationQuery,
        ProductRelationGuiToProductFacadeInterface $productFacade,
        ProductRelationGuiConfig $productRelationGuiConfig,
        ProductRelationGuiToLocaleFacadeInterface $localeFacade
    ) {
        $this->productRelationQuery = $productRelationQuery;
        $this->productFacade = $productFacade;
        $this->productRelationGuiConfig = $productRelationGuiConfig;
        $this->localeFacade = $localeFacade;

        $this->setTableIdentifier('product-relation-table');
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return \Spryker\Zed\Gui\Communication\Table\TableConfiguration
     */
    protected function configure(TableConfiguration $config): TableConfiguration
    {
        $this->setHeaders($config);
        $this->setRawColumns($config);
        $this->setSortableFields($config);
        $this->setSearchableFields($config);
        $this->setDefaultSortField($config);

        return $config;
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setRawColumns(TableConfiguration $config): void
    {
        $config->setRawColumns([
            static::COL_ACTIONS,
            SpyProductRelationTableMap::COL_IS_ACTIVE,
            SpyStoreTableMap::COL_NAME,
        ]);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setHeaders(TableConfiguration $config): void
    {
        $config->setHeader([
            SpyProductRelationTableMap::COL_ID_PRODUCT_RELATION => static::HEADER_ID_PRODUCT_RELATION,
            SpyProductRelationTableMap::COL_PRODUCT_RELATION_KEY => static::HEADER_PRODUCT_RELATION_KEY,
            SpyProductAbstractTableMap::COL_SKU => static::HEADER_ABSTRACT_SKU,
            SpyProductAbstractLocalizedAttributesTableMap::COL_NAME => static::HEADER_ABSTRACT_PRODUCT_NAME,
            SpyProductRelationTypeTableMap::COL_KEY => static::HEADER_RELATION_TYPE,
            SpyProductRelationTableMap::COL_IS_ACTIVE => static::HEADER_STATUS,
            SpyStoreTableMap::COL_NAME => static::HEADER_AVAILABLE_IN_STORE,
            static::COL_NUMBER_OF_RELATED_PRODUCTS => static::HEADER_NUMBER_OF_PRODUCTS,
            static::COL_ACTIONS => static::COL_ACTIONS,
        ]);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setSortableFields(TableConfiguration $config): void
    {
        $config->setSortable([
            SpyProductRelationTableMap::COL_ID_PRODUCT_RELATION,
            SpyProductAbstractTableMap::COL_SKU,
            SpyProductAbstractLocalizedAttributesTableMap::COL_NAME,
            SpyProductRelationTableMap::COL_IS_ACTIVE,
            SpyProductRelationTypeTableMap::COL_KEY,
            static::COL_NUMBER_OF_RELATED_PRODUCTS,
            SpyProductRelationTableMap::COL_PRODUCT_RELATION_KEY,
        ]);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setSearchableFields(TableConfiguration $config): void
    {
        $config->setSearchable([
            SpyProductAbstractTableMap::COL_SKU,
            SpyProductRelationTypeTableMap::COL_KEY,
            SpyProductAbstractLocalizedAttributesTableMap::COL_NAME,
            SpyProductRelationTableMap::COL_PRODUCT_RELATION_KEY,
        ]);
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return void
     */
    protected function setDefaultSortField(TableConfiguration $config): void
    {
        $config->setDefaultSortField(
            SpyProductRelationTableMap::COL_ID_PRODUCT_RELATION,
            TableConfiguration::SORT_DESC,
        );
    }

    /**
     * @param \Spryker\Zed\Gui\Communication\Table\TableConfiguration $config
     *
     * @return array
     */
    protected function prepareData(TableConfiguration $config): array
    {
        $localeTransfer = $this->localeFacade->getCurrentLocale();
        $query = $this->prepareQuery($localeTransfer->getIdLocale());

        $queryResults = $this->runQuery($query, $config, true);

        $results = [];
        foreach ($queryResults as $item) {
            $results[] = $this->mapResults($item);
        }

        return $results;
    }

    /**
     * @module Product
     *
     * @param int $idLocale
     *
     * @return \Orm\Zed\ProductRelation\Persistence\SpyProductRelationQuery
     */
    protected function prepareQuery(int $idLocale): SpyProductRelationQuery
    {
        return $this->productRelationQuery
            ->leftJoinWithSpyProductAbstract()
            ->leftJoinSpyProductRelationProductAbstract('num_alias')
            ->leftJoinWithSpyProductRelationType()
            ->useSpyProductAbstractQuery()
                ->useSpyProductAbstractLocalizedAttributesQuery()
                    ->filterByFkLocale($idLocale)
                ->endUse()
            ->endUse()
            ->withColumn(SpyProductAbstractLocalizedAttributesTableMap::COL_NAME, static::COL_LOCALIZED_NAME)
            ->withColumn("COUNT('num_alias')", static::COL_NUMBER_OF_RELATED_PRODUCTS)
            ->groupByIdProductRelation();
    }

    /**
     * @param \Orm\Zed\ProductRelation\Persistence\SpyProductRelation $productRelationEntity
     *
     * @return array
     */
    protected function mapResults(SpyProductRelation $productRelationEntity): array
    {
        return [
            SpyProductRelationTableMap::COL_ID_PRODUCT_RELATION => $productRelationEntity->getIdProductRelation(),
            SpyProductRelationTableMap::COL_PRODUCT_RELATION_KEY => $productRelationEntity->getProductRelationKey(),
            SpyProductAbstractTableMap::COL_SKU => $productRelationEntity->getSpyProductAbstract()->getSku(),
            SpyProductAbstractLocalizedAttributesTableMap::COL_NAME => $productRelationEntity->getVirtualColumn(static::COL_LOCALIZED_NAME),
            SpyProductRelationTypeTableMap::COL_KEY => $productRelationEntity->getSpyProductRelationType()->getKey(),
            static::COL_NUMBER_OF_RELATED_PRODUCTS => $productRelationEntity->getVirtualColumn(static::COL_NUMBER_OF_RELATED_PRODUCTS),
            SpyProductRelationTableMap::COL_IS_ACTIVE => $this->buildActiveLabel($productRelationEntity),
            SpyStoreTableMap::COL_NAME => $this->getStoreNames($productRelationEntity),
            static::COL_ACTIONS => implode(' ', $this->buildActions($productRelationEntity)),
        ];
    }

    /**
     * @param \Orm\Zed\ProductRelation\Persistence\SpyProductRelation $productRelationEntity
     *
     * @return array<string>
     */
    protected function buildActions(SpyProductRelation $productRelationEntity): array
    {
        $idProductRelation = $productRelationEntity->getIdProductRelation();

        $buttons = [];
        $buttons[] = $this->createViewButton($idProductRelation);
        $buttons[] = $this->createEditButton($idProductRelation);
        $buttons[] = $this->createRelationStatusChangeButton($productRelationEntity);
        $buttons[] = $this->createDeleteButton($idProductRelation);

        return $buttons;
    }

    /**
     * @param int $idProductRelation
     *
     * @return string
     */
    protected function createViewButton(int $idProductRelation): string
    {
        return $this->generateViewButton(
            Url::generate(
                static::URL_PRODUCT_RELATION_VIEW,
                [
                    ViewController::URL_PARAM_ID_PRODUCT_RELATION => $idProductRelation,
                ],
            ),
            'View',
            [
                'icon' => 'fa-eye',
            ],
        );
    }

    /**
     * @param \Orm\Zed\ProductRelation\Persistence\SpyProductRelation $productRelationEntity
     *
     * @return string
     */
    protected function buildActiveLabel(SpyProductRelation $productRelationEntity): string
    {
        if (!$productRelationEntity->getIsActive()) {
            return $this->generateLabel('Inactive', 'label-danger');
        }

        return $this->generateLabel('Active', 'label-info');
    }

    /**
     * @param int $idProductRelation
     *
     * @return string
     */
    protected function createEditButton(int $idProductRelation): string
    {
        return $this->generateEditButton(
            Url::generate(
                static::URL_PRODUCT_RELATION_EDIT,
                [
                    'id-product-relation' => $idProductRelation,
                ],
            ),
            'Edit',
        );
    }

    /**
     * @param int $idProductRelation
     *
     * @return string
     */
    protected function createDeleteButton(int $idProductRelation): string
    {
        return $this->generateRemoveButton(
            Url::generate(static::URL_RELATION_DELETE, [
                DeleteController::URL_PARAM_ID_PRODUCT_RELATION => $idProductRelation,
                DeleteController::URL_PARAM_REDIRECT_URL => static::URL_PRODUCT_RELATION_LIST,
            ]),
            'Delete',
        );
    }

    /**
     * @param \Orm\Zed\ProductRelation\Persistence\SpyProductRelation $productRelationEntity
     *
     * @return string
     */
    protected function createRelationStatusChangeButton(SpyProductRelation $productRelationEntity): string
    {
        if ($productRelationEntity->getIsActive()) {
            return $this->generateFormButton(
                Url::generate(static::URL_RELATION_TOGGLE_IS_ACTIVE, [
                    EditController::URL_PARAM_ID_PRODUCT_RELATION => $productRelationEntity->getIdProductRelation(),
                    EditController::URL_PARAM_REDIRECT_URL => static::URL_PRODUCT_RELATION_LIST,
                ]),
                'Deactivate',
                ProductRelationToggleIsActiveForm::class,
                [static::BUTTON_CLASS => 'btn-danger safe-submit'],
            );
        }

        return $this->generateFormButton(
            Url::generate(static::URL_RELATION_TOGGLE_IS_ACTIVE, [
                EditController::URL_PARAM_ID_PRODUCT_RELATION => $productRelationEntity->getIdProductRelation(),
                EditController::URL_PARAM_REDIRECT_URL => static::URL_PRODUCT_RELATION_LIST,
            ]),
            'Activate',
            ProductRelationToggleIsActiveForm::class,
        );
    }

    /**
     * @param \Orm\Zed\ProductRelation\Persistence\SpyProductRelation $productRelationEntity
     *
     * @return string
     */
    protected function getStoreNames(SpyProductRelation $productRelationEntity): string
    {
        $storeNames = [];
        foreach ($productRelationEntity->getProductRelationStores() as $productRelationStore) {
            $storeName = $productRelationStore->getStore()->getName();

            if ($storeName === null) {
                continue;
            }

            $storeNames[] = $this->generateLabel($storeName, static::LABEL_PRIMARY);
        }

        return implode(' ', $storeNames);
    }
}
