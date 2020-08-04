<?php
$_SESSION['page']=$_GET['u'];
?><div align='left'><h1 class="head">Message</h1></div><script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#example').dataTable( {
					"bProcessing": true,
					"bServerSide": true,
					"bJQueryUI": true,
					"aaSorting": [[ 0, "desc" ]],
					"sPaginationType": "full_numbers",
					"sAjaxSource": "<?=Url_Create('data')?>"
				} );
			} );
		</script>
		<div id="container">
			
			
			<div id="dynamic">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
	<thead>
		<tr>
		<th width="15%">Tickle Name</th>
			<th width="20%">To</th>
			
			<th width="25%">Subject</th>
			<th width="25%">Date</th>
            <th width="10%">Options</th>
            
		</tr>
	</thead>
	<tbody>
		<tr>
			<td colspan="5" class="dataTables_empty">Loading data from server</td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
		<th>Tickle Name</th>
			<th>To</th>
			
			<th>Subject</th>
			<th>Date</th>
            <th>Options</th>
            
		</tr>
	</tfoot>
</table>
			</div>
			<div class="spacer"></div>
			
			
		
		</div>