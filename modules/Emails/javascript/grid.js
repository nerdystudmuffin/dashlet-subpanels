/*********************************************************************************
 * SugarCRM is a customer relationship management program developed by
 * SugarCRM, Inc. Copyright (C) 2004 - 2009 SugarCRM Inc.
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation with the addition of the following permission added
 * to Section 15 as permitted in Section 7(a): FOR ANY PART OF THE COVERED WORK
 * IN WHICH THE COPYRIGHT IS OWNED BY SUGARCRM, SUGARCRM DISCLAIMS THE WARRANTY
 * OF NON INFRINGEMENT OF THIRD PARTY RIGHTS.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * 
 * You can contact SugarCRM, Inc. headquarters at 10050 North Wolfe Road,
 * SW2-130, Cupertino, CA 95014, USA. or at email address contact@sugarcrm.com.
 * 
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 * 
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "Powered by
 * SugarCRM" logo. If the display of the logo is not reasonably feasible for
 * technical reasons, the Appropriate Legal Notices must display the words
 * "Powered by SugarCRM".
 ********************************************************************************/
function gridInit() {
	if(SUGAR.email2.grid) {
		SUGAR.email2.grid.destroy();
	}
	
	e2Grid = {
		init : function() {
			var Ck = YAHOO.util.Cookie;
			var widths = [ 10, 10, 150, 250, 100, 125 ];
			
			if (Ck.get("EmailGridWidths")) {
				for (var i=0; i < widths.length; i++) {
					widths[i] = Ck.getSub("EmailGridWidths", i+ "", Number);
				}
			} else {
				for (var i=0; i < widths.length; i++) {
					Ck.setSub("EmailGridWidths", i + "", widths[i], {expires: SUGAR.email2.nextYear});
				}
			}
			
			// changes "F" to an icon
			function flaggedIcon(value) {
				if(value != "") {
					return "<span style='color: #f00; font-weight:bold;'>!</a>";
				}
			}
			// changes "A" to replied icon
			function repliedIcon(value) {
				if(value != "") {
					return "<img src='index.php?entryPoint=getImage&themeName="+SUGAR.themes.theme_name+"&imageName=export.gif' class='image' border='0' width='10' align='absmiddle'>";
				}
			}
	
			var colModel = 
				[
					{
						label: "&nbsp;", 
						width: widths[0], 
						sortable: true, 
						fixed: true,
						resizeable: false,
						renderer: flaggedIcon,
						key: 'flagged'
					}, 
					{
						label: "&nbsp;", 
						width: widths[1], 
						sortable: true, 
						fixed: true,
						resizeable: false,
						renderer: repliedIcon,
						key: 'status'
					},
					{
						label: app_strings.LBL_EMAIL_FROM, 
						width: widths[2],
						sortable: true,
						resizeable: true,
						key: 'from'
					}, 
					{
						label: app_strings.LBL_EMAIL_SUBJECT,
						width: widths[3], 
						sortable: true,
						resizeable: true,
						key: 'subject'
					}, 
					{
						label: app_strings.LBL_EMAIL_DATE_RECEIVED,
						width: widths[4], 
						sortable: true,
						resizeable: true,
                        key: 'date'
					}, 
					{
						label: app_strings.LBL_EMAIL_TO,
						width: widths[5], 
						sortable: false,
						resizeable: true,
                        key: 'to_addrs'
					}, 
					{
						label: 'uid',
						hidden: true,
                        key: 'uid'
					}, 
					{
						label: 'mbox',
						hidden: true,
                        key: 'mbox'
					}, 
					{
						label: 'ieId',
						hidden: true,
                        key: 'ieId'
					}, 
					{	
						label: 'site_url',
						hidden: true,
                        key: 'site_url'
					},
					{	label: 'seen',
						hidden: true,
                        key: 'seen'
					},
					{	label: 'type',
						hidden: true,
                        key: 'type'
					}
				];
			
			var dataModel = new YAHOO.util.DataSource(urlBase + "?", {
				responseType: YAHOO.util.XHRDataSource.TYPE_XML,
                // the return will be XML, so lets set up a reader
				responseSchema: {
					metaNode: 'EmailPage',
					resultNode: 'Email',
		            total: 'TotalCount', 
		            fields: ['flagged', 'status', 'from', 'subject', 'date','to_addrs', 'uid', 'mbox', 'ieId', 'site_url', 'seen', 'type', 'AssignedTo'],
		            metaFields: {total: 'TotalCount', unread:"UnreadCount", fromCache: "FromCache"}
				}
		    });
			var params = {
					to_pdf : "true",
					module : "Emails",
					action : "EmailUIAjax",
					emailUIAction : "getMessageListXML",
					mbox : "INBOX",
					ieId : "",
					forceRefresh : "false"
			};
			if(lazyLoadFolder != null) {
				params['mbox'] = lazyLoadFolder.folder;
				params['ieId'] = lazyLoadFolder.ieId;
				//Check if the folder is a Sugar Folder
				var test = new String(lazyLoadFolder.folder);
				if(test.match(/SUGAR\./)) {
					params['emailUIAction'] = 'getMessageListSugarFoldersXML';
					params['mbox'] = test.substr(6);
				}
			}
			//dataModel.initPaging(urlBase, SUGAR.email2.userPrefs.emailSettings.showNumInList);
	
			// create the Grid
			var grid = SUGAR.email2.grid = new YAHOO.SUGAR.SelectionGrid('emailGrid', colModel, dataModel, {
				MSG_EMPTY: SUGAR.language.get("Emails", "LBL_EMPTY_FOLDER"),
				dynamicData: true,
				//TODO: Need to create our own paginator that fits into the page layout better
				paginator: new YAHOO.widget.Paginator({ 
					rowsPerPage:parseInt(SUGAR.email2.userPrefs.emailSettings.showNumInList),  
					containers : ["dt-pag-nav"]
				}),
				initialRequest:SUGAR.util.paramsToUrl(params),
				width:  "800px",
				height: "400px"
			});

			initRowDD();

			//Override Paging request construction
			grid.set("generateRequest", function(oState, oSelf) {
	            oState = oState || {pagination:null, sortedBy:null};
	            var sort = (oState.sortedBy) ? oState.sortedBy.key : oSelf.getColumnSet().keys[0].getKey();
	            var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc";
	            var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
	            var results = (oState.pagination) ? oState.pagination.rowsPerPage : null;
	            // Build the request 
	            var ret = 
		            SUGAR.util.paramsToUrl(oSelf.params) + 
		            "&sort=" + sort +
	                "&dir=" + dir +
	                "&start=" + startIndex +
	                ((results !== null) ? "&limit=" + results : "");
	            return  ret;
	        });
			
			
			grid.handleDataReturnPayload = function(oRequest, oResponse, oPayload) { 
				oPayload = oPayload || { };
				
				oPayload.totalRecords = oResponse.meta.total; 
				return oPayload; 
			}
			
			var resize = grid.resizeGrid = function () {
				SUGAR.email2.grid.set("width",  SUGAR.email2.grid.get("element").parentNode.clientWidth + "px");
				SUGAR.email2.grid.set("height", (SUGAR.email2.grid.get("element").parentNode.clientHeight - 37) + "px");
			}
			grid.convertDDRows = function() {
				var rowEl = this.getFirstTrEl();
				while (rowEl != null) {
					new this.DDRow(this, this.getRecord(rowEl), rowEl);
					rowEl = this.getNextTrEl(rowEl);
				}
			}
			
			
			grid.on("columnResizeEvent", function(o) {
				//Find the index of the column
				var colSet = SUGAR.email2.grid.getColumnSet().flat;
				for (var i=0; i < colSet.length; i++) {
					if (o.column == colSet[i]) {
						//Store it in the cookie
						Ck.setSub("EmailGridWidths", i + "", o.width, {expires: SUGAR.email2.nextYear});
					}
				}
				//this.resizeGrid();
			}, null, grid); 
			grid.on("postRenderEvent", function() {this.convertDDRows()}, null, grid);
			grid.on("rowSelectEvent", SUGAR.email2.listView.handleClick);  
			grid.on("rowDblclickEvent", SUGAR.email2.listView.getEmail);  
			grid.render();
			SUGAR.email2.listViewLayout.on("render", resize);
			resize();
			
			//Setup the default load parameters
			SUGAR.email2.grid.params = params;
			
			grid.on('postRenderEvent', SUGAR.email2.listView.setEmailListStyles);
			dataModel.subscribe("requestEvent", grid.disable, grid, true);
			dataModel.subscribe("responseParseEvent", grid.undisable, grid, true);
		}
	};
	e2Grid.init();
};


