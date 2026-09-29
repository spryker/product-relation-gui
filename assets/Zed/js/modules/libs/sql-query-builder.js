/**
 * Copyright (c) 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

'use strict';

require('@spryker/jquery-query-builder');

var tableAccess = require('ZedGuiModules/libs/table/table-access');

var SqlQueryBuilder = function (options) {
    this.idProductRelation = null;
    this.builder = null;
    this.queryBuilderElement = null;
    this.filtersUrl = null;
    this.productRelationQuerySet = null;
    this.productRelationFormSubmitBtn = null;
    this.ruleQueryTable = null;
    this.ruleQueryTableHandle = null;
    this.tabsContainer = null;
    this.flashMessages = null;

    $.extend(this, options);

    var filterConfigurationUrl = this.filtersUrl + this.idProductRelation;
    var self = this;

    tableAccess.requestTable(this.ruleQueryTable[0], function (handle) {
        self.ruleQueryTableHandle = handle;
    });

    $.get(filterConfigurationUrl).done(function (filters) {
        self.builder = self.queryBuilderElement.queryBuilder(self.getQueryBuilderOptions(filters));
        self.loadQuerySet();
        self.watchForQueryRuleUpdates();
        self.updateTable();
        self.onFormSubmit();
    });
};

SqlQueryBuilder.prototype.getQuerySet = function () {
    var status = this.builder.queryBuilder('getRules') || {};

    if (!status.rules || !status.rules.length) {
        return [];
    }

    this.toggleSubmitButton(false);
    this.toggleErrorState(false);

    return this.builder.queryBuilder('getRules');
};

SqlQueryBuilder.prototype.loadQuerySet = function () {
    var querySet = this.productRelationQuerySet.val();

    if (querySet.length > 0) {
        this.builder.queryBuilder('setRules', JSON.parse(querySet));
    }
};

SqlQueryBuilder.prototype.onFormSubmit = function () {
    var self = this;

    this.productRelationFormSubmitBtn.on('click', function (event) {
        if (!self.builder.queryBuilder('validate')) {
            event.preventDefault();
            self.toggleSubmitButton(true);
            self.toggleErrorState(true);
            window.scrollTo(0, 0);
        }
    });
};

SqlQueryBuilder.prototype.getQueryBuilderOptions = function (filters) {
    return {
        filters: filters,
        default_condition: 'AND',
        optgroups: {
            attributes: '-- Attributes',
        },
        lang: {
            operators: {
                in: 'is in',
            },
        },
        sqlOperators: {
            in: { op: 'IS IN ?', sep: ', ' },
        },
        sqlRuleOperator: {
            'IS IN': function (v) {
                return {
                    val: v,
                    op: 'in',
                };
            },
        },
    };
};

SqlQueryBuilder.prototype.watchForQueryRuleUpdates = function () {
    var self = this;

    this.queryBuilderElement.on(
        'afterAddGroup.queryBuilder afterAddRule.queryBuilder afterUpdateRuleValue.queryBuilder	afterUpdateRuleFilter.queryBuilder afterUpdateRuleOperator.queryBuilder afterApplyRuleFlags.queryBuilder afterUpdateGroupCondition.queryBuilder afterDeleteRule.queryBuilder afterDeleteGroup.queryBuilder',
        function () {
            self.updateTable();
            self.updateQuerySetField();
        },
    );
};

SqlQueryBuilder.prototype.updateTable = function () {
    this.reloadQueryBuilderTable(JSON.stringify(this.getQuerySet()));
};

SqlQueryBuilder.prototype.updateQuerySetField = function () {
    var json = JSON.stringify(this.getQuerySet());

    this.productRelationQuerySet.val(json);
};

/**
 * @param {string} json - Rules of the query builder, as the table expects them.
 *
 * @returns {string} URL the rows of the table are loaded from.
 */
SqlQueryBuilder.prototype.getQueryBuilderTableUrl = function (json) {
    var url = new URL(this.ruleQueryTable[0].dataset.ajax, window.location.origin);

    url.searchParams.set('data', json);

    return url.pathname + url.search;
};

/**
 * @param {string} json - Rules of the query builder, as the table expects them.
 */
SqlQueryBuilder.prototype.reloadQueryBuilderTable = function (json) {
    if (!this.ruleQueryTableHandle) {
        return;
    }

    this.ruleQueryTableHandle.reload(this.getQueryBuilderTableUrl(json));
};

SqlQueryBuilder.prototype.toggleSubmitButton = function (isDisabled) {
    this.productRelationFormSubmitBtn[0].disabled = isDisabled;
    this.productRelationFormSubmitBtn[0].classList.toggle('disabled', isDisabled);
};

SqlQueryBuilder.prototype.toggleErrorState = function (isError) {
    this.tabsContainer.find('[data-bs-target="tab-content-assign-products"]').toggleClass('error', isError);

    this.flashMessages.html(
        isError ? '<div class="alert alert-danger">' + this.builder.attr('data-error-message') + '</div>' : '',
    );
};

module.exports = SqlQueryBuilder;
