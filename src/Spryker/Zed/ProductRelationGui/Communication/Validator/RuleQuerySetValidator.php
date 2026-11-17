<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Communication\Validator;

use Generated\Shared\Transfer\PropelQueryBuilderRuleSetTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\ProductRelationGui\Communication\Provider\FilterProviderInterface;

class RuleQuerySetValidator implements RuleQuerySetValidatorInterface
{
    protected const string FILTER_KEY_ID = 'id';

    protected const string FILTER_KEY_FIELD = 'field';

    protected const string FILTER_KEY_OPERATORS = 'operators';

    protected const array ALLOWED_CONDITIONS = [Criteria::LOGICAL_AND, Criteria::LOGICAL_OR];

    public function __construct(protected FilterProviderInterface $filterProvider)
    {
    }

    public function isValid(PropelQueryBuilderRuleSetTransfer $propelQueryBuilderRuleSetTransfer): bool
    {
        return $this->isValidRuleGroup($propelQueryBuilderRuleSetTransfer, $this->getFiltersIndexedById());
    }

    /**
     * @param \Generated\Shared\Transfer\PropelQueryBuilderRuleSetTransfer $propelQueryBuilderRuleSetTransfer
     * @param array<string, array<string, mixed>> $filtersIndexedById
     *
     * @return bool
     */
    protected function isValidRuleGroup(PropelQueryBuilderRuleSetTransfer $propelQueryBuilderRuleSetTransfer, array $filtersIndexedById): bool
    {
        $condition = $propelQueryBuilderRuleSetTransfer->getCondition();
        if ($condition !== null && !in_array(strtoupper($condition), static::ALLOWED_CONDITIONS, true)) {
            return false;
        }

        foreach ($propelQueryBuilderRuleSetTransfer->getRules() as $ruleSetTransfer) {
            $isValid = $ruleSetTransfer->getRules()->count() > 0
                ? $this->isValidRuleGroup($ruleSetTransfer, $filtersIndexedById)
                : $this->isValidRule($ruleSetTransfer, $filtersIndexedById);

            if (!$isValid) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param \Generated\Shared\Transfer\PropelQueryBuilderRuleSetTransfer $ruleSetTransfer
     * @param array<string, array<string, mixed>> $filtersIndexedById
     *
     * @return bool
     */
    protected function isValidRule(PropelQueryBuilderRuleSetTransfer $ruleSetTransfer, array $filtersIndexedById): bool
    {
        $filter = $filtersIndexedById[(string)$ruleSetTransfer->getId()] ?? null;
        if ($filter === null) {
            return false;
        }

        if ($ruleSetTransfer->getField() !== $filter[static::FILTER_KEY_FIELD]) {
            return false;
        }

        return in_array($ruleSetTransfer->getOperator(), $filter[static::FILTER_KEY_OPERATORS], true);
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    protected function getFiltersIndexedById(): array
    {
        $filtersIndexedById = [];
        foreach ($this->filterProvider->getFilters() as $filter) {
            $filtersIndexedById[$filter[static::FILTER_KEY_ID]] = $filter;
        }

        return $filtersIndexedById;
    }
}
