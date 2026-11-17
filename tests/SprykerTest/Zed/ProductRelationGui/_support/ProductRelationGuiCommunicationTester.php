<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductRelationGui;

use Codeception\Actor;
use Generated\Shared\Transfer\LocaleTransfer;
use Orm\Zed\ProductAttribute\Persistence\SpyProductManagementAttributeValue;

/**
 * Inherited Methods
 *
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class ProductRelationGuiCommunicationTester extends Actor
{
    use _generated\ProductRelationGuiCommunicationTesterActions;

    protected const string PRODUCT_MANAGEMENT_ATTRIBUTE_VALUE = 'test-value';

    public function getCurrentLocale(): LocaleTransfer
    {
        return $this->getLocator()->locale()->facade()->getCurrentLocale();
    }

    public function haveProductManagementAttributeKeyWithValue(): string
    {
        $productManagementAttributeEntity = $this->haveProductManagementAttributeEntity();

        (new SpyProductManagementAttributeValue())
            ->setFkProductManagementAttribute($productManagementAttributeEntity->getIdProductManagementAttribute())
            ->setValue(static::PRODUCT_MANAGEMENT_ATTRIBUTE_VALUE)
            ->save();

        return $productManagementAttributeEntity->getSpyProductAttributeKey()->getKey();
    }
}
