<?php
//gettting action
$action=@trim($_REQUEST['action']);
$redirect=@trim($_REQUEST['redirect']);

//getting all request params
$FirstName=@trim($_REQUEST['FirstName']);
$LastName=@trim($_REQUEST['LastName']);
$EmailID=@trim($_REQUEST['EmailID']);
$ContactID=@intval($_REQUEST['ContactID']);
$CContactID=@trim($_REQUEST['CContactID']);
$CategoryName=@strtolower(trim($_REQUEST['CategoryName']));
$CategoryID=@intval($_REQUEST['CategoryID']);
$SCategoryID = @trim($_REQUEST['CategoryID']);
$ParentID=@intval($_REQUEST['ParentID']);
$ContactIDArr = $_REQUEST['ContactIDArr'];
//print_r($ContactIDArr);exit;
if($ParentID<=0) $ParentID=0;

//getting session vars
$TickleID = @intval($_SESSION['TickleID']);

$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
{
        $sLimit = "LIMIT ".mysqli_real_escape_string($db->conn, $_GET['iDisplayStart'] ).", ".
                mysqli_real_escape_string($db->conn, $_GET['iDisplayLength'] );
}


switch($action){
    case "EditContactForm":
        include_once "editcontactform.php";
        exit;
    case "EditGroupForm":
        include_once "editgroupform.php";
        exit;
    case "EditContact":

            // if ($EmailID==""){
            //     redirect("contactmanager","msg=".urlencode("Please fill required fields.")."&action=EditContactForm&ContactID=".$ContactID);
            // }

            $db->update('contact_list',array('FirstName'=>$FirstName,'LastName'=>"$LastName"),array("WHERE ContactID= ?",$ContactID));

            $page = GetVal($redirect,'contactlist');
                $surl = '?';
                foreach(json_decode(base64_decode($_POST['redirectUrl'])) as $key => $redirectUrl01)
                {
                    if($key!='u'){ $surl .= $key.'='.$redirectUrl01.'&';} 
                } 

                if($_POST['redirectUrl']!=''){
                    header("location: https://client.tickletrain.com/".$page."/".substr($surl,0,-1)."#".$_POST['hashtag']);
                }else{
                   redirect($page);
                }

            
      //       $eml = mysqli_query($db->conn,"select * from `contact_list` where TickleID='".$TickleID."' and BINARY `EmailID` = '".$EmailID."' and  ContactID<>'".$ContactID."'");
          
      //     $row = mysqli_fetch_assoc($eml);   
         
      //     if(mysqli_num_rows($eml) > 0 && $row['ContactID']>0){
		    //   redirect("contactmanager","msg=".urlencode("Email already exists.")."&ContactID=".$ContactID);
		    // }
            
            //$eml = $db->select_row("contact_list",''," where TickleID=".$TickleID." and EmailID='".$EmailID."' and ContactID<>".$ContactID);
            //if(is_array($eml) && $eml['ContactID']>0){
			//	if(strtolower($EmailID) != strtolower($eml['EmailID'])){
             //   redirect("contactmanager","msg=".urlencode("Email already exists.")."&ContactID=".$ContactID);
             //   }
             //  }
             
             
           /* if ($ContactID<=0){
                //$db->update("contact_list", array('FirstName'=>$FirstName,'LastName'=>"$LastName"), array("WHERE  EmailID= ?",$EmailID));
               	if (!is_array($eml)){
                    $ids = $db->insert('contact_list',array('FirstName'=>$FirstName,'LastName'=>"$LastName",'EmailID'=>"$EmailID",'CategoryID'=>$CategoryID,'TickleID'=>$TickleID,'Status'=>'Y', 'FbUid'=>'', 'FbFname'=>'','FbLname'=>''));
                }else{
                    if ($CategoryID>0){                    
                        $ids = $eml['ContactID'];
                        $db->delete('category_contact_list',array('where CategoryID=? and ContactID=?',$CategoryID,$ids));
                    }
                }
                if ($CategoryID>0){
                    $db->insert('category_contact_list',array('CategoryID'=>$CategoryID, 'ContactID'=>$ids));
                }
                redirect('contactlist');
            }else{*/

                /*$contact=$db->select_row('contact_list',''," Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and ContactID='$ContactID'");
                $res = mysqli_query($db->conn,"select distinct MailID from user_mail inner join tickle on (TickleTitleID=TickleTrainID) where TickleContact=".$contact['CategoryID']);
                $mails = array();
                while($row=  mysqli_fetch_array($res)){
                    $mails[]=$row['MailID'];
                }
                if (count($mails)){
                    mysqli_query($db->conn,"update user_mail set toaddress = replace(toaddress,'".$contact['EmailID']."','".$EmailID."') where MailID in (".join(",",$mails).")");
                }
                $db->update("contact_list", array('FirstName'=>$FirstName,'LastName'=>"$LastName"), array("WHERE  EmailID= ?",$EmailID));*/

              /*  $db->update('contact_list',array('FirstName'=>$FirstName,'LastName'=>"$LastName",'EmailID'=>"$EmailID"),array("WHERE ContactID= ?",$ContactID));//'TickleID'='".$_SESSION['TickleID']."' and
                if ($CategoryID>0){
                    $db->delete('category_contact_list',array('where CategoryID=? and ContactID=?',$CategoryID,$ContactID));
                    $db->insert('category_contact_list',array('CategoryID'=>$CategoryID, 'ContactID'=>$ContactID));
                }
                $page = GetVal($redirect,'contactlist');
				$surl = '?';
				foreach(json_decode(base64_decode($_POST['redirectUrl'])) as $key => $redirectUrl01)
				{
					if($key!='u'){ $surl .= $key.'='.$redirectUrl01.'&';} 
				}			
				if($_POST['redirectUrl']!='')
				{
					header("location: https://client.tickletrain.com/".$page."/".substr($surl,0,-1)."#".$_POST['hashtag']);
				}
				else
				{					
                   redirect($page);
				} 
            }*/
        break;
        case "DeleteCategory":
            if ($CategoryID>0){
                $db->delete('category',array("WHERE CategoryID =".$CategoryID));
                $db->delete('category_contact_list',array("WHERE CategoryID =".$CategoryID));                
            }
            redirect("contactmanager");
        break;
        case "DeleteContact":
            if ($ContactID>0){
                if ($CategoryID==0){
                    $db->delete('contact_list',array("WHERE ContactID =".$ContactID));
                    $db->delete('category_contact_list',array("WHERE ContactID=$ContactID"));
                }else{
                    $db->delete('category_contact_list',array("WHERE ContactID=$ContactID and CategoryID=$CategoryID"));
                }
            }
            redirect("contactmanager");
        break;
        case "DeleteMultipleContacts":
            if (is_array($ContactIDArr)){
                //$db->delete('contact_list',array("WHERE ContactID in(".implode(",",$ContactIDArr).")"));
                $db->delete('category_contact_list',array("WHERE concat(ContactID,'_',CategoryID) in('".implode("','",$ContactIDArr)."')"));
            }
            redirect("contactlist");
        break;
        case "ExportContacts":
//            $sql = "select ifnull(CategoryName,'') as CategoryName,FirstName,LastName,EmailID from contact_list as cont left outer join category_contact_list ccat on (cont.ContactID=ccat.ContactID) left outer join category as cat on (ccat.CategoryID=cat.CategoryID)";
//            $sWhere = "WHERE cont.TickleID='$TickleID'";
//            if ($SCategoryID!=""){
//                $sWhere.=" and ifnull(cat.CategoryID,0)=$CategoryID";
//            }
//            /*if ($CategoryID>0){
//                $sWhere.=" and ccat.CategoryID=$CategoryID";
//            }*/
//            $search = @mysqli_real_escape_string($db->conn,trim($_REQUEST['search']));
//            if ($search!=""){
//                $sWhere.=" and (CategoryName like '%$search%' or FirstName like '%$search%' or LastName like '%$search%' or EmailID like '%$search%')";
//            }
//            if ($sWhere!=""){
//                $sql.=" ".$sWhere;
//            }
//            $qr = mysqli_query($db->conn,$sql);
//            $arr = array();
//            while ($row=  mysqli_fetch_array($qr)){
//                $arr[]=$row;
//            }
//            mysqli_free_result($qr);
            
            $fileds = "select cont.ContactID, FirstName, LastName, EmailID, concat(cont.ContactID,'_',ifnull(ccat.CategoryID,0)) as CContactID";
            $mselect = " from contact_list as cont inner join category_contact_list ccat on cont.ContactID=ccat.ContactID WHERE cont.TickleID='$TickleID'";
            $q = $_REQUEST['search'];
            if ($q!=''){
                $mselect.=" and (FirstName like '%$q%' or LastName like '%$q%' or EmailID like '%$q%')";
            }
            if ($CategoryID!=""){
                $mselect.=" and ifnull(ccat.CategoryID,0)=".@intval($CategoryID);
            }
            $mselect.=" group by cont.ContactID ";
            //echo $fileds.$mselect;  die();
            $list = $db->query_to_array($fileds.$mselect);
            $arr = array();
            foreach($list as $lKey=>$row){
                $catData = $db->query_to_array("select ccat.CategoryID,tickle.TickleTrainID,tickle.TickleName from category_contact_list ccat inner join tickle on ccat.CategoryID=tickle.TickleContact where ccat.ContactID='".$row['ContactID']."'");
                foreach($catData as $catRow){
                    $arr[] = array('CategoryName'=>$catRow['TickleName'],'FirstName'=>$row['FirstName'],'LastName'=>$row['LastName'],'EmailID'=>$row['EmailID']);
                }    
            }
            
            $exp = "Tickle,FirstName,LastName,EmailID\n";
            for($i=0;$i<count($arr);$i++){
               $row = $arr[$i];
               $exp.= $row['CategoryName'].",";
               $exp.= $row['FirstName'].",";
               $exp.= $row['LastName'].",";
               $exp.= $row['EmailID']."\n";
            }
            
            header("Content-type: text/csv");
            header("Content-Disposition: filename=\"contactlist.csv\"");
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: ".strlen($exp));
            header("Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0"); 
            header("Expires: 0"); 
            header("Pragma: public"); 
            echo $exp;
            flush();
            exit();
        break;
        case "CheckCategory":
                $CategoryName = @strtolower(trim($_REQUEST['CategoryName']));
                $CategoryID = @intval($_REQUEST['CategoryID']);
                if ($CategoryName==""){
                    if ($CategoryID>0){
						exit("0");	
					}
					exit("true");
                }
                //echo " Where TickleID='".$_SESSION['TickleID']."' and Status='Y' and CTickleName='$CTickleName'";exit;
                $tcheck=$db->select_to_array('category',''," Where TickleID='".$_SESSION['TickleID']."' and CategoryName='$CategoryName' and CategoryID<>'$CategoryID'");
                if(@intval($tcheck[0]['CategoryID'])!=0)
                {
                        if ($CategoryID>0){
							echo intval($tcheck[0]['CategoryID']);exit;
						}
						exit("false");
                }
				if ($CategoryID>0){
					exit("0");	
				}
				exit("true");
		break;
        case "CheckContactEmail":
            //$EmailID = @strtolower(trim($_REQUEST['EmailID']));
		    $EmailID = trim($_REQUEST['EmailID']);
            $ContactID = @intval($_REQUEST['ContactID']);
            if ($EmailID==""){
                if ($ContactID>0){
                    exit("0");
                }
                exit("true");
            }
	    //$eml = $db->select_row("contact_list",''," where TickleID=".$TickleID." and EmailID='".$EmailID."' and ContactID<>".$ContactID);
          
        $eml = mysqli_query($db->conn,"select * from `contact_list` where TickleID='".$TickleID."' and BINARY `EmailID` = 
        '".$EmailID."' and  ContactID<>'".$ContactID."'") or die(mysqli_error($db->conn). __LINE__);
          
          $row = mysqli_fetch_assoc($eml);
          if(mysqli_num_rows($eml) > 0 && $row['ContactID']>0){
		   exit("false");
		 }
		 
		 exit("true");
        
        break;
        
        case "EditCategory":
			$MCategoryID = intval($_REQUEST['MergeCategoryID']);
            if($CategoryID<=0 && $CategoryName!="" && $MCategoryID<=0)
            {
                $ids = $db->insert('category',array('CategoryName'=>$CategoryName,'ParentID'=>"$ParentID",'TickleID'=>$_SESSION['TickleID'],'Status'=>'Y'));
            }
            if ($CategoryID>0 && $CategoryName!="" && checkGroupDelete($CategoryID)){
				if ($MCategoryID<=0){
	                $db->update('category',array('CategoryName'=>$CategoryName),array("WHERE  CategoryID= ?",$CategoryID));//'TickleID'='".$_SESSION['TickleID']."' and
				}else{
					$db->update_ignore('category_contact_list',array('CategoryID'=>$MCategoryID),array("WHERE CategoryID=?",$CategoryID));
					$db->delete('category',array("WHERE  CategoryID= ?",$CategoryID));
				}
            }
            redirect("contactmanager");
        break;
        case "ListCategory":
            $aColumns = array( 'CategoryName','CategoryID' );
            /* Indexed column (used for fast and accurate table cardinality) */
            $sIndexColumn = "CategoryName";
            /* DB table to use */
            $sTable = "category";
            $sWhere = "WHERE ParentID='0' and TickleID='$TickleID'";
        break;
        case "ContactList":
            $aColumns = array("concat(cont.ContactID,'_',ifnull(cat.CategoryID,0)) as ContactID", 'CategoryName','FirstName', 'LastName', 'EmailID',"concat(cont.ContactID,'_',ifnull(cat.CategoryID,0)) as CContactID" );
            //$JoinTickleID="cont.TickleID";
            //$WhereCond=" AND cat.CategoryID = cont.CategoryID";
            /* Indexed column (used for fast and accurate table cardinality) */
            $sIndexColumn = "concat(cont.ContactID,'_',ifnull(cat.CategoryID,0))";
            /* DB table to use */
            $sTable = "contact_list as cont left outer join category_contact_list ccat on (cont.ContactID=ccat.ContactID) left outer join category as cat on (ccat.CategoryID=cat.CategoryID)";
            $sWhere = "WHERE cont.TickleID='$TickleID'";
            if ($CategoryID>0){
                $sWhere.=" and ccat.CategoryID=$CategoryID";
            }
        break;
        case "SearchContact":
            $aColumns = array('CategoryName','FirstName', 'LastName', 'EmailID',"concat(cont.ContactID,'_',ifnull(cat.CategoryID,0)) as CContactID" );
            //$JoinTickleID="cont.TickleID";
            //$WhereCond=" AND cat.CategoryID = cont.CategoryID";
            /* Indexed column (used for fast and accurate table cardinality) */
            $sIndexColumn = "concat(cont.ContactID,'_',ifnull(cat.CategoryID,0))";
            /* DB table to use */
            $sTable = "contact_list as cont left outer join category_contact_list ccat on (cont.ContactID=ccat.ContactID) left outer join category as cat on (ccat.CategoryID=cat.CategoryID)";
            $sWhere = "WHERE cont.TickleID='$TickleID'";
            if ($SCategoryID!=""){
                $sWhere.=" and ifnull(cat.CategoryID,0)=$CategoryID";
            }
        break;
        default:return;
}

if ( intval( $_GET['iSortCol_0'] ) )
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
}

