<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Dependency\Facade;

use Generated\Shared\Transfer\ProductRelationCriteriaTransfer;
use Generated\Shared\Transfer\ProductRelationResponseTransfer;
use Generated\Shared\Transfer\ProductRelationTransfer;

class ProductRelationGuiToProductRelationFacadeBridge implements ProductRelationGuiToProductRelationFacadeInterface
{
    /**
     * @var \Spryker\Zed\ProductRelation\Business\ProductRelationFacadeInterface
     */
    protected $productRelationFacade;

    /**
     * @param \Spryker\Zed\ProductRelation\Business\ProductRelationFacadeInterface $productRelationFacade
     */
    public function __construct($productRelationFacade)
    {
        $this->productRelationFacade = $productRelationFacade;
    }

    /**
     * @param int $idProductRelation
     *
     * @return \Generated\Shared\Transfer\ProductRelationResponseTransfer
     */
    public function findProductRelationById($idProductRelation): ProductRelationResponseTransfer
    {
        return $this->productRelationFacade->findProductRelationById($idProductRelation);
    }

    public function findProductRelationByCriteria(ProductRelationCriteriaTransfer $productRelationCriteriaTransfer): ?ProductRelationTransfer
    {
        return $this->productRelationFacade->findProductRelationByCriteria($productRelationCriteriaTransfer);
    }

    public function createProductRelation(ProductRelationTransfer $productRelationTransfer): ProductRelationResponseTransfer
    {
        return $this->productRelationFacade->createProductRelation($productRelationTransfer);
    }

    public function deleteProductRelation(int $idProductRelation): ProductRelationResponseTransfer
    {
        return $this->productRelationFacade->deleteProductRelation($idProductRelation);
    }

    public function updateProductRelation(ProductRelationTransfer $productRelationTransfer): ProductRelationResponseTransfer
    {
        return $this->productRelationFacade->updateProductRelation($productRelationTransfer);
    }

    public function getProductAbstractDataById(int $idProductAbstract, int $idLocale): array
    {
        return $this->productRelationFacade->getProductAbstractDataById($idProductAbstract, $idLocale);
    }

    public function getStoresByProductRelationCriteria(ProductRelationCriteriaTransfer $productRelationCriteriaTransfer): array
    {
        return $this->productRelationFacade->getStoresByProductRelationCriteria($productRelationCriteriaTransfer);
    }
}
