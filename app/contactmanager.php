<div align='left'><h1 class="head">Contact Manager</h1></div>
		<div id="container">
                    <fieldset>
<?include_once "editcontactform.php"?>
</fieldset>
<div class="spacer"></div>
                    <fieldset>
<?/*h1 class="head">Manage Groups</h1>
<?include_once "editgroupform.php"*/?>
<h1 class="head">Edit Groups</h1>
<script type="text/javascript" charset="utf-8">
    var cTable = null;
    function renameCategory(id, name){
        $.get('<?=Url_Create('contactmanager')?>', { CategoryID:id, action:'EditGroupForm', redirect:'contactmanager'},function(data){mdialog("Edit group",data);});
        return false;    
        
        var nname = $.trim(prompt("New name", name));
        if (nname!=name && nname!=""){
            if (nname.length>255){
                alert('Name length is exceeded');
                return;
            }
            
            window.location.href="<?=Url_Create("contactmanager", "action=EditCategory")?>"+"&CategoryID="+id+"&CategoryName="+nname;
        }
    }
			$(document).ready(function() {
				cTable = $('#ListCategory').dataTable( {
					"bProcessing": true,
					"bServerSide": true,
					"bJQueryUI": true,
					"aaSorting": [[ 0, "asc" ]],
					"aoColumns": [
					null,
			{ "bSortable": false }

					],
					"sPaginationType": "full_numbers",
					"sAjaxSource": "<?=Url_Create("contactmanager", "action=ListCategory")?>"
				} );
			} );
		</script>
			<div id="dynamic">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="ListCategory">
	<thead>
		<tr>
		<th width="10%">Group Name</th>
			<th width="20%">Options</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="2" class="dataTables_empty">Loading data from server</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
		<th>Group Name</th>
			<th>Options</th>
		</tr>
	</tfoot>
</table>
			</div>
</fieldset>
			<div class="spacer"></div>
                        <fieldset>
                            <h1 class="head">
                               Search groups
                            </h1>
                                <div>View all contacts<input type="button" name="ViewAll" value="Go" onclick="reloadSearchContact(this.name)"/></div>
                                <div>Filter by group
<select name="groupfilter" id="ViewGroup" size="1">
<option value="">Select Group</option>
<option value="0">Unassigned</option>
<?php echo $glist;?>
</select><input type="button" name="ViewGroup" value="Go" onclick="reloadSearchContact(this.name)"/>
<a href="javascript:void()" class="link" style="float:right;" target="blank" id="export_link" onclick="return ExportContacts(this)">Export contacts</a></div>
<script type="text/javascript" charset="utf-8">
    var oTable = null;
    
    function ExportContacts(elm){
        var url = '<?=Url_Create("contactmanager", "action=ExportContacts")?>';
        if ($("#ViewGroup").val()!=""){
            url+="&CategoryID="+$("#ViewGroup").val();
        }
        if ($("#SearchContact_filter").children("input").val()!=""){
            url+="&search="+$("#SearchContact_filter").children("input").val();
        }
        $(elm).attr("href",url);
        return true;
    }
    
    function reloadSearchContact(mode){
        if (!oTable){
            return;
        }
        var oSettings = oTable.fnSettings();
        if (mode=="ViewAll"){
            oSettings.sAjaxSource = "<?=Url_Create("contactmanager", "action=SearchContact")?>";
        }
        if (mode=="ViewGroup"){
            var catId = $("#ViewGroup").val();
            oSettings.sAjaxSource = "<?=Url_Create("contactmanager", "action=SearchContact&CategoryID=")?>"+catId;
        }
        oTable.fnClearTable( 0 );
        oTable.fnDraw();

    }
    $(document).ready(function() {
            oTable = $('#SearchContact').dataTable( {
                    "bProcessing": true,
                    "bServerSide": true,
                    "bJQueryUI": true,
                    "aaSorting": [[ 0, "asc" ]],
                    "aoColumns": [
                    null,
                    null,
                    null,
                    null,
                    { "bSortable": false }
                    ],
                    "sPaginationType": "full_numbers",
                    "sAjaxSource": "<?=Url_Create("contactmanager", "action=SearchContact")?>",
                    "sDom": 'T<"fg-toolbar ui-widget-header ui-corner-tl ui-corner-tr ui-helper-clearfix"lfr>t<"fg-toolbar ui-widget-header ui-corner-bl ui-corner-br ui-helper-clearfix"ip>',
                    "oTableTools": {
                    "sSwfPath": "app/media/swf/copy_cvs_xls_pdf.swf",
                    "aButtons": []
                    }

            });
    });
</script>
		<div id="container">
			<div id="dynamic">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="SearchContact">
	<thead>
		<tr>
                        <th width="10%">Group</th>
			<th width="20%">FirstName</th>
			<th width="25%">LastName</th>
			<th width="25%">Email ID</th>
			<th width="15%">Option</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="5" class="dataTables_empty">Loading data from server</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
		<th>Group</th>
			<th>FirstName</th>
			<th>LastName</th>
			<th>Email ID</th>
			<th>Option</th>
		</tr>
	</tfoot>
</table>
			</div>
			<div class="spacer"></div>
		</div>
                        </fieldset>
                </div>