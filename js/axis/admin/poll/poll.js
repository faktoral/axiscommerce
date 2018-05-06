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

Ext.onReady(function () {

    var iteratorNewAnswer = 0;
    Poll = function() {

        function _getNewAnswerId()
        {
            return  --iteratorNewAnswer; //global var
        }

        function _clearQuestion() {
            for (var languageId in Axis.languages) {
                Ext.getCmp('description[' + languageId + ']').setValue();
            }
            return true;
        }

        function _clearAnswers() {
            for (var languageId in Axis.languages) {
                Ext.getCmp('answers-rowset-' + languageId).removeAll();
            }
            return true;
        }

        return {
            //////////////////////////////////
            getResults: function(row) {
                Ext.StoreMgr.lookup('storeResults').load({
                    params: {
                        'id': row.id
                    }
                })
                Ext.getCmp('window-question-result').setTitle(row.question);
                Ext.getCmp('window-question-result').show();
            },
            /////////////////////////////////////
            removeAnswerRow: function(answerId) {
                for (var languageId in Axis.languages) {
                    var rowset = Ext.getCmp('answers-rowset-' + languageId);
                    rowset.remove(
                        'column-answer-text[' +
                        languageId + '][' + answerId + ']'
                    );
                    rowset.remove(
                        'column-answer-delete-button[' +
                        languageId + '][' + answerId + ']'
                    );
                    rowset.doLayout();
                }
            },
            //////////////////////////////
            addAnswerRow: function(answerId, values) {
                answerId = answerId || _getNewAnswerId();
                if (!values) {
                    var values = {};
                    for (var languageId in Axis.languages) {
                        values[languageId] = '';
                    }
                }
                for (var languageId in Axis.languages) {
                    var rowset = Ext.getCmp('answers-rowset-' + languageId);
                    rowset.add({
                        layout: 'form',
                        id: 'column-answer-text[' + languageId + '][' + answerId + ']',
                        border: false,
                        columnWidth: 0.95,
                        items:[{
                            xtype: 'textfield',
                            value: values[languageId],
                            name: 'answer[' + answerId + '][' + languageId + ']',
                            anchor: "98%",
                            hideLabel: true
                        }]
                    });
                    rowset.add({
                        layout: 'form',
                        id: 'column-answer-delete-button[' + languageId + '][' + answerId + ']',
                        border: false,
                        columnWidth: 0.05,
                        items:[{
                            xtype: 'button',
                            icon: Axis.skinUrl + '/images/icons/delete.png',
                            anchor: "100%",
                            handler: function() {
                                Poll().removeAnswerRow(answerId);
                            }
                        }]
                    });
                    rowset.doLayout();
                }
                return true;
            },
            //////////////////////////////
            editQuestion: function(id) {
                Ext.getCmp('window-question').show();
                Ext.getCmp('form-question').getForm().clear();
                Ext.getCmp('form-question').getForm().load({
                    url: Axis.getUrl('poll/load'),
                    params : {id : id},
                    success: function(form, action) {
                        var response = Ext.decode(action.response.responseText).data[0];
                        _clearAnswers();
                        Ext.getCmp('window-question').setTitle(response.description[Axis.language]);
                        var answers = response.answer;
                        for (var answerId in answers) {
                            Poll().addAnswerRow(
                                answerId, answers[answerId].text
                            );
                        }
                    }
                });
            },
//          \\|//////////////////////////////
/*         -*/batchSave: function() {///.\
//          //|\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\
                var modified = Ext.getCmp('grid-poll').getStore().getModifiedRecords();
                if (modified.length < 1) return alert('Nothing to save');
                var data = {};
                for (var i = 0; i < modified.length; i++) {
                    var _row = modified[i]['data'];
                    data[i] = {
                        'id'     : _row.id,
                        'status' : _row.status,
                        'type'   : _row.type,
                        'sites'  : _row.sites
                    };
                }
                Ext.Ajax.request({
                    url: Axis.getUrl('poll/batch-save'),
                    params: {data: Ext.encode(data)},
                    callback: function() {
                        Ext.getCmp('grid-poll').getStore().commitChanges();
                        Ext.getCmp('grid-poll').getStore().reload();
                    }
                })
            },
            //////////////////////////////
            saveQuestion: function() {
                Ext.getCmp('form-question').getForm().submit({
                    url: Axis.getUrl('poll/save'),
                    success: function(form, response) {
                        Ext.getCmp('window-question').hide();
                        form.clear();
                        Ext.getCmp('grid-poll').getStore().reload();
                    }
                });

            },
            removeBatch:function() {
                var selectedItems = Ext.getCmp('grid-poll')
                    .getSelectionModel().selections.items;
                if (selectedItems.length < 1) return;
                if (!confirm('Are you sure?'.l())) return;
                var data = {};
                for (var i = 0; i < selectedItems.length; i++) {
                    data[i] = selectedItems[i].id;
                }
                Ext.Ajax.request({
                    params: {data: Ext.encode(data)},
                    url: Axis.getUrl('poll/remove'),
                    callback: function() {
                        Ext.getCmp('grid-poll').getStore().reload();
                    }
                });
            },
            remove: function(text, id) {
                if (!confirm('Delete question: [ '+ text + ' ]')) {
                    return;
                }
                Ext.Ajax.request({
                    params : {data :  [id]},
                    url: Axis.getUrl('poll/remove'),
                    callback: function(response, options) {
                        Ext.getCmp('grid-poll').getStore().reload();
                    }
                })
            },
            clearVoted: function() {
                var selectedItems = Ext.getCmp('grid-poll')
                        .getSelectionModel().selections.items;

                if (selectedItems.length < 1) {
                    return;
                }
                if (!confirm('Clear Voted?')) {
                    return;
                }
                var data = {};
                for (var i = 0; i < selectedItems.length; i++) {
                    data[i] = selectedItems[i].id;
                }
                Ext.Ajax.request({
                    url: Axis.getUrl('poll/clear'),
                    params: {data: Ext.encode(data)},
                    callback: function() {
                        Ext.getCmp('grid-poll').getStore().reload();
                    }
                });
            },
            addQuestion: function() {
                _clearQuestion();
                _clearAnswers();
                Ext.getCmp('window-question').setTitle('New Question'.l());
                Ext.getCmp('window-question').show();
                Ext.getCmp('form-question').getForm().clear();
            }

        }//end return
    }
});
//eof