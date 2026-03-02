<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductRelationGui\Communication\QueryCreator;

use Generated\Shared\Transfer\ProductRelationTransfer;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Spryker\Zed\ProductRelationGui\Communication\QueryCreator\RuleQueryCreator;

class RuleQueryCreatorMock extends RuleQueryCreator
{
    public function createQuery(ProductRelationTransfer $productRelationTransfer): ModelCriteria
    {
        return $this->prepareQuery($productRelationTransfer->getQueryDataProvider());
    }
}
