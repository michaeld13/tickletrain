<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>tickletain | Email</title>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0">
	<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
		<tr>
			<td>
				<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
					<tr>
						<td valign="top" style="font-size:0;line-height:0;"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="15" alt="" /></td>
					</tr>
					<tr>
						<td valign="top" style="font-size:0;line-height:0;"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="20px" height="1" alt="" /></td>
						<td>
							<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#ffffff">
								<tr>
									<td style="line-height: 22px;">
                                                                               <?php if($Value['serviceid']!="" && $Value['serviceid']!="0"){ ?>
										<table cellspacing="0" cellpadding="0" width="100%"><tr><td style="font-family:Arial, Helvetica, sans-serif; font-size: 18px; width: 60%; font-weight: bold; color:#222222">Today's Tickles scheduled for <?=$reportDate?></td><td style="width: 40%"><a style="color:red; float: right;" href='<?=$Value['Tdirectupgradelink']?>'><img src="/<?=ROOT_FOLDER?>images/upgrade-plan.png" align="right"/></a></td></tr><tr><td colspan="2" style = "font-family:Arial, Helvetica, sans-serif; color:red;  font-size: 10px; text-align: right; width: 100%">We suggest you upgrade your plan. You are approching your limit</td></tr></table>
									<?php } else { ?>
                                                                                <font face="Arial, Helvetica, sans-serif" size="2" color="#222222" style="font-size: 18px; font-weight: bold;">Today's Tickles scheduled for </font>
                                                                        <?php } ?>
                                                                        </td>
								</tr>
								<tr>
									<td valign="top" style="font-size:0;line-height:0;"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="17" alt="" /></td>
								</tr>
								<tr>
									<td>
										<table cellspacing="0" cellpadding="0" width="100%">
											<tr>
												<td valign="top" colspan="3" style="font-size:0;line-height:0;" bgcolor="#d7d7d7"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="1" alt="" /></td>
											</tr>
											<tr>
												<td valign="top" width="1" style="font-size:0;line-height:0;" bgcolor="#d7d7d7"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="1" alt="" /></td>
												<td>
													<table cellspacing="0" cellpadding="0" width="100%">
														<tr bgcolor="#e7e7e7">
															<td valign="top" colspan="9" style="font-size:0;line-height:0;"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="8" alt="" /></td>
														</tr>
														<tr bgcolor="#e7e7e7" style="line-height: 17px;">
															<th align="left">
																<font face="Arial, Helvetica, sans-serif" size="2" color="#222222" style="font-size: 13px; font-weight: bold;">E-mail</font>
															</th>
															<th align="left">
																<font face="Arial, Helvetica, sans-serif" size="2" color="#222222" style="font-size: 13px; font-weight: bold;">Subject</font>
															</th>
															<th align="left">
																<font face="Arial, Helvetica, sans-serif" size="2" color="#222222" style="font-size: 13px; font-weight: bold;">Tickle</font>
															</th>
															<th align="left">
																<font face="Arial, Helvetica, sans-serif" size="2" color="#222222" style="font-size: 13px; font-weight: bold;">Time</font>
															</th>
															<th align="left">
																<font face="Arial, Helvetica, sans-serif" size="2" color="#222222" style="font-size: 13px; font-weight: bold;">Stage</font>
															</th>
															<th align="center">
																<font face="Arial, Helvetica, sans-serif" size="2" color="#222222" style="font-size: 13px; font-weight: bold;">Actions</font>
															</th>
														</tr>
														<tr bgcolor="#e7e7e7">
															<td valign="top" colspan="9" style="font-size:0;line-height:0;"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="6" alt="" /></td>
														</tr>
														<tr>
															<td valign="top" colspan="9" style="font-size:0;line-height:0;" bgcolor="#d7d7d7"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="1" alt="" /></td>
														</tr>
<?=$Value['HTML']?>
													</table>
												</td>
												<td valign="top" width="1" style="font-size:0;line-height:0;" bgcolor="#d7d7d7"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="1" alt="" /></td>
											</tr>
											
										</table>
									</td>
								</tr>
								<tr>
									<td valign="top" style="font-size:0;line-height:0;"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="15" alt="" /></td>
								</tr>
								<tr>
									<td>
										<font face="Arial, Helvetica, sans-serif" size="2" color="#5d5d5d" style="font-size: 14px;"><?php echo @$Value['view_more'] ;?> <a href="<?=$Value['TDashboardLink']?>" style="text-decoration: none;"><font color="#0088c2"> Click here</font></a> to login to TickleTrain</font>
									</td>
								</tr>
							</table>
						</td>
						<td valign="top" style="font-size:0;line-height:0;"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="20px" height="1" alt="" /></td>
					</tr>
					<tr>
						<td valign="top" style="font-size:0;line-height:0;"><img src="/<?=ROOT_FOLDER?>images/none.gif" width="1" height="15" alt="" /></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>