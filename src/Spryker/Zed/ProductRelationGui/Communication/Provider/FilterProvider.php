<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Communication\Provider;

use Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToProductAttributeFacadeInterface;

class FilterProvider implements FilterProviderInterface
{
    /**
     * @see \Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap::COL_ATTRIBUTES
     *
     * @var string
     */
    protected const COL_ATTRIBUTES = 'spy_product_abstract.attributes';

    /**
     * @see \Orm\Zed\Category\Persistence\Map\SpyCategoryAttributeTableMap::COL_NAME
     *
     * @var string
     */
    protected const COL_CATEGORY_NAME = 'spy_category_attribute.name';

    /**
     * @var string
     */
    protected const PATTERN_ATTRIBUTE_KEY = 'product.json.%s';

    /**
     * @var \Spryker\Zed\ProductRelationGui\Dependency\Facade\ProductRelationGuiToProductAttributeFacadeInterface
     */
    protected $productAttributeFacade;

    public function __construct(ProductRelationGuiToProductAttributeFacadeInterface $productAttributeFacade)
    {
        $this->productAttributeFacade = $productAttributeFacade;
    }

    public function getFilters(): array
    {
        $filters = $this->buildProductFilters();
        $filters = array_merge($filters, $this->buildCategoryFilters());
        $filters = array_merge($filters, $this->buildProductAttributeFilters());

        return $filters;
    }

    protected function buildCategoryFilters(): array
    {
        return [
            [
                'id' => 'product_category_name',
                'field' => static::COL_CATEGORY_NAME,
                'label' => 'category',
                'type' => 'string',
                'input' => 'text',
                'operators' => $this->getTextOperators(),
            ],
        ];
    }

    protected function buildProductFilters(): array
    {
        return [
            [
                'id' => 'product_sku',
                'field' => 'product_sku',
                'label' => 'sku',
                'type' => 'string',
                'input' => 'text',
                'operators' => $this->getTextOperators(),
            ],
            [
                'id' => 'product_name',
                'field' => 'product_name',
                'label' => 'name',
                'type' => 'string',
                'input' => 'text',
                'operators' => $this->getTextOperators(),
            ],
            [
                'id' => 'product_created_at',
                'field' => 'product_created_at',
                'label' => 'created at',
                'type' => 'date',
                'input' => 'text',
                'validation' => [
                    'format' => 'yyyy-mm-dd',
                ],
                'plugin' => 'datepicker',
                'plugin_config' => [
                    'format' => 'yyyy-mm-dd',
                    'todayBtn' => 'linked',
                    'todayHighlight' => true,
                    'autoclose' => true,
                ],
                'operators' => $this->getTextOperators(),
            ],
        ];
    }

    /**
     * @return array<string>
     */
    protected function getTextOperators(): array
    {
        return [
            'equal',
            'greater',
            'greater_or_equal',
            'less',
            'less_or_equal',
            'in',
        ];
    }

    protected function buildProductAttributeFilters(): array
    {
        $productManagementAttributeTransfers = $this->productAttributeFacade->getProductAttributeCollection();

        $filters = [];
        foreach ($productManagementAttributeTransfers as $productManagementAttributeTransfer) {
            $filters[] = [
                'id' => $this->buildAttributeKey($productManagementAttributeTransfer->getKey()),
                'field' => static::COL_ATTRIBUTES,
                'label' => $productManagementAttributeTransfer->getKey(),
                'type' => 'string',
                'input' => 'text',
                'optgroup' => 'attributes',
                'operators' => $this->getTextOperators(),
            ];
        }

        return $filters;
    }

    protected function buildAttributeKey(string $persistedAttributeKey): string
    {
        return sprintf(static::PATTERN_ATTRIBUTE_KEY, $persistedAttributeKey);
    }
}