function initRowDD() {
	var sg = SUGAR.email2.grid,
	Dom = YAHOO.util.Dom;
	sg.DDRow = function(oDataTable, oRecord, elTr) {
		if(oDataTable && oRecord && elTr) {
			this.ddtable = oDataTable;
	        this.table = oDataTable.getTableEl();
	        this.row = oRecord;
	        this.rowEl = elTr;
	        this.newIndex = null;
	        this.init(elTr);
	        this.initFrame(); // Needed for DDProxy
	        this.invalidHandleTypes = {};
	    }	
	};
	
	YAHOO.extend(sg.DDRow, YAHOO.util.DDProxy, {
	    _resizeProxy: function() {
	        this.constructor.superclass._resizeProxy.apply(this, arguments);
	        var dragEl = this.getDragEl(),
	            el = this.getEl();
	        var xy = Dom.getXY(el);
	        
	        Dom.setStyle(dragEl, 'height', this.rowEl.offsetHeight + "px");
	        Dom.setStyle(dragEl, 'width', (parseInt(Dom.getStyle(dragEl, 'width'),10) + 4) + 'px');
	        Dom.setXY(dragEl, [xy[0] - 100, xy[1] - 20] );
	        Dom.setStyle(dragEl, 'display', "");
	    },
	    
	    startDrag: function(x, y) { 
	    	//Check if we should be dragging a set of rows rather than just the one.
	    	var selectedRows = this.ddtable.getSelectedRows();
	    	var iSelected = false;
	    	for (var i in selectedRows) {
	    		if (this.rowEl.id == selectedRows[i]) {
	    			iSelected = true;
	    			break
	    		}
	    	}
	    	if (iSelected) {
	    		this.rows = [];
	    		for (var i in selectedRows) {
	    			this.rows[i] = this.ddtable.getRecord(selectedRows[i]);
		    	}
	    	} else {
	    		this.rows = [this.row];
	    		this.ddtable.unselectAllRows();
	    		this.ddtable.selectRow(this.row);
	    	}
	    	
	    	//Initialize the dragable proxy
	    	var dragEl = this.getDragEl(); 
	        var clickEl = this.getEl(); 
	        Dom.setStyle(clickEl, "opacity", "0.25"); 
	        dragEl.innerHTML = "<table><tr>" + clickEl.innerHTML + "</tr></table>"; 
	    	Dom.addClass(dragEl, "yui-dt-liner");
	    	Dom.setStyle(dragEl, "opacity", "0.5"); 
	        Dom.setStyle(dragEl, "height", (clickEl.clientHeight - 2) + "px");
	        Dom.setStyle(dragEl, "backgroundColor", Dom.getStyle(clickEl, "backgroundColor")); 
	  	    Dom.setStyle(dragEl, "border", "2px solid gray"); 
	    },
	    
	    clickValidator: function(e) {
	    	if (this.row.getData()[0] == " ")
	    		return false;
	        var target = YAHOO.util.Event.getTarget(e);
	    	return ( this.isValidHandleChild(target) && 
	    			(this.id == this.handleElId || this.DDM.handleWasClicked(target, this.id)) );
	    },
	    /**
	     * This funciton checks that the target of the drag is a table row in this
	     * DDGroup and simply moves the sourceEL to that location as a preview.
	     */
	    onDragOver: function(ev, id) {
	    	var node = SUGAR.email2.tree.getNodeByElement(Dom.get(id));
	    	if (node && node != this.targetNode) {
	    		this.targetNode = node;
	    		SUGAR.email2.folders.unhighliteAll();
	    		node.highlight();
	    	}
	    },
	    
	    onDragOut: function(e, id) {
	    	if (this.targetNode) {
	    		SUGAR.email2.folders.unhighliteAll();
	    		this.targetNode = false;
	    	}
	    },
	    endDrag: function() {
	    	Dom.setStyle(this.getEl(), "opacity", "");
	    	Dom.setStyle(this.getDragEl(), "display", "none"); 
	    	if (this.targetNode) {
	    		SUGAR.email2.folders.handleDrop(this.rows, this.targetNode);
	    	}
	    	SUGAR.email2.folders.unhighliteAll();
	    	this.rows = null;
	    }
	});
}

