<tr style="line-height: 10px;"<?=GetIf($ddd%2==1,' bgcolor="#f2f7fb"','')?>>
	


<td align="left" style="padding: 5px;border-right: 1px solid #ccc; line-height: 15px;max-width:400px;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;">
    <font face="Arial, Helvetica, sans-serif" size="2" color="#5d5d5d" style="font-size: 14px;white-space: nowrap;"><?=$subject?></font>
	<?php
	$query = "select * from comments where MailID='".$row['MailID']."'";
	  	$result =  mysqli_query($db->conn,$query);
			if(mysqli_num_rows($result) > 0){
				if($new_comments = hasNewCommentsNEWWW($row['MailID'])){
					$protect2 = protect($row['TickleID'] . "-" . $row['MailID']);
					$add_comment = "https://".SERVER_NAME.Url_Create("addcomments","cptsk=".rawurlencode($protect2)."&els=".rawurlencode(protect('yes')));

				?>
					<a href="<?=$add_comment;?>"><img  style="position:absolute;margin-left:5px;" class="comnt-btn pointer" width="20" height="20" src="/<?=ROOT_FOLDER?>images/icon-comment.png" /><span style="font-size:11px;margin:0 0 0 -5px;" id="coment-count"><?=$new_comments;?></span></a>
				<?php }
			}
	?>
</td>


<td align="left" style="padding: 5px;border-right: 1px solid #ccc; line-height: 15px;white-space: nowrap;text-overflow: ellipsis;">
   <div id="time-holder">
   <?php
   $timing_array = [ 
			"now"  =>["N","Now"],
            "two"  => ["1H","One Hour"],
            "three"=> ["2H","Two Hours"],
            "four" => ["3H","Three Hours"],
            "five" => ["1D","One Day"],
            "six"  => ["2D","Two Days"],
            "saven"=> ["3D","Three Days"],
            "eight"=> ["1W","One Week"],
            "nine"=> ["2W","Two Weeks"],
            "ten"  => ["1M","One Month"]
        ];
		$TLink = "https://" . SERVER_NAME . Url_Create("test", "tskup=" . rawurlencode($protect));
		 $TaskTime = "https://" . SERVER_NAME . Url_Create("edittask", "act=" . rawurlencode($protect) . "&MailID=" . $row['MailID']);
		 $protect = protect($row['TickleID'] . "-" . $row['TaskID']);
		foreach ($timing_array as $class => $value) {
                        if($add_class_to  == $value[0]){
                            $class = 'activeSpan';
                        } ?>
                       <span id="<?=$class?>" title="<?=$value[1]?>" ><a href="<?=$TLink?>&val=<?=rawurlencode(protect($value[0]))?>&w=<?=rawurlencode(protect($row['NoWeekend']))?>"><?=$value[0]?></a></span>
                  <?php  }
					
		?>
		<span style="margin-left:3px;"><a  href="<?=$TaskTime?>"><img src="/<?=ROOT_FOLDER?>images/edit_icon.png" border="0" width="14" height="14" alt="" /></a></span>
		
<?php
 $protect2 = protect($row['TickleID'] . "-" . $row['MailID']);
 $add_comment = "https://".SERVER_NAME.Url_Create("addcomments","cptsk=".rawurlencode($protect2)."&els=".rawurlencode(protect('yes')));
?>
    <span style="margin-left:-1px;"><a style="margin-left:5px;" href="<?=$add_comment;?>">Comments</a></span>
	
	<?php if(!empty($row['attachments'])) { 
	 $protect = protect($row['TickleID'] . "-" . $row['TaskID']);
	$attachmail = "https://" . SERVER_NAME . Url_Create("attachmail", "act=" . rawurlencode($protect) . "&MailID=" . $row['MailID'] . "&Mails=Mail");
       // $attachmail = '<a href="' . $attachmail . '" style="white-space:nowrap">' . htmlspecialchars($row['Subject']) . '</a>';
        
	?>
		<span style="margin-left:8px;"><a href="<?= $attachmail ?>" style="white-space:normal;" id="show_ticket_attch" style="margin-left:8px;" href=""><img src="/<?=ROOT_FOLDER?>images/attachment.png"></a></span>
	<?php } ?>
</div>
</td>

<td align="left" style="padding: 5px;border-right: 1px solid #ccc; line-height: 15px;">
    <font face="Arial, Helvetica, sans-serif" size="2" color="#5d5d5d" style="font-size: 14px;white-space: normal;"><?=date('m-d-Y',strtotime($row['TaskCretedDate']));?></font>
</td>




<td align="center">
    <a href="<?=$TickleArr[$TickleID][$ddd]['DeleteLink']?>"><img src="/<?=ROOT_FOLDER?>images/ico_basket_mail.png" border="0" width="12" height="14" alt="" /></a>
    <?if (isset($TickleArr[$TickleID][$ddd]['ApproveLink'])){?>&nbsp;&nbsp;<a href="<?=$TickleArr[$TickleID][$ddd]['ApproveLink']?>" target="_blank"><img src="/<?=ROOT_FOLDER?>images/ico_play_mail.png" border="0" width="14" height="14" alt="" /></a><?}?>
    <?if (isset($TickleArr[$TickleID][$ddd]['PauseLink'])){?>&nbsp;&nbsp;<a href="<?=$TickleArr[$TickleID][$ddd]['PauseLink']?>" target="_blank"><img src="/<?=ROOT_FOLDER?>images/ico_pause_mail.png" border="0" width="14" height="14" alt="" /></a><?}?>
    <?if (isset($TickleArr[$TickleID][$ddd]['UnPauseLink'])){?>&nbsp;&nbsp;<a href="<?=$TickleArr[$TickleID][$ddd]['UnPauseLink']?>" target="_blank"><img src="/<?=ROOT_FOLDER?>images/ico_play_mail.png" border="0" width="14" height="14" alt="" /></a><?}?>
</td>

</tr>
