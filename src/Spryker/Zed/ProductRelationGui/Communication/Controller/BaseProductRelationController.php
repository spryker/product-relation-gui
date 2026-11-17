<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Communication\Controller;

use Generated\Shared\Transfer\ProductRelationTransfer;
use Generated\Shared\Transfer\PropelQueryBuilderRuleSetTransfer;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @method \Spryker\Zed\ProductRelationGui\Communication\ProductRelationGuiCommunicationFactory getFactory()
 */
class BaseProductRelationController extends AbstractController
{
    /**
     * @var string
     */
    protected const URL_PARAM_DATA = 'data';

    protected const string ERROR_MESSAGE_INVALID_RULE_QUERY_SET = 'Invalid rule query set.';

    protected const string RESPONSE_KEY_ERROR = 'error';

    public function ruleQueryTableAction(Request $request): JsonResponse
    {
        $ruleSetJson = (string)$request->get(static::URL_PARAM_DATA);

        $ruleSet = $this->getFactory()->getUtilEncodingService()
            ->decodeJson($ruleSetJson, true);

        if (!is_array($ruleSet)) {
            return $this->createInvalidRuleQuerySetResponse();
        }

        $productRelationTransfer = $this->createProductRelationTransfer($ruleSet);

        if (!$this->getFactory()->createRuleQuerySetValidator()->isValid($productRelationTransfer->getQuerySetOrFail())) {
            return $this->createInvalidRuleQuerySetResponse();
        }

        $productRuleTable = $this->getFactory()
            ->createProductRuleTable($productRelationTransfer);

        return $this->jsonResponse(
            $productRuleTable->fetchData(),
        );
    }

    protected function createInvalidRuleQuerySetResponse(): JsonResponse
    {
        return $this->jsonResponse(
            [static::RESPONSE_KEY_ERROR => static::ERROR_MESSAGE_INVALID_RULE_QUERY_SET],
            Response::HTTP_BAD_REQUEST,
        );
    }

    protected function createProductRelationTransfer(array $ruleSet): ProductRelationTransfer
    {
        $productRelationTransfer = new ProductRelationTransfer();
        $propelQueryBuilderRuleSetTransfer = new PropelQueryBuilderRuleSetTransfer();
        $propelQueryBuilderRuleSetTransfer->fromArray($ruleSet);
        $productRelationTransfer->setQuerySet($propelQueryBuilderRuleSetTransfer);

        return $productRelationTransfer;
    }
}
