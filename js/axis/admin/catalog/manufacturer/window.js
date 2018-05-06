/**
 * Axis
 *
 * This file is part of Axis.
 *
 * Axis is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Axis is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Axis.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @copyright   Copyright 2008-2012 Axis
 * @license     GNU Public License V3.0
 */

Ext.onReady(function(){

    Ext.QuickTips.init();

    Ext.form.Field.prototype.msgTarget = 'qtip';

    var form = new Axis.FormPanel({
        bodyStyle: 'padding: 10px;',
        id: 'form',
        defaults: {
            anchor: '-20'
        },
        items: [{
            xtype: 'hidden',
            name: 'id'
        }, {
            fieldLabel: 'Name'.l(),
            xtype: 'textfield',
            name: 'name',
            allowBlank: false
        }, {
            fieldLabel: 'Url'.l(),
            xtype: 'textfield',
            name: 'key_word',
            allowBlank: false
        }, {
            allowBlank: false,
            fieldLabel: 'Title'.l(),
            defaultType: 'textfield',
            name: 'description[title]',
            xtype: 'langset'
        }, {
            fieldLabel: 'Image'.l(),
            url: Axis.getUrl('catalog/manufacturer/save-image'),
            name: 'image',
            rootPath: 'media/manufacturer',
            rootText: 'manufacturer',
            xtype: 'imageuploadfield'
        }, {
            fieldLabel: 'Description'.l(),
            defaultType: 'ckeditor',
            height: 100,
            name: 'description[description]',
            xtype: 'langset'
        }]
    });

    var window = new Axis.Window({
        id: 'window',
        title: 'Manufacturer'.l(),
        items: form,
        buttons: [{
            text: 'Save'.l(),
            handler: function(){
                form.getForm().submit({
                    url: Axis.getUrl('catalog/manufacturer/save'),
                    method: 'post',
                    success: function(){
                        window.hide();
                        Ext.getCmp('grid').store.reload();
                    }
                });
            }
        }, {
            text: 'Cancel'.l(),
            handler: function(){
                window.hide();
            }
        }]
    });
});
