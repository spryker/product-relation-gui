<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductRelationGui\Communication\Validator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\PropelQueryBuilderRuleSetTransfer;
use Propel\Runtime\ActiveQuery\Criteria;
use Spryker\Zed\ProductRelationGui\Communication\ProductRelationGuiCommunicationFactory;
use Spryker\Zed\ProductRelationGui\Communication\Validator\RuleQuerySetValidatorInterface;
use SprykerTest\Zed\ProductRelationGui\ProductRelationGuiCommunicationTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductRelationGui
 * @group Communication
 * @group Validator
 * @group RuleQuerySetValidatorTest
 * Add your own group annotations below this line
 */
class RuleQuerySetValidatorTest extends Unit
{
    protected const string FILTER_ID_PRODUCT_SKU = 'product_sku';

    protected const string OPERATOR_EQUAL = 'equal';

    protected const string FIELD_PRODUCT_ABSTRACT_ATTRIBUTES = 'spy_product_abstract.attributes';

    protected const string PATTERN_PRODUCT_ATTRIBUTE_FILTER_ID = 'product.json.%s';

    protected const string FIELD_INJECTION = 'spy_product_abstract.sku) OR (1=1';

    protected ProductRelationGuiCommunicationTester $tester;

    public function testIsValidReturnsTrueForRuleThatMatchesFilter(): void
    {
        // Arrange
        $ruleSetTransfer = static::createRuleSet(static::FILTER_ID_PRODUCT_SKU, static::FILTER_ID_PRODUCT_SKU, static::OPERATOR_EQUAL);

        // Act
        $isValid = $this->createRuleQuerySetValidator()->isValid($ruleSetTransfer);

        // Assert
        $this->assertTrue($isValid);
    }

    public function testIsValidReturnsTrueForProductAttributeRule(): void
    {
        // Arrange
        $attributeKey = $this->tester->haveProductManagementAttributeKeyWithValue();
        $ruleSetTransfer = static::createRuleSet(sprintf(static::PATTERN_PRODUCT_ATTRIBUTE_FILTER_ID, $attributeKey), static::FIELD_PRODUCT_ABSTRACT_ATTRIBUTES, static::OPERATOR_EQUAL);

        // Act
        $isValid = $this->createRuleQuerySetValidator()->isValid($ruleSetTransfer);

        // Assert
        $this->assertTrue($isValid);
    }

    public function testIsValidReturnsTrueForLowercaseCondition(): void
    {
        // Arrange
        $ruleSetTransfer = static::createRuleSet(static::FILTER_ID_PRODUCT_SKU, static::FILTER_ID_PRODUCT_SKU, static::OPERATOR_EQUAL)
            ->setCondition(strtolower(Criteria::LOGICAL_OR));

        // Act
        $isValid = $this->createRuleQuerySetValidator()->isValid($ruleSetTransfer);

        // Assert
        $this->assertTrue($isValid);
    }

    /**
     * @dataProvider invalidRuleSetDataProvider
     */
    public function testIsValidReturnsFalseForInvalidRuleSet(PropelQueryBuilderRuleSetTransfer $ruleSetTransfer): void
    {
        // Act
        $isValid = $this->createRuleQuerySetValidator()->isValid($ruleSetTransfer);

        // Assert
        $this->assertFalse($isValid);
    }

    /**
     * @return array<string, array<\Generated\Shared\Transfer\PropelQueryBuilderRuleSetTransfer>>
     */
    public static function invalidRuleSetDataProvider(): array
    {
        return [
            'field does not match filter' => [
                static::createRuleSet(static::FILTER_ID_PRODUCT_SKU, static::FIELD_INJECTION, static::OPERATOR_EQUAL),
            ],
            'id is not a known filter' => [
                static::createRuleSet('spy_product_abstract.json.attr', 'spy_product_abstract.sku', static::OPERATOR_EQUAL),
            ],
            'operator is not allowed for filter' => [
                static::createRuleSet(static::FILTER_ID_PRODUCT_SKU, static::FILTER_ID_PRODUCT_SKU, 'contains'),
            ],
            'condition is not AND or OR' => [
                static::createRuleSet(static::FILTER_ID_PRODUCT_SKU, static::FILTER_ID_PRODUCT_SKU, static::OPERATOR_EQUAL)
                    ->setCondition('AND 1=1'),
            ],
            'nested group contains invalid rule' => [
                static::createRuleSet(static::FILTER_ID_PRODUCT_SKU, static::FILTER_ID_PRODUCT_SKU, static::OPERATOR_EQUAL)
                    ->addRules(
                        static::createRuleSet(static::FILTER_ID_PRODUCT_SKU, static::FILTER_ID_PRODUCT_SKU, static::OPERATOR_EQUAL)
                            ->addRules(static::createRule(static::FILTER_ID_PRODUCT_SKU, static::FIELD_INJECTION, static::OPERATOR_EQUAL)),
                    ),
            ],
        ];
    }

    protected function createRuleQuerySetValidator(): RuleQuerySetValidatorInterface
    {
        return (new ProductRelationGuiCommunicationFactory())->createRuleQuerySetValidator();
    }

    protected static function createRuleSet(string $id, string $field, string $operator): PropelQueryBuilderRuleSetTransfer
    {
        return (new PropelQueryBuilderRuleSetTransfer())
            ->setCondition(Criteria::LOGICAL_AND)
            ->addRules(static::createRule($id, $field, $operator));
    }

    protected static function createRule(string $id, string $field, string $operator): PropelQueryBuilderRuleSetTransfer
    {
        return (new PropelQueryBuilderRuleSetTransfer())
            ->setId($id)
            ->setField($field)
            ->setOperator($operator)
            ->setValue('x');
    }
}