function AddressSearchGridInit() {
    function moduleIcon(elCell, oRecord, oColumn, oData) {
    	elCell.innerHTML = "<img src='index.php?entryPoint=getImage&themeName="+SUGAR.themes.theme_name+"&imageName=" + oData + ".gif' class='image' border='0' width='16' align='absmiddle'>";
    };
    function selectionCheckBox(elCell, oRecord, oColumn, oData) {
        elCell.innerHTML =  '<input type="checkbox" onclick="SUGAR.email2.addressBook.grid.toggleSelect(\'' + oRecord.getId() + '\', this.checked);">';
    };
    var checkHeader = '<input type="checkbox" ';
    if (SUGAR.email2.util.isIe()) {
        checkHeader += 'style="top:-5px" ';
    }
    checkHeader += 'onclick="SUGAR.email2.addressBook.grid.toggleSelectAll(this.checked);">';
    var colModel = 
	    [{
	    	label: checkHeader,
            width: 30,
            formatter: selectionCheckBox,
            key: 'bean_id'
        },
	    {
        	label: '',
	        width: 25,
	        formatter: moduleIcon,
	        key: 'bean_module'
        },
	    {
        	label: app_strings.LBL_EMAIL_ADDRESS_BOOK_NAME, 
	        width: 200,
	        sortable: false,
	        key: 'name'
	    }, 
	    {
	    	label: app_strings.LBL_EMAIL_ADDRESS_BOOK_EMAIL_ADDR,
	        width: 300, 
	        sortable: false,
	        key: 'email'
	    }];
    
    var dataModel = new YAHOO.util.DataSource(urlBase + "?", {
		responseType: YAHOO.util.XHRDataSource.TYPE_XML,
        responseSchema: {
			metaNode: 'EmailPage',
			resultNode: 'Person',
            total: 'TotalCount', 
            fields: ['name', 'email', 'bean_id', 'bean_module']
    	},
        //enable sorting on the server accross all data
        remoteSort: true
    });
    dataModel.params = {
		to_pdf		: true,
		module		: "Emails",
		action		: "EmailUIAjax",
		emailUIAction:"getAddressSearchResults"
    }
    SUGAR.email2.addressBook.addressBookDataModel = dataModel;
	
    var reportsDataModel = new YAHOO.util.DataSource(urlBase + "?", {
		responseType: YAHOO.util.XHRDataSource.TYPE_XML,
        responseSchema: {
			metaNode: 'EmailPage',
			resultNode: 'Person',
            total: 'TotalCount', 
            fields: ['name', 'email', 'bean_id', 'bean_module']
    	},
        //enable sorting on the server accross all data
        remoteSort: true
    });
	reportsDataModel.params = {
		to_pdf 		: true,
		reportId	: '',
		module		: "Emails",
		action		: "EmailUIAjax",
		emailUIAction:"getReportUsers"
	};
    SUGAR.email2.addressBook.reportsDataModel = reportsDataModel;   
    
    var grid = SUGAR.email2.addressBook.grid = new YAHOO.widget.ScrollingDataTable("addrSearchGrid", colModel, dataModel, {
    	MSG_EMPTY: "&nbsp;", //SUGAR.language.get("Emails", "LBL_EMPTY_FOLDER"),
		dynamicData: true,
		paginator: new YAHOO.widget.Paginator({ 
			rowsPerPage: 20,  
			containers : ["dt-pag-nav"]
		}),
		initialRequest:SUGAR.util.paramsToUrl(dataModel.params),
		width:  "950px",
		height: "250px"
    });
    grid.render();
    dataModel.subscribe("requestEvent", grid.disable, grid, true);
    dataModel.subscribe("responseParseEvent", grid.undisable, grid, true);
    
    /*
    grid.getSelectionModel().on('beforerowselect', function(selModel, rowIndex, keep) {
    	this.clearTextSelection();
        var row = this.getDataSource().getAt(rowIndex)
        if (!row.data.checked) {
            return false;
        }
        return true;
    }, grid, grid);    
    
    grid.getSelectionModel().on('rowdeselect', function(selModel, rowIndex) {
        var row = SUGAR.email2.addressBook.grid.getDataSource().getAt(rowIndex)
        if (row.data.checked) {
            selModel.selectRow(rowIndex, true);
        }
    });*/
    
    grid.toggleSelect = function(id, checked) {
        var row = SUGAR.email2.addressBook.grid.getRecord(id);
    	row.setData("checked",  checked);
        if (checked) {
            SUGAR.email2.addressBook.grid.selectRow(row);
        } else {
            SUGAR.email2.addressBook.grid.unselectRow(row);
        }
    };
    
    grid.toggleSelectAll = function(checked) {
        rows = SUGAR.email2.addressBook.grid.getRecordSet().getRecords();
        for (var i = 0; i < rows.length; i++) {
        	rows[i].setData("checked",  checked);
        	if (checked)
        		SUGAR.email2.addressBook.grid.selectRow(rows[i]);
        	else 
        		SUGAR.email2.addressBook.grid.unselectRow(rows[i]);
        }
        var checkBoxes = SUGAR.email2.addressBook.grid.get("element").getElementsByTagName('input');
        for (var i = 0; i < checkBoxes.length; i++) {
            checkBoxes[i].checked = checked;
        }
    };
}




