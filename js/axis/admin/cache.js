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
    
    var cacheTag = new Ext.data.Record.create([
        'id',
        'name',
        'is_active',
        'lifetime'
    ]);
    
    var ds = new Ext.data.Store({
        url: Axis.getUrl('core/cache/list'),
        reader: new Ext.data.JsonReader({
            root: 'data',
            id: 'id'
        }, cacheTag),
        autoLoad: true
    });
    
    var status = new Axis.grid.CheckColumn({
        header: "Status".l(),
        dataIndex: 'is_active',
        width: 100
    });
    
    var cm = new Ext.grid.ColumnModel([
        {
            header: 'Id'.l(),
            dataIndex: 'id',
            width: 60
        }, {
            id: 'name',
            header: 'Name'.l(),
            dataIndex: 'name',
            menuDisabled: true
        }, status, {
            header: 'Lifetime'.l(),
            dataIndex: 'lifetime',
            width: 100,
            menuDisabled: true,
            editor: new Ext.form.TextField(),
            renderer: function(value) {
                if (!value) {
                    return 'default';
                }
                return value;
            }
        }
    ]);
    cm.defaultSortable = true;
    
    var grid = new Axis.grid.EditorGridPanel({
        id: 'grid-cache',
        cm: cm,
        ds: ds,
        plugins: status,
        autoExpandColumn: 'name',
        tbar: [{
            text: 'Save'.l(),
            iconCls: 'x-btn-text',
            icon: Axis.skinUrl + '/images/icons/save_multiple.png',
            handler: save
        }, {
            text: 'Clear'.l(),
            iconCls: 'x-btn-text',
            icon: Axis.skinUrl + '/images/icons/bin.png',
            handler: clear
        }, '->', {
            text: 'Reload'.l(),
            iconCls: 'x-btn-text',
            icon: Axis.skinUrl + '/images/icons/refresh.png',
            handler: reload
        }]
    });
    
    new Axis.Panel({
        items: [
            grid
        ]
    });
    
    function reload() {
        ds.reload();
    }
})

function save(flag) {
    if (!flag) {
        var modified = Ext.getCmp('grid-cache').getStore().getModifiedRecords();
        if (!modified.length) {
            return false;
        }
    }
    var data = {};
    Ext.getCmp('grid-cache').getStore().each(function(record) {
        var row = record['data'];
        if (flag == 'disable') {
            row.is_active = 0;
        } else if (flag == 'enable') {
            row.is_active = 1;
        } 
        data[record.id] = row;
    })
    
    var jsonData = Ext.encode(data);
    Ext.Ajax.request({
        url: Axis.getUrl('core/cache/batch-save'),
        params: {data: jsonData},
        callback: function() {
            Ext.getCmp('grid-cache').getStore().commitChanges();
            Ext.getCmp('grid-cache').getStore().reload();
        }
    })
}

function clear() {
    if (!Ext.getCmp('grid-cache').getSelectionModel().getSelections().length) {
        return false;
    }
    
    var data = {};
    Ext.getCmp('grid-cache').getSelectionModel().each(function(record) {
        data[record.id] = record.get('name');
    });
    
    var jsonData = Ext.encode(data);
    Ext.Ajax.request({
        url: Axis.getUrl('core/cache/remove'),
        params: {data: jsonData}
    })
}

function clearAll() {
    Ext.Ajax.request({
        url: Axis.getUrl('core/cache/remove')
    })
}