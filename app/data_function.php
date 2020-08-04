<?php
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Easy set variables
	 */
	
	/* Array of database columns which should be read and sent back to DataTables. Use a space where
	 * you want to insert a non-database field (for example a counter or static image)
	 */
	 $JoinTickleID="TickleID";
	 if($_SESSION['page']=="message")
	 {
	$aColumns = array('um.TickleTitle','um.toaddress','um.Subject','um.Date','um.MailID' );
	$JoinTickleID="um.TickleID";
        $WhereCond=" AND ParentID=0";
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "um.TickleTitle";
	
	/* DB table to use */
	$sTable = "user_mail um inner join task ts on (um.MailID = ts.MailID and ts.Status='Y')";
	 }
	 	 
	if($_SESSION['page']=="contactlist")
	 {
	$aColumns = array( 'CategoryName','FirstName', 'LastName', 'EmailID','ContactID' );
	$JoinTickleID="cont.TickleID";
	$WhereCond=" AND cat.CategoryID = cont.CategoryID";
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "CategoryName";
	
	/* DB table to use */
	$sTable = "category as cat,contact_list as cont";
	 }
	 
	 	if($_SESSION['page']=="contactmanager")
	 {
	$aColumns = array( 'CategoryName','CategoryID' );
	
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "CategoryName";
	
	/* DB table to use */
	$sTable = "category";
	 }
	 
	 if($_SESSION['page']=="task")
	 {
	$aColumns = array( 'Subject','TickleTitle','CategoryName','TaskCretedDate','TaskInitiateDate','TaskID' );
	$JoinTickleID="um.TickleID";
	$WhereCond="and t.TickleID=um.TickleID and um.status='Y' and um.status=t.status and task.status='Y' and t.TickleTrainID=um.TickleTitleID and um.MailID=task.MailID and cat.CategoryID=t.TickleContact";
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "Subject";
	
	/* DB table to use */
	$sTable = "user_mail as um,tickle as t,task as task,category as cat";
	 }
	 
	if($_SESSION['page']=="tickle")
	 {
	$aColumns = array( 'EndAfter','TickleName','EmailPriority','DailyDays','CategoryName','CTickleName','TickleTrainID' );
	$JoinTickleID="t.TickleID";
	$WhereCond="and c.TickleID=t.TickleID and c.Status='Y' and t.TickleContact=c.CategoryID";
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "TickleName";
	
	/* DB table to use */
	$sTable = "tickle as t,category as c";
	 }
	 
	if($_SESSION['page']=="home")
	 {
	$toaddress=extract_emails_from($user_mail[0]['toaddress']);
	$aColumns = array( 'FirstName','LastName','CAST(umail.toaddress AS CHAR CHARACTER SET utf8)','Subject','CTickleName','TaskInitiateDate' );
	//$JoinTickleID="t.TickleID";
	//$WhereCond="and c.TickleID=t.TickleID and c.Status='Y' and t.TickleContact=c.CategoryID";
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "FirstName";
	
	/* DB table to use */
	$sTable = "contact_list as clist,user_mail as umail,tickle as tic,task as task";
	 }
	
	/* Database connection information 
	$gaSql['user']       = "root";
	$gaSql['password']   = "";
	$gaSql['db']         = "";
	$gaSql['server']     = "localhost";*/
	
	/* REMOVE THIS LINE (it just includes my SQL connection user/pass) */
	//include( $_SERVER['DOCUMENT_ROOT']."/datatables/mysql.php" );
	
	
	/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * If you just want to use the basic configuration for DataTables with PHP server-side, there is
	 * no need to edit below this line
	 */
	
	/* 
	 * MySQL connection
	 
	$gaSql['link'] =  mysqli_pconnect( $gaSql['server'], $gaSql['user'], $gaSql['password']  ) or
		die( 'Could not open connection to server' );
	
	mysqli_select_db( $gaSql['db'], $gaSql['link'] ) or 
		die( 'Could not select database '. $gaSql['db'] );
	
	*/
	/* 
	 * Paging
	 */
	$sLimit = "";
	if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
	{
		$sLimit = "LIMIT ".mysqli_real_escape_string($db->conn, $_GET['iDisplayStart'] ).", ".
			mysqli_real_escape_string($db->conn, $_GET['iDisplayLength'] );
	}
	
	
	/*
	 * Ordering
	 */
	if ( isset( $_GET['iSortCol_0'] ) )
	{
		$sOrder = "ORDER BY  ";
		for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
		{
			if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
			{
				$sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]."
				 	".mysqli_real_escape_string($db->conn, $_GET['sSortDir_'.$i] ) .", ";
			}
		}
		
		$sOrder = substr_replace( $sOrder, "", -2 );
		if ( $sOrder == "ORDER BY" )
		{
			if($_SESSION['page']=="message")
			{
			$sOrder = "ORDER BY um.MailID desc";
			}
			if($_SESSION['page']=="contactlist")
			{
			$sOrder = "ORDER BY cat.CategoryName desc";
			}
		}
	}
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
	 
	$sWhere = "";
	if ( $_GET['sSearch'] != "" )
	{
		$sWhere = "WHERE (";
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($db->conn, $_GET['sSearch'] )."%' OR ";
		}
		$sWhere = substr_replace( $sWhere, "", -3 );
		$sWhere .= ')';
	}
	
	/* Individual column filtering */
	for ( $i=0 ; $i<count($aColumns) ; $i++ )
	{
		if ( $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' )
		{
			if ( $sWhere == "" )
			{
				$sWhere = "WHERE ";
			}
			else
			{
				$sWhere .= " AND ";
			}
			$sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($db->conn,$_GET['sSearch_'.$i])."%' ";
		}
	}
	
	if ( $sWhere == "" && $_SESSION['page']=="message")
			{
				$sWhere = "WHERE um.TickleID='".$_SESSION['TickleID']."' and ParentID=0";
			}
	elseif ( $sWhere == "" && $_SESSION['page']=="contactlist")
			{
				$sWhere = "WHERE cat.TickleID='".$_SESSION['TickleID']."' and cont.TickleID='".$_SESSION['TickleID']."' and cat.CategoryID=cont.CategoryID";
			}	
	elseif ( $sWhere == "" && $_SESSION['page']=="contactmanager")
			{
				$sWhere = "WHERE ParentID='0' and TickleID='".$_SESSION['TickleID']."'";
			}	
	elseif ( $sWhere == "" && $_SESSION['page']=="task")
			{
				$sWhere = "WHERE um.TickleID='".$_SESSION['TickleID']."' and t.TickleID=um.TickleID and um.status='Y' and um.status=t.status and task.status='Y' and t.TickleTrainID=um.TickleTitleID and um.MailID=task.MailID and cat.CategoryID=t.TickleContact";
			}	
	elseif ( $sWhere == "" && $_SESSION['page']=="tickle")
			{
				$sWhere = "WHERE t.TickleID='".$_SESSION['TickleID']."' and c.TickleID=t.TickleID and c.Status='Y' and t.TickleContact=c.CategoryID";
			}	
	elseif ( $sWhere == "" && $_SESSION['page']=="home")
			{
				$sWhere = "Where task.TickleID='".$_SESSION['TickleID']."' and tic.TickleID='".$_SESSION['TickleID']."' and umail.TickleID=tic.TickleID and clist.Status='Y' and umail.Status='Y' and tic.Status='Y' and task.Status='Y' and clist.EmailID=umail.toaddress and clist.CategoryID=tic.TickleContact and umail.MailID=task.MailID and tic.TickleTrainID=umail.TickleTitleID";
			}	
			else
			{
				$sWhere .= " AND $JoinTickleID='".$_SESSION['TickleID']."' $WhereCond";
			}

	/*
	 * SQL queries
	 * Get data to display
	 */
	$sQuery = "
		SELECT SQL_CALC_FOUND_ROWS distinct ".str_replace(" , ", " ", implode(", ", $aColumns))."
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";

	$rResult = mysqli_query($db->conn, $sQuery ) or die(mysqli_error($db->conn));//, $gaSql['link']
	
	/* Data set length after filtering */
	$sQuery = "
		SELECT FOUND_ROWS()
	";
	$rResultFilterTotal = mysqli_query($db->conn, $sQuery ) or die(mysqli_error($db->conn));//, $gaSql['link']
	$aResultFilterTotal = mysqli_fetch_array($rResultFilterTotal);
	$iFilteredTotal = $aResultFilterTotal[0];
	
	/* Total data set length */
	$sQuery = "
		SELECT COUNT(".$sIndexColumn.")
		FROM   $sTable
	";
	$rResultTotal = mysqli_query($db->conn, $sQuery ) or die(mysqli_error($db->conn));//, $gaSql['link']
	$aResultTotal = mysqli_fetch_array($rResultTotal);
	$iTotal = $aResultTotal[0];
	
	
	/*
	 * Output
	 */
	$output = array(
		"sEcho" => intval($_GET['sEcho']),
		"iTotalRecords" => $iTotal,
		"iTotalDisplayRecords" => $iFilteredTotal,

		"aaData" => array()
	);
	
	while ( $aRow = mysqli_fetch_array( $rResult ) )
	{
		$row = array();
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
                        $aColumns[$i]=preg_replace('/^[^\.]+\./i',"",$aColumns[$i]);
			if (( $aColumns[$i] == "Subject" || $aColumns[$i] == "MailID" || $aColumns[$i] == "toaddress") && $_SESSION['page']=="message")
			{
				
				$toaddr=extract_emails_from($aRow['toaddress']);	
				if($aColumns[$i] == "toaddress")
				{								
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : "".$toaddr[0]."";	
				}
				if( $aColumns[$i] == "Subject")
				{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : "<a href='".Url_Create('maildetails','MID='.base64_encode($aRow['MailID']))."'>".$aRow[ $aColumns[$i] ]."</a>";
				}
				if( $aColumns[$i] == "MailID")
				{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' : '<a href="'.Url_Create('message','MID='.base64_encode($aRow['MailID']).'&action=Delete').'"  onclick="javascript:return confirm(\'Are You Sure want to delete?\');">Delete</a>';
				}
			}
			else if ( $aColumns[$i] == "ContactID" && $_SESSION['page']=="contactlist")
			{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' :'<a href="?u=addcontact&cid='.$aRow['ContactID'].'&action=Edit">Edit</a> || <a href="?u=contactlist&cid='.$aRow['ContactID'].'&action=Delete" onclick="javascript:return confirm(\'Are You Sure want to delete?\nEmail :'.$aRow['EmailID'].'\');">Delete</a>';
			}
			else if ( $aColumns[$i] == "CategoryID" && $_SESSION['page']=="contactmanager")
			{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' :'<a href="?u='.$_SESSION['page'].'&gid='.$aRow['CategoryID'].'&action=Edit">Rename</a> || <a href="?u='.$_SESSION['page'].'&gid='.$aRow['CategoryID'].'&action=Delete" onclick="javascript:return confirm(\'Are you sure want to delete? All contacts in this Group will be deleted\');">Delete</a>';
			}

			else if (($aColumns[$i] == "TaskInitiateDate" || $aColumns[$i] == "TaskCretedDate" || $aColumns[$i] == "TaskID") && $_SESSION['page']=="task")
			{
				$Created=convert_date($aRow['TaskInitiateDate']);
				$Wait=convert_date($aRow['TaskCretedDate']);
				if($aColumns[$i] == "TaskInitiateDate")
				{
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' :''.$Created.'';	
				}
				if($aColumns[$i] == "TaskCretedDate")
				{
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' :''.$Wait.'';	
				}
				if($aColumns[$i] == "TaskID")
				{
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' :'<a href="'.Url_Create($_SESSION['page'],'&tid='.$aRow['TaskID'].'&action=Delete').'" onclick="javascript:return confirm(\'Are You Sure want to delete?\nTask on : '.$Created.'\');">Delete</a>';
				}
			}
			else if ( ($aColumns[$i] == "EmailPriority" || $aColumns[$i] == "DailyDays" || $aColumns[$i] =="CTickleName" || $aColumns[$i] == "TickleTrainID") && $_SESSION['page']=="tickle")
			{
				if($aColumns[$i] == "EmailPriority")
				{
				$EmailPrioritys['1']="High";
				$EmailPrioritys['5']="Low";
				$EmailPrioritys['3']="Normal";
				/* Special output formatting for 'version' column */
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' :''.$EmailPrioritys[$aRow['EmailPriority']].'';
				}
				if($aColumns[$i] == "DailyDays")
				{
				$EndAfter=$aRow['EndAfter']-1;
				if($EndAfter<=0)
				$EndAfter=0;
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' :'<b>'.$aRow['DailyDays'].'</b> Days, Repeat <b>'.$EndAfter.'</b> times';	
				}
				if($aColumns[$i] == "CTickleName")
				{
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' :''.$aRow['CTickleName'].'+'.$_SESSION['UserName'].'@tickletrain.com';	
				}
				if($aColumns[$i] == "TickleTrainID")
				{
				$row[] = ($aRow[ $aColumns[$i] ]=="0") ? '-' :'<a href="'.Url_Create('addtickle','tid='.$aRow['TickleTrainID'].'&action=Edit').'">Edit</a> || <a href="'.Url_Create($_SESSION['page'],'tid='.$aRow['TickleTrainID'].'&action=Delete').'" onclick="javascript:return confirm(\'Are You Sure want to delete?\nThis will delete this tickle and any follow up tickles including tasks.\nTickle :'.$aRow['TickleName'].'\');">Delete</a> || <a href="javascript:void(0);" onclick="javascript:return duplicate(\''.$aRow['TickleTrainID'].','.addslashes($aRow['TickleName']).'\')">Duplicate</a> || <a href="javascript:void(0);" onclick="javascript:return PreviewEmail(\''.$aRow['TickleTrainID'].','.addslashes($aRow['TickleName']).'\');">Test</a>';	
				}
				
			}
			
			else if ( $aColumns[$i] != ' ' )
			{
				/* General output */
				$row[] = $aRow[ $aColumns[$i] ];
			}
		}
		$output['aaData'][] = $row;
	}
	
	echo json_encode( $output );
	exit;
?>