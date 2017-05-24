/**
 * Umc_Base extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/mit-license.php
 *
 * @category  Umc
 * @package   Umc_Base
 * @copyright Marius Strajeru
 * @license   http://opensource.org/licenses/mit-license.php MIT License
 * @author    Marius Strajeru <ultimate.module.creator@gmail.com>
 */
define([
    'jquery',
    'mage/template',
    'Magento_Ui/js/modal/confirm',
    'jquery/jstree/jquery.jstree'
], function($, mageTemplate, confirm) {
    'use strict';
    function checkMessagesElement() {
        if ($('#messages').length == 0) {
            $('#page\\:main-container').before('<div id="messages"></div>');
        }
    }
    $.mage.validation.prototype._onSuccess = function(response) {
        if (!response.error) {
            this._submit();
        } else {
            var attributes = response.attributes || {};
            if (response.attribute) {
                attributes[response.attribute] = response.message;
            }
            for (var attributeCode in attributes) {
                if (attributes.hasOwnProperty(attributeCode)) {
                    $('#' + attributeCode)
                        .addClass('validate-ajax-error')
                        .data('msg-validate-ajax-error', attributes[attributeCode]);
                    this.validate.element("#" + attributeCode);
                }
            }
            var body = $('body');
            body.notification('clear');
            if (response.message) {
                checkMessagesElement();
                $('#messages').html('');
                var glue = (typeof response['glue'] != "undefined") ? response['glue'] : '####';
                var messages = response.message.split(glue);
                for (var i = 0; i < messages.length; i++) {
                    body.notification('add', {
                        error:'error',
                        message:messages[i],
                        messageContainer: '#messages'
                    });
                }
            }

            body.trigger('processStop');
        }
    };
    $.widget("umc_base.umctooltip", {
        /**
         * widget options
         */
        options: {
            type : 'popup',
            buttonLabel: $.mage.__('OK! I got it!')
        },
        /**
         * constructor
         */
        _create: function() {
            var that = this;

            this.element.modal({
                type: that.options.type,
                modalClass: 'form-inline',
                title: $(that.element).attr('title'),
                buttons: that.getButtons(),
                width: 500
            });
        },
        /**
         * get the tooltip buttons
         */
        getButtons: function() {
            var that = this;
            if (!this.buttons) {
                this.buttons = [{
                    text: this.options.buttonLabel,
                    class: 'action-primary',
                    click: function () {
                        $(that.element).trigger("closeModal");
                    }
                }];
            }
            return this.buttons;
        }
    });
    $.widget("umc_base.umcmodule", $.mage.form, {
        menuDialog: '',
        entityIndex: 0,
        entities: [],
        relationIndex: 0,
        relations: [],
        options: {
            addAttributeTrigger:        '.add-attribute',
            addEntityTrigger:           '.add-entity',
            addRelationTrigger:         '.add-relation',
            attributeContainer:         '.attributes-container:first',
            attributeDepends:           {},
            attributeLabelSelector:     '.attribute-label',
            attributePositionSelector:  'input.attribute-position',
            attributeReloaderSelector:  '.attribute-reloader',
            attributeSelector:          '.umc-attribute',
            attributeSortHandle:        '.draggable-handle',
            attributeSortPlaceholder:   'umc-sort-placehoder',
            attributeTemplate:          '#attribute-template',
            attributeTitleSelector:     '.attribute-title',
            entityNameSelector:         '.label-singular',
            entityContainer:             '#entities_container',
            entityDepends:              {},
            entityReloaderSelector:     '.entity-reloader',
            entitySelector:              '.umc-entity',
            entityTemplate:             '#entity-template',
            entityTitleSelector:        '.entity-title',
            entityTabIndex:             1,
            menuElementSelector:        '#umc-nav',
            menuSelector:               '#menu-selector',
            menuSelectorTrigger :       '.menu-selector-trigger',
            moduleDepends:              {},
            moduleReloaderSelector:     '.module-reloader',
            nameAttributes:             {},
            relationContainerSelector:  '#relations_container',
            relationDepends:            {},
            relations :                 {},
            relationSelector:           '.umc-relation',
            relationTitleSelector:      '.relation-title',
            relationOptionClassPrefix:  'relation-entity-index-',
            relationReloaderSelector:   '.relation-reloader',
            relationTabIndex:           2,
            relationsTabSelector:       'li[data-ui-id="umc-base-module-tabs-tab-item-relation"]',
            relationTemplate:           '#relation-template',
            relationValues:             {},
            removeEntityMessage:        $.mage.__('Are you sure you want to remove this entity?'),
            removeEntityTrigger:        '.remove-entity',
            removeRelationMessage:      $.mage.__('Are you sure you want to remove this relation?'),
            removeRelationTrigger:      '.remove-relation',
            tabsSelector                :'#umc_base_module_tabs',
            tooltipSelector:            '.umc-tooltip',
            tooltipType:                'popup',
            topLevelMenuNotice:         $.mage.__('It is not recomended to add menu items on the top level of the admin menu. Are you sure you want to do this?'),
            treeMenuItemSelector:       'li.umc-menu-selector',
            restrictions:               {}

        },
        _create: function() {
            var that = this;
            $(this.options.tooltipSelector).umctooltip();
            this.makeTree();
            this.menuDialog = $(this.options.menuSelector).modal({
                title:      $.mage.__('Select parent menu and position'),
                modalClass: 'form-inline',
                buttons:[]
            });
            $(this.options.menuSelectorTrigger).on('click', function(e) {
                e.preventDefault();
                that.menuDialog.trigger('openModal');
            });
            $(this.options.addEntityTrigger).on('click', function() {
                that.addEntity();
            });
            $(this.options.entityContainer).find(this.options.entitySelector).each(function() {
                that.registerEntity(this, true, false);
            });
            $(this.options.addRelationTrigger).on('click', function() {
                that.addRelation();
            });
            $(this.options.relationContainerSelector).find(this.options.relationSelector).each(function() {
                that.registerRelation(this, true);
                var index = that.relationIndex - 1;
                if (typeof that.options.relationValues[index] != "undefined") {
                    $('#relation_' + index + '_entity_one').val(that.options.relationValues[index]['entity_one']);
                    $('#relation_' + index + '_entity_two').val(that.options.relationValues[index]['entity_two']);
                }
            });
            this._super();
            jQuery(document).ready(function(){
                for (var i = 0 ;i<that.entities.length;i++) {
                    $(that.entities[i]).umcentity('checkElements');
                }
            });

            $(this.element).find(this.options.moduleReloaderSelector).on('change', function() {
                that.checkElements();
            });
            this.checkElements();

        },
        checkElements: function() {
            for (var id in this.options.moduleDepends) {
                if (this.options.moduleDepends.hasOwnProperty(id)) {
                    var depends = this.options.moduleDepends[id];
                    var valid = false;
                    for (var j = 0; j < depends.length; j++) {
                        var groupValid = true;
                        for (var k in depends[j]) {
                            if (depends[j].hasOwnProperty(k)) {
                                var parentElem = $(this.element).find('#umc_module' + k);
                                var value = $(parentElem).val();
                                if (typeof depends[j][k]['self'] != "undefined") {
                                    if ($.inArray(value, depends[j][k]['self']) == -1) {
                                        groupValid = false;
                                        break;
                                    }
                                }
                            }
                        }
                        if (groupValid) {
                            valid = true;
                            break;
                        }
                    }
                    var mainElement = $(this.element).find('#umc_module' + id);
                    valid ? this.enableElement(mainElement) : this.disableElement(mainElement);
                }
            }

        },
        makeTree: function() {
            var that = this;
            var tree = $(this.options.menuElementSelector).parent().jstree();
            tree.on('select_node.jstree', function(node, selected) {
                function setParentData(values) {
                    $('.menu-parent-id:first').val(values[0]);

                    var sortOrder = '';
                    var next = $(sel).parent().next().next();
                    if (next.length) {
                        var nextValues = $(next).find('a:first').attr('id').split('___');
                        sortOrder = Math.ceil((parseInt(values[1]) + parseInt(nextValues[1])) / 2);
                    } else {
                        sortOrder = parseInt(values[1]) + 10;
                    }
                    $('.menu-sort-order:first').val(sortOrder);
                    that.menuDialog.trigger('closeModal');
                }
                var sel = $(selected.args[0]);
                var values = sel.attr('id').split('___');
                if (values[0] === '') {
                    confirm({
                        content: that.options.topLevelMenuNotice,
                        actions: {
                            confirm: function () {
                                setParentData(values);
                            }
                        }
                    });
                } else {
                    setParentData(values);
                }
            });
        },
        getEntities: function() {
            return this.entities;
        },
        addEntity: function() {
            var index = this.entityIndex;
            $(this.options.entityContainer).append(mageTemplate(this.options.entityTemplate)({entity_id:index}));
            var newEntityContainer = $('#entity-container-' + index);
            this.registerEntity(newEntityContainer, false, true);
            $(this.options.tabsSelector).tabs("option", "active", this.options.entityTabIndex);
            // add it to relations
            var option = $('<option />');
            option.attr({
                value: index,
                class: this.options.relationOptionClassPrefix + index
            }).text($(this.entities[index]).umcentity('getLabel'));
            $('.relation-entity').append(option);
            $('html, body').animate({
                scrollTop: newEntityContainer.offset().top
            }, 1000);
        },
        registerEntity: function(element, collapse, checkFields) {
            var entity = $(element).umcentity({
                addAttributeTrigger:        this.options.addAttributeTrigger,
                attributeContainer:         this.options.attributeContainer,
                attributeDepends:           this.options.attributeDepends,
                attributeLabelSelector:     this.options.attributeLabelSelector,
                attributePositionSelector:  this.options.attributePositionSelector,
                attributeReloaderSelector:  this.options.attributeReloaderSelector,
                attributeSelector:          this.options.attributeSelector,
                attributeSortHandle:        this.options.attributeSortHandle,
                attributeSortPlaceholder:   this.options.attributeSortPlaceholder,
                attributeTemplate:          this.options.attributeTemplate,
                attributeTitleSelector:     this.options.attributeTitleSelector,
                entityDepends:              this.options.entityDepends,
                entityNameSelector:         this.options.entityNameSelector,
                entityTitleSelector:        this.options.entityTitleSelector,
                index:                      this.entityIndex,
                module:                     this,
                reloaderSelector:           this.options.entityReloaderSelector,
                nameAttribute:              this.options.nameAttributes[this.entityIndex],
                removeEntityMessage:        this.options.removeEntityMessage,
                removeEntityTrigger:        this.options.removeEntityTrigger,
                checkFields:                checkFields
            });
            this.entities.push(entity);
            this.entityIndex++;
            if (collapse) {
                $(element).find('.fieldset-wrapper-content.collapse').collapse('hide');
            }
        },
        removeEntity: function(index) {
            for (var i = 0; i < this.entities.length; i++) {
                if (this.entities[i].umcentity('getIndex') == index) {
                    this.entities[i].umcentity('destroy');
                    this.entities.splice(i, 1);
                    $(this.options.relationOptionClassPrefix + index).remove();
                }
            }
            return this;
        },
        disableElement: function(elem) {
            $(elem).attr('disabled', 'disabled');
        },
        enableElement: function(elem) {
            $(elem).removeAttr('disabled');
        },
        addRelation: function() {
            $(this.options.relationContainerSelector).append(
                mageTemplate(this.options.relationTemplate)({relation_id:this.relationIndex})
            );
            var newRelationContainer = $('#relation-container-' + this.relationIndex);
            this.registerRelation(newRelationContainer, false);
            $(this.options.tabsSelector).tabs("option", "active", this.options.relationTabIndex);
            $('html, body').animate({
                scrollTop: newRelationContainer.offset().top
            }, 1000);
        },
        removeRelation: function(index) {
            for (var i = 0; i < this.relations.length; i++) {
                if (this.relations[i].umcrelation('getIndex') == index) {
                    this.relations[i].umcrelation('destroy');
                    this.relations.splice(i, 1);
                }
            }
            return this;
        },

        registerRelation: function(element, collapse) {
            var relation = $(element).umcrelation({
                index: this.relationIndex,
                module: this,
                relationOptionClassPrefix: this.options.relationOptionClassPrefix,
                removeRelationMessage:     this.options.removeRelationMessage,
                removeRelationTrigger:     this.options.removeRelationTrigger,
                relationTitleSelector:     this.options.relationTitleSelector,
                relationDepends:           this.options.relationDepends,
                relationReloaderSelector:  this.options.relationReloaderSelector
            });
            this.relations.push(relation);
            this.relationIndex++;
            if (collapse) {
                $(element).find('.fieldset-wrapper-content.collapse').collapse('hide');
            }
            return relation;
        }
    });
    $.widget("umc_base.umcentity", {
        index: '',
        module: '',
        attributeIndex: 0,
        attributes: [],
        options: {
            addAttributeTrigger:        '.add-attribute',
            attributeContainer:         '.attributes-container:first',
            attributeDepends:           {},
            attributeLabelSelector:     '.attribute-label',
            attributePositionSelector:  'input.attribute-position',
            attributeReloaderSelector:  '.attribute-reloader',
            attributeSelector:          '.umc-attribute',
            attributeSortHandle:        '.attributeSortHandle',
            attributeSortPlaceholder:   'umc-sort-placehoder',
            attributeTemplate:          '#attribute-template',
            attributeTitleSelector:     '.fieldset-wrapper-title strong span',
            entityDepends:              {},
            entityNameSelector:         '.label-singular',
            entityTitleSelector:        '.entity-title',
            index:                      '',
            module:                     '',
            nameAttribute:              '',
            reloaderSelector:           '.entity-reloader',
            removeEntityMessage:        $.mage.__('Are you sure you want to remove this entity?'),
            removeEntityTrigger:        '.remove-entity',
            checkFields:                true
        },
        _create: function() {
            var that = this;
            this.index = this.options.index;
            this.module = this.options.module;
            $(this.element).find(this.options.entityNameSelector).on('change', function() {
                $(that.element).find(that.options.entityTitleSelector).html(that.getLabel());
                $('.relation-entity-index-' + that.getIndex()).html(that.getLabel());
            });
            $(this.element).find(this.options.removeEntityTrigger).on('click', function() {
                confirm({
                    content: that.options.removeEntityMessage,
                    actions: {
                        confirm: function () {
                            that.remove();
                        }
                    }
                });
            });
            $(this.element).find(this.options.addAttributeTrigger).on('click', function() {
                that.addAttribute();
            });
            $(this.element).find(this.options.attributeContainer).find(this.options.attributeSelector).each(function() {
                that.registerAttribute(this);
            });
            this.initAttributesSort(false);
            $(this.element).find(this.options.reloaderSelector).on('change', function() {
                that.checkElements();
                for (var i = 0; i<that.attributes.length; i++) {
                    $(that.attributes[i]).umcattribute('checkElements');
                }
            });
            if (this.options.checkFields) {
                this.checkElements();
            }
        },
        getLabel: function() {
            var val = 'Entity ' + this.getIndex();
            var filledInLabel = $(this.element).find(this.options.entityNameSelector).val();
            if (filledInLabel) {
                val = filledInLabel
            }
            return val;
        },
        getParentEntity: function() {
            return this.module;
        },
        setIndex: function(index) {
            this.index = index;
        },
        getIndex: function() {
            return this.index;
        },
        setModule: function(module) {
            this.module = module;
        },
        remove: function() {
            var that = this;
            this.module.removeEntity(this.index);
            $(this.element).slideUp(500, function() {
                $(that.element).remove();
            });
        },
        addAttribute: function() {
            var vars = {
                entity_id:this.getIndex(),
                attribute_id: this.attributeIndex
            };
            $(this.getAttributesContainer()).append(mageTemplate(this.options.attributeTemplate)(vars));
            var newAttributeContainer = $('#attribute_' + this.getIndex() + '_' + this.attributeIndex);
            this.registerAttribute(newAttributeContainer);
            this.initAttributesSort(true);
            var attributesContainer = this.getAttributesContainer();
            var parentFieldset = $(attributesContainer).parent();
            parentFieldset.collapse('show');
            $('html, body').animate({
                scrollTop: newAttributeContainer.offset().top - 100
            }, 1000);
        },
        registerAttribute: function(element) {
            var attribute = $(element).umcattribute({
                attributeDepends: this.options.attributeDepends,
                entity: this,
                index: this.attributeIndex,
                labelSelector: this.options.attributeLabelSelector,
                reloaderSelector: this.options.attributeReloaderSelector,
                titleSelector: this.options.attributeTitleSelector,
                tooltipSelector: this.options.tooltipSelector
            });
            if (this.attributeIndex == this.options.nameAttribute) {
                attribute.umcattribute('getNameElement').prop('checked', 'checked');
            }
            this.attributes.push(attribute);
            this.attributeIndex++;
        },
        removeAttribute: function(index) {
            for (var i = 0; i < this.attributes.length; i++) {
                if (this.attributes[i].umcattribute('getIndex') == index) {
                    this.attributes[i].umcattribute('destroy');
                    this.attributes.splice(i, 1);
                }
            }
            return this;
        },
        initAttributesSort: function(withDestroy) {
            var that = this;
            if (withDestroy) {
                this.getAttributesContainer().sortable('destroy');
            }
            this.getAttributesContainer().sortable({
                handle: this.options.attributeSortHandle,
                items: this.options.attributeSelector,
                placeholder: this.options.attributeSortPlaceholder,
                update: function() {
                    that.updatePosition();
                }
            });
            that.updatePosition();
            return this;
        },
        getAttributesContainer: function() {
            return $(this.element).find(this.options.attributeContainer);
        },
        updatePosition : function() {
            $(this.element).find(this.options.attributePositionSelector).each(function(item) {
                $(this).attr('value', 10 * (item + 1));
            });
        },
        checkElements: function() {
            for (var id in this.options.entityDepends) {
                if (this.options.entityDepends.hasOwnProperty(id)) {
                    var depends = this.options.entityDepends[id];
                    var valid = false;
                    for (var j = 0; j < depends.length; j++) {
                        var groupValid = true;
                        for (var k in depends[j]) {
                            if (depends[j].hasOwnProperty(k)) {
                                var parentElem = $(this.element).find('#entity_' + this.getIndex() + '_' + k);
                                var value = $(parentElem).val();
                                if (typeof depends[j][k]['self'] != "undefined") {
                                    if ($.inArray(value, depends[j][k]['self']) == -1) {
                                        groupValid = false;
                                        break;
                                    }
                                }
                            }
                        }
                        if (groupValid) {
                            valid = true;
                            break;
                        }
                    }
                    var mainElement = $(this.element).find('#entity_' + this.getIndex() + '_' + id);
                    valid ? this.enableElement(mainElement) : this.disableElement(mainElement);
                }
            }

        },
        disableElement: function(elem) {
            this.module.disableElement(elem);
        },
        enableElement: function(elem) {
            this.module.enableElement(elem);
        },
        getElement: function() {
            return this.element;
        }



    });
    $.widget("umc_base.umcattribute", {
        entity: '',
        index: '',
        options: {
            attributeDepends: {},
            entity: '',
            index: '',
            labelSelector: '.attribute-label',
            reloaderSelector: '.attribute-reloader',
            titleSelector: '.attribute-title'
        },
        _create: function() {
            var that = this;
            this.entity = this.options.entity;
            this.index = this.options.index;
            this.processIsName();
            $(this.element).find(this.options.labelSelector).on('change', function() {
                var val = 'Attribute ' + that.getIndex();
                if ($(this).val()) {
                    val = $(this).val();
                }
                $(that.element).find(that.options.titleSelector).html(val);
            });
            $(this.element).find(this.options.labelSelector).change();

            $(this.element).find('button.remove-attribute').on('click', function() {
                confirm({
                    content: $.mage.__('Are you sure you want to remove this attribute?'),
                    actions: {
                        confirm: function () {
                            that.remove();
                        }
                    }
                });
            });
            $(this.element).find(this.options.reloaderSelector).on('change', function() {
                that.checkElements();
            });
            this.checkElements();

        },
        getParentEntity: function() {
            return this.entity;
        },
        setIndex: function(index) {
            this.index = index;
            this.processIsName();
        },
        getIndex: function() {
            return this.index;
        },
        setEntity: function(entity) {
            this.entity = entity;
        },
        getEntity: function() {
            return this.entity;
        },
        getNameElement: function() {
            return $(this.element).find('.is-name:first');
        },
        processIsName: function() {
            this.getNameElement().attr('name', 'entity[' + this.getEntity().getIndex() + '][attributes][is_name]');
            this.getNameElement().attr('value', this.getIndex());
        },
        remove: function() {
            var that = this;
            $(this.element).slideUp(500, function() {
                $(that.element).remove();
            });
            this.getEntity().removeAttribute(this.index);
        },
        checkElements: function() {
            for (var id in this.options.attributeDepends) {
                if (this.options.attributeDepends.hasOwnProperty(id)) {
                    var depends = this.options.attributeDepends[id];
                    var valid = false;
                    for (var j = 0; j < depends.length; j++) {
                        var groupValid = true;
                        for (var k in depends[j]) {
                            if (depends[j].hasOwnProperty(k)) {
                                var parentElem = '';
                                var arrValues = '';
                                if (typeof depends[j][k]['self'] != "undefined") {
                                    parentElem = $(this.element).find('#attribute_' + this.getEntity().getIndex() + '_' + this.getIndex() + '_' + k);
                                    arrValues = depends[j][k]['self'];
                                } else {
                                    parentElem = $(this.getEntity().getElement()).find('#entity_' + this.getEntity().getIndex() + '_' + k);
                                    arrValues = depends[j][k]['parent'];
                                }
                                var value = $(parentElem).val();
                                if ($.inArray(value, arrValues) == -1) {
                                    groupValid = false;
                                    break;
                                }
                            }
                        }
                        if (groupValid) {
                            valid = true;
                            break;
                        }
                    }
                }
                var mainElement = $(this.element).find('#attribute_' + this.getEntity().getIndex() + '_' + this.getIndex() + '_' + id);
                valid ? this.enableElement(mainElement) : this.disableElement(mainElement);
            }

        },
        disableElement: function(elem) {
            this.getEntity().disableElement(elem);
        },
        enableElement: function(elem) {
            this.getEntity().enableElement(elem);
        }
    });

    $.widget("umc_base.umcrelation", {
        module: '',
        index: '',
        options: {
            entityDropdownSelector: '.relation-entity',
            index: '',
            module: '',
            relationOptionClassPrefix:    'relation-entity-index-',
            removeRelationMessage:        $.mage.__('Are you sure you want to remove this relation?'),
            removeRelationTrigger:        '.remove-relation',
            relationTitleSelector:        '.relation-title',
            relationFieldsetTitleSelector: '.relation-fieldset-title',
            relationDepends:              {},
            reloaderSelector:             '.reload-relation'
        },
        _create: function() {
            var that = this;
            this.index = this.options.index;
            this.module = this.options.module;
            this.fillEntities();
            $(this.element).find(this.options.removeRelationTrigger).on('click', function() {
                confirm({
                    content: that.options.removeRelationMessage,
                    actions: {
                        confirm: function () {
                            that.remove();
                        }
                    }
                });
            });
            $(this.element).find(this.options.relationTitleSelector).on('change', function(){
                var val = 'Relation ' + that.getIndex();
                if ($(this).val()) {
                    val = $(this).val();
                }
                $(that.element).find(that.options.relationFieldsetTitleSelector).html(val);
            });
            $(this.element).find(this.options.reloaderSelector).on('change', function() {
                that.checkElements();
            });
            this.checkElements();
        },
        getIndex: function() {
            return this.index;
        },
        fillEntities: function() {
            var entities = this.module.getEntities();
            var selects = $(this.element).find(this.options.entityDropdownSelector);
            for (var i = 0; i<selects.length;i++) {
                var currentValue = $(selects[i]).val();
                $(selects[i]).find('option').remove = '';
                for (var j = 0; j<entities.length;j++) {
                    var index = $(entities[j]).umcentity('getIndex');
                    var option = $('<option/>');
                    option.attr({
                        value: index,
                        class: this.options.relationOptionClassPrefix + index
                    }).text($(entities[j]).umcentity('getLabel'));
                    if (index == currentValue) {
                        option.attr('selected', 'selected');
                    }
                    $(selects[i]).append(option);
                }
            }
        },
        remove: function() {
            var that = this;
            this.module.removeRelation(this.index);
            $(this.element).slideUp(500, function() {
                $(that.element).remove();
            });
        },
        checkElements: function() {
            for (var id in this.options.relationDepends) {
                if (this.options.relationDepends.hasOwnProperty(id)) {
                    var depends = this.options.relationDepends[id];
                    var valid = false;
                    for (var j = 0; j < depends.length; j++) {
                        var groupValid = true;
                        for (var k in depends[j]) {
                            if (depends[j].hasOwnProperty(k)) {
                                var parentElem = $(this.element).find('#relation_' + this.getIndex() + '_' + k);
                                var value = $(parentElem).val();
                                if (typeof depends[j][k]['self'] != "undefined") {
                                    if ($.inArray(value, depends[j][k]['self']) == -1) {
                                        groupValid = false;
                                        break;
                                    }
                                }
                            }
                        }
                        if (groupValid) {
                            valid = true;
                            break;
                        }
                    }
                    var mainElement = $(this.element).find('#relation_' + this.getIndex() + '_' + id);
                    valid ? this.enableElement(mainElement) : this.disableElement(mainElement);
                }
            }
        },
        disableElement: function(elem) {
            this.module.disableElement(elem);
        },
        enableElement: function(elem) {
            this.module.enableElement(elem);
        }
    });
});
