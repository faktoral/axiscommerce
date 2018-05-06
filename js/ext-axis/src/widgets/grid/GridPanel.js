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

/**
 * GridPanel for standard Axis 2-columns layout
 */
Axis.grid.GridPanel = Ext.extend(Ext.grid.GridPanel, {

    autoExpandMax: 1600,

    collapsible: true,

    header: false,

    massAction: true,

    region: 'center',

    split: true,

    stripeRows: true,

    viewConfig: {
        emptyText: 'No records found'.l()
    },

    width: 220,

    initComponent: function() {
        if (this.massAction && !this.sm) {
            this.sm = new Ext.grid.CheckboxSelectionModel();
            this.cm.config.splice(0, 0, this.sm);
        }
        Axis.grid.GridPanel.superclass.initComponent.call(this);

        this.getStore().on({
            scope       : this,
            beforeload  : this.beforeLoad
        })
    },

    beforeLoad: function(store, options) {
        var state           = store.sortInfo,
            cm              = this.getColumnModel(),
            col             = null
            options.params  = options.params || {};
        if (state) {
            if (cm.columns) {
                col = cm.columns[cm.findColumnIndex(state.field)];
            } else {
                Ext.each(cm.config, function(col) {
                    if (col.dataIndex == state.field) {
                        return false;
                    }
                });
            }
            if (col) {
                var field = col.sortName ? col.sortName     : state.field;
                var table = col.table    ? col.table + '.'  : '';
                options.params.sort = table + field;
            }
        }
    }
});
