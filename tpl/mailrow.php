<tr style="line-height: 10px;"<?=GetIf($ddd%2==1,' bgcolor="#f2f7fb"','')?>>
	
<td align="left" nowrap style="padding: 5px;border-right: 1px solid #ccc; line-height: 15px;">
    <font face="Arial, Helvetica, sans-serif" size="2" color="#5d5d5d" style="font-size: 14px;">
    <?=str_replace("'","",$names['FirstName']) ?> <?=str_replace("'","",$names['LastName']) ?>
    <br>
    <?=$vx?>
    </font>
</td>

<td align="left" style="padding: 5px;border-right: 1px solid #ccc; line-height: 15px;max-width:400px;overflow:hidden;white-space: nowrap;text-overflow: ellipsis;">
    <font face="Arial, Helvetica, sans-serif" size="2" color="#5d5d5d" style="font-size: 14px;"><?=$subject?></font>
</td>

<td align="left" style="padding: 5px;border-right: 1px solid #ccc; line-height: 15px;">
    <font face="Arial, Helvetica, sans-serif" size="2" color="#5d5d5d" style="font-size: 14px;"><?=htmlspecialchars($row['TickleName'])?></font>
</td>

<td align="left" nowrap style="padding: 5px;border-right: 1px solid #ccc; line-height: 15px;">
    <font face="Arial, Helvetica, sans-serif" size="2" color="#5d5d5d" style="font-size: 14px;"><?=$TaskTime?></font>
</td>

<td align="left" style="padding: 5px;border-right: 1px solid #ccc; line-height: 15px;">
    <font face="Arial, Helvetica, sans-serif" size="2" color="#5d5d5d" style="font-size: 14px;"><?=$Stage?></font>
</td>

<td align="center">
    <a href="<?=$TickleArr[$TickleID][$ddd]['DeleteLink']?>"><img src="/<?=ROOT_FOLDER?>images/ico_basket_mail.png" border="0" width="12" height="14" alt="" /></a>
    <?if (isset($TickleArr[$TickleID][$ddd]['ApproveLink'])){?>&nbsp;&nbsp;<a href="<?=$TickleArr[$TickleID][$ddd]['ApproveLink']?>" target="_blank"><img src="/<?=ROOT_FOLDER?>images/ico_play_mail.png" border="0" width="14" height="14" alt="" /></a><?}?>
    <?if (isset($TickleArr[$TickleID][$ddd]['PauseLink'])){?>&nbsp;&nbsp;<a href="<?=$TickleArr[$TickleID][$ddd]['PauseLink']?>" target="_blank"><img src="/<?=ROOT_FOLDER?>images/ico_pause_mail.png" border="0" width="14" height="14" alt="" /></a><?}?>
    <?if (isset($TickleArr[$TickleID][$ddd]['UnPauseLink'])){?>&nbsp;&nbsp;<a href="<?=$TickleArr[$TickleID][$ddd]['UnPauseLink']?>" target="_blank"><img src="/<?=ROOT_FOLDER?>images/ico_play_mail.png" border="0" width="14" height="14" alt="" /></a><?}?>
</td>

</tr>
<!-- 
<tr<?=GetIf($ddd%2==1,' bgcolor="#f2f7fb"','')?>>
<td valign="top" colspan="9" style="font-size:0;line-height:0;"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="9" alt="" /></td>
</tr> -->
