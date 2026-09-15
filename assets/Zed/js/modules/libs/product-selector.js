/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

var ProductSelector = function ProductSelector(options) {
    this.idProductAbstractElement = null;
    this.selectedProductContainer = null;
    this.selectProductNotice = null;
    this.productTable = null;
    this.selectProductUrl = null;

    $.extend(this, options);

    this.initialiseProductTable();
    this.findSelectedProduct();
};

ProductSelector.prototype.initialiseProductTable = function () {
    var self = this;

    this.productTable.on('click', 'a[data-select-product]', function (event) {
        event.preventDefault();

        self.selectProduct($(this).data('select-product'));
    });
};

ProductSelector.prototype.findSelectedProduct = function () {
    var idSelectedProduct = parseInt(this.idProductAbstractElement.val());
    if (!idSelectedProduct) {
        return;
    }

    this.selectProduct(idSelectedProduct);
};

ProductSelector.prototype.selectProduct = function (idProductAbstract) {
    var self = this;

    $.get(this.selectProductUrl + idProductAbstract).done(function (selectedProduct) {
        self.updateSelectedProduct(selectedProduct);
    });
};

ProductSelector.prototype.updateSelectedProduct = function (selectedProduct) {
    var name = selectedProduct['spy_product_abstract_localized_attributes.name'];
    var description = selectedProduct['spy_product_abstract_localized_attributes.description'];
    var categories = selectedProduct.assigned_categories;
    var imageUrl = selectedProduct['spy_product_image.external_url_small'];
    var idProductAbstract = selectedProduct['spy_product_abstract.id_product_abstract'];

    this.selectProductNotice.hide();

    this.selectedProductContainer.show();
    this.selectedProductContainer.find('#product-img').attr({ src: imageUrl });
    this.selectedProductContainer.find('.product-name').text(name);
    this.selectedProductContainer.find('#product-description').text(description);
    this.selectedProductContainer.find('#product-category').text(categories);
    this.idProductAbstractElement.val(idProductAbstract);
};

module.exports = ProductSelector;
