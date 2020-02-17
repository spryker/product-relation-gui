<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductRelationGui\Communication\Controller;

use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ProductRelationGui\Communication\ProductRelationGuiCommunicationFactory getFactory()
 */
class ViewController extends AbstractController
{
    public const URL_PARAM_ID_PRODUCT_RELATION = 'id-product-relation';
    protected const ERROR_MESSAGE = 'Product relation not found';
    protected const REDIRECT_URL = '/product-relation-gui/list';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function indexAction(Request $request)
    {
        $idProductRelation = $this->castId($request->query->get(static::URL_PARAM_ID_PRODUCT_RELATION));

        $productRelationTransfer = $this->getFactory()
            ->getProductRelationFacade()
            ->findProductRelationById($idProductRelation);

        if ($productRelationTransfer === null) {
            $this->addErrorMessage(static::ERROR_MESSAGE);

            return $this->redirectResponse(static::REDIRECT_URL);
        }

        $productTable = $this->getFactory()->createProductTable($idProductRelation);

        $productAbstractTransfer = $this->getFactory()
            ->getProductFacade()
            ->findProductAbstractById($productRelationTransfer->getFkProductAbstract());

        $localeTransfer = $this->getFactory()
            ->getLocaleFacade()
            ->getCurrentLocale();

        return [
            'productRelation' => $productRelationTransfer,
            'product' => $productAbstractTransfer,
            'productTable' => $productTable->render(),
            'locale' => $localeTransfer,
        ];
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function tableAction(Request $request)
    {
        $idProductRelation = $this->castId($request->query->get(static::URL_PARAM_ID_PRODUCT_RELATION));

        $productTable = $this->getFactory()->createProductTable($idProductRelation);

        return $this->jsonResponse(
            $productTable->fetchData()
        );
    }
}
