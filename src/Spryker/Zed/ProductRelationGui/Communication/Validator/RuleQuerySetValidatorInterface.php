<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Communication\Validator;

use Generated\Shared\Transfer\PropelQueryBuilderRuleSetTransfer;

interface RuleQuerySetValidatorInterface
{
    public function isValid(PropelQueryBuilderRuleSetTransfer $propelQueryBuilderRuleSetTransfer): bool;
}
