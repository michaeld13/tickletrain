<?php
$mid=base64_decode($_GET['MID']);
$rmid=base64_decode($_GET['RMID']);
if($mid>0)
{
$mail=$db->select_to_array('user_mail',''," Where MailID='".$mid."'");
$dmail=$mail[0];
$att=explode("\n",$dmail['attachments']);
}elseif($rmid>0)
{
$mail=$db->select_to_array('reply_mail',''," Where RMailID='".$rmid."'");
$dmail=$mail[0];
$att=explode("\n",$dmail['attachments']);

}

$MailHeader=ReadHeader($dmail['MailHeader']);

?>
<table bgcolor="#EFEFEF" cellpadding="5" cellspacing="1" width="100%">
<tr class="ehead"><td align="right" class="head">Subject : </td><td><?php echo $dmail['Subject'];?></td></tr>
<tr class="ehead"><td align="right" class="head">From :</td><td><?php echo htmlentities($dmail['fromaddress']);?></td></tr>
<tr class="ehead"><td align="right" class="head">Date :</td><td><?php echo $dmail['Date'];?></td></tr>
<tr class="ehead"><td align="right" class="head">To :</td><td><?php echo htmlentities(trim($dmail['XEnvelopeTo']));?></td></tr>
<?php
$test=0;

if($dmail['MessageRaw']!=""&&$test==1)
{
?>
<tr class="ehead"><td colspan="2" class="head">
Message :
</td></tr>
<tr bgcolor="#FFFFFF"><td colspan="2">
<pre>
<?php print_r(body_decode($dmail['MessageRaw']));?>
</pre>
</td></tr>
<?php
}else
{

$messagevalue=body_decode($dmail['MessageRaw'],$MailHeader['Content-Transfer-Encoding']);

$messagevalue['TEXT']=$dmail['Message'];
$messagevalue['HTML']=$dmail['MessageHtml'];
if(!is_array($messagevalue))
$messagevalue['TEXT']=strip_tags($dmail['MessageRaw']);



if($messagevalue['TEXT']!="")
{
?>
<tr class="ehead"><td colspan="2" class="head">Text Message :<br />
</td></tr>
<tr bgcolor="#FFFFFF"><td colspan="2">
<?php //echo nl2br(strip_tags($dmail['Message']));
if(trim($MailHeader['Content-Transfer-Encoding'])=="quoted-printable")
{
$messagevalue['TEXT']=quoted_printable_decode($messagevalue['TEXT']);
}
echo nl2br($messagevalue['TEXT']);




//echo "<br /><br />".$dmail['Message'];
?>
</td></tr>
<?php
}
if($messagevalue['HTML']!="")
{
?>
<tr class="ehead"><td colspan="2">Html Message :<br />
</td></tr>
<tr bgcolor="#FFFFFF"><td colspan="2">
<?php 
//echo $dmail['MessageHtml'];
if(trim($MailHeader['Content-Transfer-Encoding'])=="quoted-printable")
{
$messagevalue['HTML']=quoted_printable_decode($messagevalue['HTML']);
}
echo stripslashes($messagevalue['HTML']);
?>
</td></tr>

<?php
} //ifhtml

}//else raw
$attachment=$dmail['attachments'];
if($attachment!="")
{
?>
<tr class="ehead"><td colspan="2">
Attachments :<br />
</td></tr>
<tr bgcolor="#FFFFFF"><td colspan="2">
<?php 
$att=explode(",",$attachment);
foreach($att as $k=>$v)
{
//echo '<a href="'.Url_Create('download').'&f='.str_replace("attachment/","",$v).'" target="_blank">'.basename($v)."</a><br />";
echo basename($v)."<br />";
}
?>
</td></tr>
<?php }?>
</table>