if ( $_GET['sSearch'] != "" )
{
        $sWhere.= " and (";
        for ( $i=0 ; $i<count($aColumns) ; $i++ )
        {
            if (preg_match("/\s+as\s+/i",$aColumns[$i])){
                continue;
            }
            $sWhere .= $aColumns[$i]." LIKE '%".mysqli_real_escape_string($db->conn, $_GET['sSearch'] )."%' OR ";
        }
        $sWhere = substr_replace( $sWhere, "", -3 );
        $sWhere .= ')';
}

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
/*
 * SQL queries
 * Get data to display
 */
$sQuery = "
        SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
        FROM   $sTable
        $sWhere
        $sOrder
        $sLimit
";
//echo $sQuery;
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
//echo $sQuery;
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


while ( $aRow = mysqli_fetch_assoc( $rResult ) )
{
        $row = array();
        $rColumns = array_keys($aRow);
        for ( $i=0 ; $i<count($rColumns) ; $i++ )
        {
                if ( $rColumns[$i] == "CategoryID")
                {
                        /* Special output formatting for 'version' column */
						$row[] = ($aRow[ $aColumns[$i] ]=="0" || !checkGroupDelete($aRow['CategoryID'])) ? '<a href="#" onclick="malert(\'The Tickle associated with this Group must be deleted before editing.\');return false">Rename</a> || <a href="#" onclick="malert(\'The Tickle associated with this Group must be deleted before deleting.\');">Delete</a>' :'<a href="#" onclick="renameCategory('.$aRow['CategoryID'].',\''.$aRow['CategoryName'].'\');return false">Rename</a> || <a href="'.Url_Create("contactmanager", "action=DeleteCategory&CategoryID=".$aRow['CategoryID']).'" onclick="javascript:return confirm(\'Are you sure want to delete? All contacts in this Group will be deleted\');">Delete</a>';
                }
		else if ( $rColumns[$i] == "ContactID")
                {
                        if ($i==0){
                            $arr = explode("_", $aRow[$rColumns[$i]]);
                            $row[] = ($aRow[ $rColumns[$i] ]=="0" || !checkContactDelete($arr[0], $arr[1])) ? '-' :'<input type="checkbox" name="ContactIDArr[]" class="selectedCheckbox" value="'.$aRow['CContactID'].'"/>';
                        }else{
                            /* Special output formatting for 'version' column */
                            $arr = explode("_", $aRow[$rColumns[$i]]);
                            $dellink = ' || <a href="'.Url_Create("contactmanager", "action=DeleteContact&ContactID=".$arr[0]."&CategoryID=".$arr[1]).'" onclick="javascript:return confirm(\'Are You Sure want to delete?\nEmail: '.$aRow['EmailID'].'\nGroup: '.$aRow['CategoryName'].'\');">Delete</a>';
                            if (!checkContactDelete($arr[0],$arr[1])){
                                $dellink='';
                            }
                            $row[] = (count($arr)!=2) ? '-' :'<a href="'.Url_Create("contactmanager", "ContactID=".$arr[0]."&CategoryID=".$arr[1]).'">Edit</a>'.$dellink;
                        }
                }
                else if ( $rColumns[$i] == "CContactID")
                {
                        if ($i==0){
                            $row[] = ($aRow[ $rColumns[$i] ]=="0") ? '-' :'<input type="checkbox" name="ContactIDArr[]" class="selectedCheckbox" value="'.$aRow['CContactID'].'"/>';
                        }else{
                            /* Special output formatting for 'version' column */
                            $arr = explode("_", $aRow[$rColumns[$i]]);
                            $dellink = ' || <a href="'.Url_Create("contactmanager", "action=DeleteContact&ContactID=".$arr[0]."&CategoryID=".$arr[1]).'" onclick="javascript:return confirm(\'Are You Sure want to delete?\nEmail: '.$aRow['EmailID'].'\nGroup: '.$aRow['CategoryName'].'\');">Delete</a>';
                            if (!checkContactDelete($arr[0],$arr[1])){
                                $dellink='';
                            }                            
                            $row[] = (count($arr)!=2) ? '-' :'<a href="'.Url_Create("contactmanager", "ContactID=".$arr[0]."&CategoryID=".$arr[1]).'">Edit</a>'.$dellink;
                        }
                }
                else// if ( $rColumns[$i] != ' ' )
                {
                        /* General output */
                        $row[] = $aRow[ $rColumns[$i] ];
                }
        }
        $output['aaData'][] = $row;
}

echo json_encode( $output );
exit;
?>
