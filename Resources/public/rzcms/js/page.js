/**
 * Manages the Page Editor
 *
 * @author Olivier Paradis <paradis.olivier@gmail.com>
 *
 * @modified mell m. zamora <mell@rzproject.org>
 */
var Sonata = Sonata || {};

Sonata.Page = {

    /**
     * Enable/disable debug mode
     *
     * @var boolean
     */
    debug: false,

    /**
     * Inline editor configurations
     */
    rmz_editor_config: null,
    rmz_editor_config_rmzmedia: null,
    rmz_editor_modified_blocks: [],

    /**
     * Collection of blocks found on the page
     *
     * @var array
     */
    blocks: [],

    /**
     * Collection of containers found on the page
     *
     * @var array
     */
    containers: [],

    /**
     * block data
     *
     * @var array
     */
    data: [],

    /**
     * Block DOM selector
     *
     * @var string
     */
    blockSelector: '.cms-block',

    /**
     * Container DOM selector
     *
     * @var string
     */
    containerSelector: '.cms-container',

    /**
     * Drop placeholder CSS class
     *
     * @var string
     */
    dropPlaceHolderClass: 'cms-block-placeholder',

    /**
     * Drop placeholder size
     *
     * @var integer
     */
    dropPlaceHolderSize: 100,

    /**
     * Drop zone container CSS class
     *
     * @var string
     */
    dropZoneClass: 'cms-container-drop-zone',

    /**
     * Block hover CSS class
     *
     * @var string
     */
    blockHoverClass: 'cms-block-hand-over',

    editMode: 'preview',

    settingsPopup: null,

    /**
     * URLs to use when performing ajax operations
     *
     * @var Object
     */
    url: {
        block_save_position: null,
        block_edit: null,
        block_child_edit: null,
        block_delete: null
    },

    htmlCopy: null,

    /**
     * Initialize Page editor mode
     */
    init: function(options) {
        options = options || [];
        for (property in options) {
            this[property] = options[property];
        }

        this.initInterface();
        this.initBlocks();
        this.initContainers();
        this.initBlockData();
    },

    /**
     * Initialize Admin interface (buttons)
     */
    initInterface: function() {
        jQuery('#page-action-enabled-edit').click(jQuery.proxy(this.toggleEditMode, this));
        jQuery('#page-action-save-position').click(jQuery.proxy(this.saveBlockLayout, this));
        // mell zamora
        jQuery('#page-action-enabled-editable').click(jQuery.proxy(this.toggleEditable, this));
        jQuery('#page-action-enabled-preview').click(jQuery.proxy(this.togglePreviewMode, this));
        //inline edit save button
        jQuery('#page-action-save').click(jQuery.proxy(this.saveInlineEdits, this));


    },

    /**
     * Initialize block elements and behaviors
     */
    initBlocks: function() {
        // cache blocks
        this.blocks = jQuery(this.blockSelector);

        this.blocks.mouseover(jQuery.proxy(this.handleBlockHover, this));
        //this.blocks.dblclick(jQuery.proxy(this.handleBlockClick, this));
    },

    /**
     * Initialize container elements and behaviors
     */
    initContainers: function() {
        // cache containers
        this.containers = jQuery(this.containerSelector);

        this.containers.sortable({
            connectWith:          this.containerSelector,
            items:                this.blockSelector,
            placeholder:          this.dropPlaceHolderClass,
            helper:               'clone',
            dropOnEmpty:          true,
            forcePlaceholderSize: this.dropPlaceHolderSize,
            opacity:              1,
            //cursor:               'move',
            start:                jQuery.proxy(this.startContainerSort, this),
            stop:                 jQuery.proxy(this.stopContainerSort, this)
        }).sortable('disable');
    },

    /**
     * Initialize the block data (used to perform a diff when changing position/hierarchy)
     */
    initBlockData: function() {
        this.data = this.buildBlockData();
    },

    /**
     * Starts the container sorting
     *
     * @param event
     * @param ui
     */
    startContainerSort: function(event, ui) {
        this.containers.addClass(this.dropZoneClass);
        this.containers.append(jQuery('<div class="cms-fake-block">&nbsp;</div>'));
    },

    /**
     * Stops the container sorting
     *
     * @param event
     * @param ui
     */
    stopContainerSort: function(event, ui) {
        this.containers.removeClass(this.dropZoneClass);
        jQuery('div.cms-fake-block').remove();
        this.refreshLayers();
    },

//    /**
//     * Handle a click on the block
//     *
//     * @param event
//     */
//    handleBlockClick: function(event) {
//        var target = event.currentTarget,
//            id = jQuery(target).attr('data-id');
//
//        //window.open(this.url.block_edit.replace(/BLOCK_ID/, id), '_newtab');
//        this.showPopup(this.initializeSettingsPopup(id),this.url.block_edit.replace(/BLOCK_ID/, id));
//
//        event.preventDefault();
//        event.stopPropagation();
//    },

    handleBlockEdit: function(event) {
        var target = event.currentTarget,
            id = jQuery(target).attr('data-id'),
            page_id = jQuery(target).attr('data-page-id');

        console.log(jQuery(target));
        console.log('page_id-->'+page_id);
        console.log('block->'+id);

        //window.open(this.url.block_edit.replace(/BLOCK_ID/, id), '_newtab');
        if (page_id) {
            this.showPopup(this.initializeSettingsPopup(id, null), 'popup_settings_'+id,this.url.block_child_edit.replace(/PAGE_ID/, page_id).replace(/BLOCK_ID/, id), 'edit');
        } else {
            this.showPopup(this.initializeSettingsPopup(id, null), 'popup_settings_'+id,this.url.block_edit.replace(/BLOCK_ID/, id), 'edit');
        }

        event.preventDefault();
        event.stopPropagation();
    },

    handleBlockDelete: function(event) {
        var target = event.currentTarget,
            id = jQuery(target).attr('data-id'),
            parent_id = jQuery(target).attr('data-parent-id');

        //window.open(this.url.block_edit.replace(/BLOCK_ID/, id), '_newtab');

        console.log(this.url.block_delete.replace(/BLOCK_ID/, id));
        this.showPopup(this.initializeSettingsPopup(id, parent_id), 'popup_settings_'+id,this.url.block_delete.replace(/BLOCK_ID/, id), 'delete');

        event.preventDefault();
        event.stopPropagation();
    },

    /**
     * Handle a hover on the block
     *
     * @param event
     */
    handleBlockHover: function(event) {
        this.blocks.removeClass(this.blockHoverClass);
        jQuery(this).addClass(this.blockHoverClass);
        event.stopPropagation();
    },

    /**
     * Toggle preview mode
     *
     * @param event
     */
    togglePreviewMode: function(event) {
        if (this.editMode == 'preview')  {
            event.preventDefault();
            event.stopPropagation();
        } else if (this.editMode == 'zone')  {
            this.disableZone();
        } else if (this.editMode == 'editable')  {
            this.disableEditable();
        }
        this.editMode = 'preview';

        jQuery('body').trigger('css-class-changed-preview');
    },

    /**
     * Toggle edit mode
     *
     * @param event
     */
    toggleEditMode: function(event, el) {
        if (this.editMode == 'zone')  {
            event.preventDefault();
            event.stopPropagation();
            return false;
        } else if (this.editMode == 'editable')  {
            this.disableEditable();
        }

        this.enableZone();
    },

    /**
     * Toggle edit mode
     *
     * @param event
     */
    toggleEditable: function(event) {
        if (this.editMode == 'editable')  {
            event.preventDefault();
            event.stopPropagation();
            return false;
        } else if (this.editMode == 'zone')  {
            this.disableZone();
        }

        this.enableEditable();
    },

    /**
     * Enable zone
     */
    enableZone: function() {
        var that = this;
        that.editMode = 'zone';
        jQuery('body').addClass('cms-edit-mode');
        //jQuery('.cms-container').sortable({handle: '.cms-layout-title'});

        jQuery('.cms-container').sortable(
            {
                handle: '.cms-layout-drag',
                start: function( event, ui ) {
                    that.unWrapBlock(jQuery(ui.item));
                },
                stop: function( event, ui ) {
                    that.wrapBlock(ui.item);
                }

            }
        );

        jQuery('.cms-container').sortable('enable');
        jQuery('#page-action-save-position').show();
        this.buildLayers(this.blocks);
        this.initializeShowZone();
        jQuery('.cms-edit-link').on('click', jQuery.proxy(this.handleBlockEdit, this));
        jQuery('.cms-delete-link').on('click', jQuery.proxy(this.handleBlockDelete, this));
    },

    /**
     * Disable zone
     */
    disableZone: function() {
        jQuery('body').removeClass('cms-edit-mode');
        jQuery('div.cms-container').sortable('disable');
        jQuery('#page-action-save-position').hide();
        this.removeLayers();
    },

    /**
     * Enable editable
     */
    enableEditable: function() {
        this.editMode = 'editable';
        jQuery('.cms-block-editable').attr('contenteditable', 'true');
        jQuery('.cms-block-editable').addClass('cms-block-editable-enabled');
        jQuery('#page-action-save').show();
        //debug mode
        this.log(this.rmz_editor_config);
        this.log(this.rmz_editor_config_rmzmedia);
        //setconfiguration
        //CKEDITOR.config =  this.rmz_editor_config;
        jQuery.each(this.rmz_editor_config, function(i, item) {
            CKEDITOR.config[i] = item;
        })
        CKEDITOR.inlineAll();
    },

    /**
     * Disable editable
     */
    disableEditable: function() {
        jQuery('.cms-block-editable').removeAttr('contenteditable');
        jQuery('.cms-block-editable').removeClass('cms-block-editable-enabled');
        jQuery('#page-action-save').hide();
        for(name in CKEDITOR.instances) {
            this.log('detroy=>'.name);
            CKEDITOR.instances[name].destroy()
        }
    },

    /**
     * Build block layers
     */
    buildLayers:function(blocks) {
        var that = this;
        blocks.each(jQuery.proxy(function(index, obj) {
            that.buildLayer(obj);
        }, that));
    },

    buildLayer: function(currentBlock) {
        var block   = jQuery(currentBlock),
            role    = block.attr('data-role') || 'block',
            name    = block.attr('data-name') || 'missing data-name',
            id      = block.attr('data-id') || 'missing data-id',
            type    = block.attr('data-block-type') || 'missing data-type',
            has_parent = block.attr('data-block-has-parent') || false,
            parent_id = block.attr('data-block-parent-id') || null,
            page_id = block.attr('data-page-id') || null,
            classes = [],
            layer, title, container;

        classes.push('rz-cms-layout-layer');
        classes.push('cms-layout-role-'+role);
        classes.push('cms-layout-title');
        classes.push('span12');

        // build layer
        layer = jQuery('<div class="'+classes.join(' ')+'" ></div>');

        console.log(id+'--->'+type+'--->'+role+'--parentid->'+parent_id);


        if (role == 'block') {
            if (type == 'sonata.page.block.container') {
                block = this.wrapBlock(block);
            } else {
                block = this.manualWrapBlock(block);
            }

            var button =  "<div class='btn-group'><button class='rzcms btn btn-small btn-block dropdown-toggle' data-toggle='dropdown'><i class='icon-cog icon-large'></i></button>"+
                "<ul class='dropdown-menu'>"+
                "<li><a class='cms-edit-link' data-id='"+id+"' data-page-id='"+page_id+"' href='#'>Edit</a></li>"+
                "<li><a class='cms-delete-link' data-id='"+id+"' data-parent-id='"+parent_id+"' href='#' href='#'>Delete</a></li>"+
                "</ul></div>";
            layer.append('<span class="cms-layout-drag cms-layout-drag-'+role+' rzcms btn btn-small btn-success"><i class="icon-move icon-large"></i></span>'+button);

        } else {
            var button =  "<div class='btn-group'><button class='rzcms btn btn-small btn-container dropdown-toggle' data-toggle='dropdown'><i class='icon-cogs icon-large'></i></button>"+
                "<ul class='dropdown-menu'>"+
                "<li><a class='cms-edit-link' data-id='"+id+"' href='#'>Edit</a></li>"+
                "</ul></div>";
            layer.append('<span class="rzcms-layout-title-name-'+role+'">'+button+'</span>');
        }

        //layer.append(title);

        block.prepend(layer);

        if (has_parent) {
            block = this.wrapBlock(block);
        }
    },

    wrapBlock: function(block) {
        return block.wrap("<div class='rz-cms-row-wrapper row-fluid'></div>").wrap("<div class='rz-cms-block-wrapper span12'></div>");
    },

    manualWrapBlock: function(block) {
        return jQuery(block.html(jQuery("<div class='rz-cms-row-wrapper row-fluid'></div>").append(jQuery("<div class='rz-cms-block-wrapper span12'></div>").append(block.html()))));
    },

    unWrapBlock: function(block) {
//        console.log(block.parent().parent());
        jQuery(block.parent()).unwrap();
        jQuery(block).unwrap();
    },

    /**
     * Remove all block layers
     */
    removeLayers: function() {
        jQuery('.rz-cms-layout-layer').remove();
        jQuery('.rz-cms-block-wrapper').unwrap();
        jQuery.each( jQuery('.rz-cms-block-wrapper'), function( key, value ) {
            var temp = jQuery(value).contents();
            jQuery(value).replaceWith(temp);
        });

        jQuery('body').trigger('css-class-changed-zone');
    },

    /**
     * Refreshes the block layers
     */
    refreshLayers: function() {
        jQuery('.rz-cms-layout-layer').each(function(position) {
            var layer = jQuery(this),
                block = layer.parent();

            layer.css('min-width', block.width());
            //layer.css('min-height', block.height());
        });
    },


    saveInlineEdits: function(event) {
        event.preventDefault();
        event.stopPropagation();
        this.log('--- saveInlineEdits START ---');
        // check for modifications before saving
        var editedData = [];

        for(name in CKEDITOR.instances) {
            if (CKEDITOR.instances[name].checkDirty()) {
                var temp = new Object();
                temp.id = jQuery("#"+name).attr('data-id');
                temp.content = CKEDITOR.instances[name].getData();
                this.log(temp);
                editedData.push(temp);
            } else {
                this.log('No Data modifications for '+name)
            }
        }

        if (editedData.length !== 0 ) {
            this.saveEditedBlocks(editedData);
        } else {
            this.notification('Notification', 'No modifications.', false);
        }
        this.log('--- saveInlineEdits END ---');
    },

    saveEditedBlocks: function(ckeditor_data) {

        this.log('--- saveEditedBlocks START ---');

        var data = { data: JSON.stringify(ckeditor_data) };
        this.log(data);

        // generate URL require FOSJsRoutingBundle
        var url = Routing.generate('admin_sonata_page_block_saveTextBlock', null, true);
        this.log(url);

        //this.block();
        var jqxhr = jQuery.ajax(
            {
                dataType: 'json',
                type: "POST",
                data: data,
                url:  url
            })
            .done(jQuery.proxy(function(data, status, xhr) {
                this.log('saveEditedBlocks | done | ajax request done.');
                this.log('-- data --');
                this.log(data);
                this.log('-- status --');
                this.log(status);
                this.log('-- xhr --');
                this.log(xhr);

                setTimeout(jQuery.proxy(function() {
                    rz_gritter.addPrimary('Notification', data.message);
                    this.resetCKEditorData();
                }, this, data), 500);

            }, this))
            .fail(jQuery.proxy(function(data, status, error) {
                this.log('saveEditedBlocks | fail | ajax request fail.');
                this.log('-- data --');
                this.log(data);
                this.log('-- status --');
                this.log(status);
                this.log('-- error --');
                this.log(error);
                setTimeout(function() {
                    //popup dialog
                }, 500);
            }, this))
            .always(jQuery.proxy(function(data, status, xhr) {
                this.log('saveEditedBlocks | always | ajax request done.');
                this.log('-- data --');
                this.log(data);
                this.log('-- status --');
                this.log(status);
                this.log('-- xhr --');
                this.log(xhr);
            }, this));

        this.log('--- saveEditedBlocks END ---');
    },

    resetCKEditorData: function() {
        for(name in CKEDITOR.instances) {
            if (CKEDITOR.instances[name].checkDirty()) {
                CKEDITOR.instances[name].resetDirty();
            }
        }
    },

    /**
     * Build block data used to perform a database update of block position and hierarchy
     *
     * @return {Array} An array of block information with id, position, and parent id
     */
    buildBlockData: function() {
        var data = [];

        this.blocks.each(jQuery.proxy(function(index, block) {
            var item = this.buildSingleBlockData(block)
            if (item) {
                data.push(item);
            }
        }, this));

        // sort items on page, parent and position
        data.sort(function(a, b) {
            if (a.page_id == b.page_id) {
                if (a.parent_id == b.parent_id) {
                    return a.position - b.position;
                }
                return a.parent_id - b.parent_id;
            }
            return a.page_id - b.page_id;
        })

        return data;
    },

    /**
     * Builds a single block data
     *
     * @param original
     */
    buildSingleBlockData: function(original) {
        var block, id, parent, parentId, pageId, previous, position;

        block = jQuery(original);

        // retrieve current block id
        id = block.attr('data-id');
        if (!id) {
            this.log('Block has no data-id, ignored !');
            return;
        }

        // retrieve parent block container
        parent = this.findParentContainer(block);
        if (!parent) {
            this.log('Block '+id+' has no parent, it must be a root container, ignored');
            return;
        }
        parentId = jQuery(parent).attr('data-id');

        // retrieve root's page (because a root container cannot be moved)
        root = this.findRootContainer(block);
        if (!root) {
            this.log('Block '+id+' has no root but has a parent, should never happen!');
            return;
        }
        pageId = jQuery(root).attr('data-page-id');

        // get previous siblings to count position
        previous = block.prevAll(this.blockSelector+'[data-id]');
        position = previous.length + 1;

        if (!id || !parentId) {
            return;
        }

        return {
            id:        id,
            position:  position,
            parent_id: parentId,
            page_id:   pageId
        };
    },

    /**
     * Returns an array with differences from 2 arrays
     *
     * @param previousData Previous data
     * @param newData      New data
     *
     * @return Array
     */
    buildDiffBlockData: function(previousData, newData) {
        var diff = [];

        jQuery.map(previousData, function(previousItem, index) {
            var found;

            found = jQuery.grep(newData, function(newItem, index) {
                if (previousItem.id != newItem.id) {
                    return false;
                }

                if (previousItem.position != newItem.position || previousItem.parent_id != newItem.parent_id || previousItem.page_id != newItem.page_id) {
                    return true;
                }
            });

            if (found && found[0]) {
                diff.push(found[0]);
            }
        });

        return diff;
    },

    /**
     * Returns the parent container of a block
     *
     * @param block
     *
     * @return {*}
     */
    findParentContainer: function(block) {
        var parents, parent;

        parents = jQuery(block).parents(this.containerSelector+'[data-id]');
        parent = parents.get(0);

        return parent;
    },

    /**
     * Returns the root container of a block
     *
     * @param block
     *
     * @return {*}
     */
    findRootContainer: function(block) {
        var parents, root;

        parents = jQuery(block).parents(this.containerSelector+'[data-id]');
        root = parents.get(-1);

        return root;
    },

    /**
     * Save block layout to server
     *
     * @param event
     */
    saveBlockLayout: function(event) {
        var diff;

        event.preventDefault();
        event.stopPropagation();

        diff = this.buildDiffBlockData(this.data, this.buildBlockData());

        if (diff.length == 0) {
            alert('No changes found.');
            return;
        }

        jQuery.blockUI({ message: this.loadingMessage(null)});

        jQuery.each(diff, jQuery.proxy(function(item, block) {
            this.log('Update block '+block.id+ ' (Page '+block.page_id+'), parent '+block.parent_id+', at position '+block.position+')');
        }, this));

        jQuery.ajax({
            type: 'POST',
            url: this.url.block_save_position,
            data: { disposition: diff },
            dataType: 'json'})
            .done(jQuery.proxy(function(data, status, xhr) {
                if (data.result == 'ok') {
                    setTimeout(jQuery.proxy(function() {
                        rz_gritter.addSuccess('Notification', 'Block ordering saved!');
                        this.resetCKEditorData();
                    }, this, data), 500);
                    // re-initialize block data to consider as the new "previous" values
                    this.initBlockData();
                } else {
                    this.log(data);
                    rz_gritter.addDanger('Error', 'Server could not save block ordering!');
                }
            }, this))
            .fail(jQuery.proxy(function(xhr, status, error) {
                this.log('Unable to save block ordering: '+ error);
                this.log(status);
                this.log(xhr);
            }, this));
    },

    /**
     *
     * Added By:
     * @author mell m. zamora
     *
     * Requires jQuery Gritter Plugin
     * A growl-like notifications
     * Included on RmzamoraSonataExtAdminBundle
     *
     */
    notification: function(title, message, isSticky) {
        console.log('gritter');
        jQuery.gritter.add({
            title:	title,
            text:	message,
            sticky: isSticky,
            time: 8000
        });
    },

    loadingMessage: function(msg) {
        msg = msg ? msg : 'Please wait while we process your request.';
        return '<div id="gritter-notice-wrapper-blockui-loading"><div id="gritter-item-blockui-loading" class="gritter-item-wrapper gritter-primary" style=""><div class="gritter-top"></div><div class="gritter-item"><div class="gritter-close" style="display: none;"></div><div class="gritter-without-image"><span class="gritter-title"><i class="icon-spinner"></i> Processing...</span><p>'+msg+'</p></div><div style="clear:both"></div></div><div class="gritter-bottom"></div></div></div>';
    },

    /**
     * Log messages
     */
    log: function() {
        if (!this.debug) {
            return;
        }

        try {
            console.log(arguments);
        } catch(e) {

        }
    },

    initializeSettingsPopup: function(id, parent_id) {
        //var popupTempId = 'popup_settings_'+id;
        return jQuery("<div class='rzcms rz-cms-admin modal hide fade' data-backdrop='static' data-id='"+id+"' data-parent-id='"+parent_id+"' id='popup_settings_"+id+"'  aria-hidden='false'></div>");
        //jQuery(document.body).append(div);
        //return popupTempId;
    },

    initializeShowZone: function() {
        jQuery('.cms-block').hoverIntent({
            over: function() {
                jQuery(this).find('.overlay').stop(true, true).fadeIn(330);
                jQuery(this).find('.plugin-stats').stop(true, true).animate({bottom: 0}, 330, 'easeOutQuad');
            },

            out: function() {
                jQuery(this).find('.overlay').stop(true, true).fadeOut(300);
                jQuery(this).find('.plugin-stats').stop(true, true).animate({bottom: -33}, 300, 'easeOutQuad');
            },

            interval: 0,
            timeout: 0
        });

    },

    showPopup: function(modal, id, url, popupType) {

        jQuery.blockUI({ message: rzadmin.loadingMessage(null)});

        var that = this;

        if (jQuery('#'+id).length <= 0) {
            jQuery(modal).appendTo(document.body);
        }

        // retrieve the form element from the related admin generator
        jQuery.ajax({
            url: url,
            dataType: 'html'
        })
            .done(function(html, textStatus, jqXHR) {
                jQuery('#'+id).html(html);

                var dialog =   jQuery('#'+id);

                jQuery('a:not([data-toggle="tab"],[data-toggle="pill"])',dialog).on('click', jQuery.proxy(function(event){
                    event.preventDefault();
                    event.stopPropagation();
                }, dialog));

                jQuery('form', dialog).on('submit', jQuery.proxy(function(event){
                    event.preventDefault();
                    event.stopPropagation();
                    that.processDialogSubmit(event.target, dialog, popupType);
                }, dialog));

                var is_closed = false;
                var init_width = Math.round(jQuery(window).width() - (jQuery(window).width() * .2));
                init_width = (init_width > 980) ? 980 :  init_width;
                dialog.modal({'width': init_width });
                dialog.on('hidden', function (event) {

                    event.preventDefault();
                    event.stopPropagation();

                    if (jQuery(event.target).hasClass('admin-filter-ajax')) {
                        return false;
                    } else {
                        jQuery('a:not([class*="admin-ajax-ignore-event"], [class*="admin-ajax-ignore-tabbable"]), a[class*="rz-admin-filter-reset-button"]', jQuery(this)).off('click');
                        jQuery('form, a:not([class*="rz-admin-filter-reset-button"])', jQuery(this)).off('submit');
                        jQuery(this).empty();
                        jQuery(this).remove();
                        return false;
                    }
                });

                dialog.on('shown', function (event) {
                    rzadmin.addFilters(jQuery(this));
                    rzadmin.initElements(jQuery(this));

                });
            })
            .fail(function(jqXHR, textStatus, errorThrown){
                console.log('fail');
            })
            .always(function() {
                console.log('always');
            });
    },

    processDialogSubmit: function(link, dialog, popupType) {

        jQuery.blockUI({ message: rzadmin.loadingMessage(null)});

        var that = this;
        var params = {'id': dialog.attr('data-id'), 'parent_id':dialog.attr('data-parent-id'), 'dialog': dialog, 'that': that };

        console.log(params);

        var form = jQuery(link);
        if (form.is('FORM')) {
            var url  = form.attr('action');
            var type = form.attr('method');
            var dataType = 'json';
            var data = {_xml_http_request: true}
        } else {
            alert('unexpected element : @' + this.nodeName + '@');
            return;
        }

        that.CKupdate();

        // the ajax post
        jQuery(form).ajaxSubmit({
            url: url,
            type: type,
            data: data,
            dataType: dataType,
            success: jQuery.proxy(function(data) {
                if (data.result == 'ok') {

                    console.log(popupType);
                    //location.reload();
                    if (popupType == 'edit') {
                        params.that.renderBlock({'id':params.id, 'dialog': params.dialog, 'that': params.that});
                    } else {
                        params.that.renderBlock({'id':params.parent_id, 'dialog': params.dialog, 'that': params.that});
                    }
                    params.dialog.modal('hide');
                }
            }, params)
        });

        return;
    },

    renderBlock: function(params) {
        jQuery.blockUI({ message: rzadmin.loadingMessage(null)});

        console.log(params);
        var that = this;
        pageId = jQuery(root).attr('data-page-id');
        var url = Routing.generate('admin_sonata_page_block_cmsBlockRender', {'pageId':pageId,'blockId': params.id}, true);
        // retrieve the form element from the related admin generator
        jQuery.ajax({
            url: url,
            dataType: 'html'
        })
            .done(jQuery.proxy(function(html, textStatus, jqXHR) {
                params.that.togglePreviewMode();
                jQuery('#cms-block-'+params.id).replaceWith(html);
                params.that.initInterface();
                params.that.initBlocks();
                params.that.initContainers();
                params.that.initBlockData();
                params.that.toggleEditMode();
            }, params))
            .fail(function(jqXHR, textStatus, errorThrown){
                console.log('fail');
            })
            .always(function() {
                console.log('always');
            });
    },

    CKupdate: function(){
        for ( instance in CKEDITOR.instances )
            CKEDITOR.instances[instance].updateElement();
    }
}
