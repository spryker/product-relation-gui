<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Dependency\Facade;

use Generated\Shared\Transfer\ProductRelationCriteriaTransfer;
use Generated\Shared\Transfer\ProductRelationResponseTransfer;
use Generated\Shared\Transfer\ProductRelationTransfer;

interface ProductRelationGuiToProductRelationFacadeInterface
{
    /**
     * @param int $idProductRelation
     *
     * @return \Generated\Shared\Transfer\ProductRelationResponseTransfer
     */
    public function findProductRelationById($idProductRelation): ProductRelationResponseTransfer;

    public function findProductRelationByCriteria(
        ProductRelationCriteriaTransfer $productRelationCriteriaTransfer
    ): ?ProductRelationTransfer;

    public function createProductRelation(ProductRelationTransfer $productRelationTransfer): ProductRelationResponseTransfer;

    public function deleteProductRelation(int $idProductRelation): ProductRelationResponseTransfer;

    public function updateProductRelation(ProductRelationTransfer $productRelationTransfer): ProductRelationResponseTransfer;

    public function getProductAbstractDataById(int $idProductAbstract, int $idLocale): array;

    public function getStoresByProductRelationCriteria(
        ProductRelationCriteriaTransfer $productRelationCriteriaTransfer
    ): array;
}
