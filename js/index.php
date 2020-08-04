<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="/<?= ROOT_FOLDER ?>css/ticker_style2.css?v=2011-04-25" rel="stylesheet" type="text/css" />
        <link href="/<?= ROOT_FOLDER ?>css/ticker-style.css" rel="stylesheet" type="text/css" />
        
        <title><?= $content['Title'] ?></title>
        <style type="text/css">
            .loader {
                background: none;
                position: fixed;
                top: 50%;
                left: 40%;
                z-index: 1104;
            }

            /*.loader-overlay {
                position: absolute;
                background-color: #000; opacity: .70;filter:Alpha(Opacity=70);
                top: 0;
                left: 0;
                width: 100%;
                min-width:1014px;
                height: 100%;
                z-index: 1100;
            }*/
        </style>
		<?php
		 if($_GET['u'] == 'edittask'){ ?>
			 <link rel="icon" type="image/vnd.microsoft.icon" href="/<?= ROOT_FOLDER ?>favicon.ico">
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/all.css" type="text/css" media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/form.css" type="text/css" media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/jquery.fancybox-1.3.4.css" type="text/css" media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>plugins/css/smoothness/jquery-ui-1.8.21.custom.css" type="text/css"
                  media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>plugins/css/TableTools.css" type="text/css" media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>plugins/css/timePicker.css" type="text/css" media="all"/>
            <!--script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery-1.7.2.min.js"></script-->
			<script src="/<?= ROOT_FOLDER ?>js/jquery.min.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery.fancybox-1.3.4.pack.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/slideBlock.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/form.js"></script>
            <?/*script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/new.js"></script*/?>
            <?/*script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/click.js"></script*/?>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/inputs.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/popup.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery.cookie.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery-ui-1.8.21.custom.min.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery.validate.pack.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery.placeholder.min.js"></script>
            <script type="text/javascript" language="javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery.iframe.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery.dataTables.min.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery-ui-timepicker-addon.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/table2CSV.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/TableTools.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/cookie.min.js"></script>	
			
		 <?php } else { ?>
		
            <link rel="icon" type="image/vnd.microsoft.icon" href="/<?= ROOT_FOLDER ?>favicon.ico">
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/all.css" type="text/css" media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/form.css" type="text/css" media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/jquery.fancybox-1.3.4.css" type="text/css" media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>plugins/css/smoothness/jquery-ui-1.8.21.custom.css" type="text/css"
                  media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>plugins/css/TableTools.css" type="text/css" media="all"/>
            <link rel="stylesheet" href="/<?= ROOT_FOLDER ?>plugins/css/timePicker.css" type="text/css" media="all"/>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery-1.7.2.min.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery.fancybox-1.3.4.pack.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/slideBlock.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/form.js"></script>
            <?/*script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/new.js"></script*/?>
            <?/*script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/click.js"></script*/?>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/inputs.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/popup.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery.cookie.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery-ui-1.8.21.custom.min.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery.validate.pack.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery.placeholder.min.js"></script>
            <script type="text/javascript" language="javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery.iframe.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery.dataTables.min.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/jquery-ui-timepicker-addon.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/table2CSV.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>plugins/TableTools.js"></script>
            <script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/cookie.min.js"></script>			 
		 <?php } ?>
		 
            <script type="text/javascript">
                //<![CDATA[
                if ((navigator.userAgent.indexOf('iPad') != -1)) {
                    document.write('<meta name="viewport" content="width=device-width, initial-scale=1.0 " />');
                }
                //]]>
            </script>

            <!--[if lt IE 8]>
            <link rel="stylesheet" type="text/css" href="/<?= ROOT_FOLDER ?>css/ie.css" media="all"/><![endif]-->
            <script type="text/javascript">
                /*function mdialog(mtitle, msg, mbuttons, opts) {
                 $("#malert").html('<strong class="lb_title">'+mtitle+'</strong><div class="lb_holder">'+msg+'</div>');
                 $.fancybox({'href':'#malert','dialog':true,'overlayColor' : '#000','overlayOpacity' : '.7','titleShow' : false,'padding' : 0,'margin' : 5});
                 }*/

				$(document).ready(function(){
					$('.close_parent_w').click(function(){
						//alert('hererre');
						window.close();
					});
					
				});
                function wopen(link) {
                    window.open($(link).attr("href"), "attachWindow", "menubar=no,height=500,width=500,location=no");
                }

                function mdialog(mtitle, msg, mbuttons, opts) {
                    $("#malert").html(msg);
                    var defOpts = {
                        title: mtitle,
                        modal: true,
                        minWidth: 400,
                        width: 400,
                        height: 'auto',
                        buttons: ((mbuttons) ? mbuttons : []),
						open: function(event, ui) {
        $('.ui-dialog-titlebar-close').removeClass("ui-dialog-titlebar-close").html('<span onclick="closse_parent_w();" class="close_parent_w" style="float:right;">X</span>');
    }
                    };
                    if (opts) {
                        defOpts = $.extend(defOpts, opts);
                    }
                    $("#malert").dialog(defOpts);
                }
				
				function mdialogNew(mtitle, msg, mbuttons, opts) {
                    $("#malert").html(msg);
                    var defOpts = {
                        title: mtitle,
                        modal: true,
                        minWidth: 400,
                        width: 400,
                        height: 'auto',
                        buttons: ((mbuttons) ? mbuttons : []),
                    };
                    if (opts) {
                        defOpts = $.extend(defOpts, opts);
                    }
                    $("#malert").dialog(defOpts);
                }
				
				
				 function mdialog1(mtitle, msg, mbuttons, opts) {
                    $("#malert").html(msg);
                    var defOpts = {
                        title: mtitle,
                        modal: true,
                        minWidth: 400,
                        width: 400,
                        height: 'auto',
                        buttons: ((mbuttons) ? mbuttons : []),
						open: function(event, ui) {
        $('.ui-dialog-titlebar-close').removeClass("ui-dialog-titlebar-close").html('<span onclick="closse_parent_w();" class="close_parent_w" style="float:right;">X</span>');
    }
                    };
                    if (opts) {
                        defOpts = $.extend(defOpts, opts);
                    }
                    $("#malert").dialog(defOpts);
                }
                function closse_parent_w(){ 
					window.top.close();
					//return false;
				}
                function mconfirm(msg, href) {
                    var cancel = {text: 'Cancel', click: function() {
                            $(this).dialog('close')
                        }};
                    var ok = {text: 'Ok', click: function() {
                            $(this).dialog('close');
                            window.location.href = href;
                        }};
                    mdialog("Confirmation", msg, [ok, cancel]);
                }

                function mralert(mtitle, msg, href) {
                    var ok = {text: 'Ok', click: function() {
                            $(this).dialog('close');
                            window.location.href = href;
                        }};
                    mdialog(mtitle, msg, [ok]);
                }

                function malert(msg, opts) {
                    var ok = {text: 'Ok', click: function() {
                            $(this).dialog('close')
                        }};
                    mdialog("Alert", msg, [ok], opts);
                }
				
				

                function mcalert(title, msg, opts) {
                    var ok = {text: 'Ok', click: function() {
                            $(this).dialog('close')
                        }};
                    mdialog(title, msg, [ok], opts);
                }

                function tick() {
                    $('#ticker li:first').animate({'opacity': 0}, 500, function() {
                        $(this).appendTo($('#ticker')).css('opacity', 1);
                    });
                }

                $(document).ready(function() {

                    //alert(hjsfdhs);

                    setInterval(function() {
                        tick()
                    }, 10000);
                    var tickersession1 = $.cookie("hideticker");
                    if (tickersession1 == $('#announce').val()) {
                       $('#tickerdiv').hide();
                    }
                    else
                    {
                        $('#tickerdiv').show();
                    }
                    $("#closeticker").click(function() {
                        $.cookie("hideticker", $('#announce').val(), {path: '/', expires: 60});
                        $('#tickerdiv').hide();
                    });

                    var href = window.location.href.replace('https://<?= $_SERVER['SERVER_NAME'] ?>', '');

                    $('.nav_holder a[href="' + href + '"]').parents("li").addClass("active");

                    $.validator.methods.charnumberonly = function(value, element) {
                        return value.match(/^[A-Za-z0-9_\u00A1-\uFFFF]*$/ig);
                    };

                    $.validator.methods.notspecialwords = function(value, element) {
                        var val = value.toLowerCase();
                        return val != 'pause' && val != 'unpause' && val != 'resubscribe' && val != 'unsubscribe';
                    };

                    $.validator.methods.charspaceonly = function(value, element) {
                        return value.match(/^[A-Za-z\s\u00A1-\uFFFF]*$/ig);
                    };

                    $.validator.methods.require_from_group = function(value, element, options) {
                        var numberRequired = options[0];
                        var selector = options[1];
                        var validOrNot = $(selector, element.form).filter(
                                function() {
                                    return $(this).val();
                                }).length >= numberRequired;
                        return validOrNot;
                    }

                    $.validator.setDefaults({
                        errorElement: "span",
                        errorPlacement: function(error, element) {
                            var id = element.attr("id");
                            var elm = '.errortext[for="err_' + id + '"]';
                            if ($(elm).length != 0) {
                                error.appendTo($(elm)[0]);
                                return;
                            }
                            error.appendTo(element.parent());
                        }
                    });
                    $("#maintbl tbody tr").hover(function() {
                        if ($(this).hasClass("maintr")) {
                            $(this).next().addClass("trhover");
                        }
                        if ($(this).hasClass("childtr")) {
                            $(this).prev().addClass("trhover");
                        }
                        $(this).addClass("trhover");
                    }, function() {
                        if ($(this).hasClass("maintr")) {
                            $(this).next().removeClass("trhover");
                        }
                        if ($(this).hasClass("childtr")) {
                            $(this).prev().removeClass("trhover");
                        }
                        $(this).removeClass("trhover");
                    });

                    if ($('#announce').val() == '0') {
                        $("#tickerdiv").hide();
                    }
					
                });
                $(window).load(function() {
                    $("#loader").hide();
                    $("#main").show();
                });
            </script>
			
			<?php if($_GET['u'] == 'edittask') { ?>
		 
			
				
					
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/moment.min.js"></script>                       
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker.min.css" />
		
		
			
		<?php } ?>
		
    </head>
    <body>


	
    <!--div class="loader-overlay" id="loader"><div class="loader"><img src="/<?= GetRootFolder() ?>images/loading.gif"/></div></div-->
        <div class="loader" id="loader"><img src="/<?= GetRootFolder() ?>images/loading.gif"/></div>
        <div id="header">
            <div class="header_holder">
                <strong class="logo"><a href="/<?= ROOT_FOLDER ?>">tickletrain. send it. and forget it. <img
                            src="/<?= ROOT_FOLDER ?>images/logo.png" width="180" height="89" alt=""/></a></strong>
                <?if ($_SESSION['TickleID'] != "") {
                $getservertz = date_default_timezone_get();
                date_default_timezone_set($_SESSION['TimeZone']);
                //$date_display = date("dS M Y h:i A");
                $date_display = date('dS M Y, \<\s\p\a\n \c\l\a\s\s\=\"\m\a\r\k\"\>h:i A\<\/\s\p\a\n\>');//, time() + 3600);
                date_default_timezone_set($getservertz);


                $whmcsurl = "https://secure.tickletrain.com/dologin.php";
                $autoauthkey = "abcXYZ123";
                $timestamp = time(); # Get current timestamp
                $email = $_SESSION['EmailID']; # Clients Email Address to Login
                $hash = sha1($email.$timestamp.$autoauthkey); # Generate Hash


                ?>
                <?php
                $newsarray = whmcs_getannouncements();
                // print_r($newsarray);
                $count = false;
                if ($newsarray->totalresults != 0) {
                    foreach ($newsarray->announcements->announcement as $ann) {
                        $announcement[$ann->id] = $ann->announcement;

                        if ($ann->published == 1) {
                            $count = true;
                        }
                    }
                }
                if ($count == true) {
                    ?>
                    <div id="tickerdiv" style="display:none"> 
                        <ul id="ticker">
                            <?php
                            if ($newsarray->totalresults != 0) {
                                foreach ($newsarray->announcements->announcement as $ann1) {
                                    if ($ann1->published == 1) {
                                        ?> 
                                        <!--        <li style="list-style: none outside none;">
                                                <a href="#" style =""><?php echo $value1; ?></a>
                                                </li>-->
                                        <li style="list-style: none outside none;">
                                            <?php echo nl2br($ann1->announcement); ?>
                                        </li>
                                        <?php
                                    }
                                }
                            }
                            ?>

                        </ul>
                        <span id="closeticker" style="margin: -65px 0px 0px; float: right; position: relative; background: url(../images/crosse.png) no-repeat;"><img src="/<?= ROOT_FOLDER ?>images/crosse.png"></img></span>
                    </div>  
                <?php } ?>

                <ul class="t_menu">
                    <li class="popup-holder" id="pp1">
                        <a href="#" class="open">Account</a>
                        <ul class="pop_up">
                            <?php if (isset($_SESSION['upw']) && $_SESSION['upw'] != "") { ?>
                                <li><a href="https://secure.tickletrain.com/clientarea.php?action=products">Plan Details</a>
                                </li>
                                <li><a href="https://secure.tickletrain.com/clientarea.php?action=invoices">My Invoices</a></li>
                                <li><a href="https://secure.tickletrain.com/clientarea.php?action=details">Billing Profile</a></li>
                            <?php } else { ?>
                                <li><a href = "<?php echo $whmcsurl . '?email=' . $email . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode('clientarea.php?action=products'); ?>">Plan Details</a>
                                </li>

                                <li><a href = "<?php echo $whmcsurl . '?email=' . $email . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode('clientarea.php?action=invoices'); ?>">My Invoices</a></li>
                                <li><a href = "<?php echo $whmcsurl . '?email=' . $email . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode('clientarea.php?action=details'); ?>">Billing Profile</a></li>  
                            <?php } ?>
                        </ul>
                    </li>
                    <li class="popup-holder" id="pp2">
                        <a href="#" class="open">Support</a>
                        <ul class="pop_up">
                            <?php if (isset($_SESSION['upw']) && $_SESSION['upw'] != "") { ?>
                                <li><a href="https://secure.tickletrain.com/supporttickets">Open/View Tickets</a></li>
                                <li><a href="https://secure.tickletrain.com/knowledgebase">Knowledgebase</a></li>
                                <li><a href="https://secure.tickletrain.com/knowledgebase.php?action=displaycat&catid=1" target="_blank">FAQ's</a></li>
                                <li><a href="https://secure.tickletrain.com/knowledgebase.php?action=displaycat&catid=3" target="_blank">Help Videos</a></li>
                            <?php } else { ?>

                                <li><a href="<?php echo $whmcsurl . '?email=' . $email . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode('supporttickets.php'); ?>">Open/View Tickets</a></li>
                                <li><a href="<?php echo $whmcsurl . '?email=' . $email . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode('knowledgebase.php'); ?>"> Knowledgebase </a></li>
                                <li><a href="<?php echo $whmcsurl . '?email=' . $email . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode('knowledgebase.php?action=displaycat&catid=1'); ?>" target="_blank">FAQ's</a></li>
                                <li><a href="<?php echo $whmcsurl . '?email=' . $email . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode('knowledgebase.php?action=displaycat&catid=3'); ?>" target="_blank">Help Videos</a></li>
                            <?php } ?> 

                        </ul>
                    </li>
                    <li><a href="/<?= ROOT_FOLDER ?>logout/">Sign out</a></li>
                    <?php if (isset($_SESSION['whmcsuserid'])) { ?>
                        <li><a href="https://secure.tickletrain.com/admin/clientssummary.php?userid=<?php echo $_SESSION['whmcsuserid']; ?>">Return to Admin Area</a></li>
                    <?php } ?>
                </ul>
                <div class="text_area">
                    <p>Welcome, <span class="mark"><?= $_SESSION['FirstName'] ?></span></p>

                    <p><?= $date_display ?></p>
                </div>
                <? }?>
            </div>
        </div>
        <div id="nav">
            <div class="nav_holder">
                <ul style="width:100%;">
                    <?if ($_SESSION['TickleID'] != "") { ?>
                    <li><a href="/<?= ROOT_FOLDER ?>dashboard/">Dashboard</a></li>
                    <li><a href="/<?= ROOT_FOLDER ?>tickle/">Tickles</a></li>
                    <li><a href="/<?= ROOT_FOLDER ?>contactlist/">Contacts</a></li>
                    <li><a href="/<?= ROOT_FOLDER ?>myaccount/">Settings</a></li>
						<?php if (isset($_SESSION['upw']) && $_SESSION['upw'] != "") { ?>
					<li style="float:right;"><a href="http://secure.tickletrain.com/knowledgebase.php?action=displayarticle&id=34">Training & Tutorials</a></li>
					 <?php } else{ ?><li style="float:right;"><a href="<?php echo $whmcsurl . '?email=' . $email . '&timestamp=' . $timestamp . '&hash=' . $hash . '&goto=' . urlencode('knowledgebase.php?action=displayarticle&id=34'); ?>">Training & Tutorials</a></li>
					
					 <?php  } ?>
					
					
                    <? } else { ?>
                    <li><a href="https://tickletrain.com/">Home</a></li>
                    <li><a href="https://tickletrain.com/register">Register</a></li>
                    <li><a href="/<?= ROOT_FOLDER ?>login/">Login</a></li>
                    <? }?>
                </ul>
                <!--ul class="links">
                    <li><a href="https://tickletrain.com/videos" target="_blank"><img src="/<?= ROOT_FOLDER ?>images/ico_video.png" width="28" height="28" alt=""/></a></li>
                    <li><a href="https://tickletrain.com/faqs" target="_blank"><img src="/<?= ROOT_FOLDER ?>images/ico_question.png" width="28" height="28" alt=""/></a></li>
                </ul-->
            </div>
        </div>
        <div id="main" style="display: none" ondragover="dragOverHandler(event);"  ondragleave="dragLeave(event)">

        <?php 
        // if(isset($_GET['test'])) {


        // }
        ?>

        <?php 

            if(isset($_GET['action']) && ($_GET['action'] == 'mail_open')){
                if(isset($_GET['qrt'])){
                  $act = unprotect(rawurldecode($_GET['qrt']));
                  $act =  explode('-', $act);

                  if(isset($act[1])){
                  		$task_id =  $act[1];
                  		$time = time();
                  		$date =  date("Y-m-d H:i",strtotime('-5 minutes',$time));
                        $user_quey = "SELECT * from task_track_records  where type='reply_receved' AND task_id=$task_id";
                        $res =  mysqli_query($db->conn,$user_quey);
                          
                        if(mysqli_num_rows($res) > 0 ){
                            $user = mysqli_fetch_assoc($res);
                            $request = ($user['request_time']+(60*2)); 
                        }else{
                            $request = $_SERVER['REQUEST_TIME'];
                        }

       
                        $uQuery = mysqli_query($db->conn,"SELECT id,task_id, created_at, request_count from task_track_records  where task_id='$task_id' and  type='mail_open'");
                        $rowU = mysqli_fetch_assoc($uQuery);

                        if(!$rowU){
                            $user_q = "SELECT MailID from task  where TaskID=$task_id";
                            $mail  =  mysqli_query($db->conn,$user_q);
                                  
                            if(mysqli_num_rows($mail) > 0 ){

                                $us = mysqli_fetch_assoc($mail);

                                $db->insert('task_track_records',array(
                                                                    'mail_id'=>$us['MailID'],
                                                                    'task_id'=>$task_id,
                                                                    'agent'=>$_SERVER['HTTP_USER_AGENT'],
                                                                    'ip' => getUserIpAddr(),
                                                                    'request_time'=>$request,
                                                                )
                                            );
                            }
                        }else{

         //                        	if( strtotime($rowU['created_at']) < strtotime($date) ){
	        //                         	$request_count = $rowU['request_count']+1;
	        //                             $update_task = "update task_track_records set request_time='".$_SERVER['REQUEST_TIME']."' , request_count='".$request_count."' where id='" . $rowU['id'] . "' and  type='mail_open'";
									// 	mysqli_query($db->conn,$update_task);
									// }
                                	

                                }

                            
                        
		          		
                  }
                  //file_put_contents(__DIR__.'/open_mail.json',json_encode($data),JSON_PRETTY_PRINT);
                }
            }
        ?>

            <?= $content['Content'] ?>
        </div>
        <input type="hidden" id="announce" name="annctotal" value="<?php echo $newsarray->totalresults; ?>"/>
        <div id="malert" style="display: none;"></div>
        <!--div style="display: none">
        <div id="malert" class="lightbox"></div>
        </div-->


        <script language="javascript">
            function sort(addfld) {
                if (typeof (reloadFollowUps) == 'function') {
                    reloadFollowUps('sort', addfld);
                    return;
                }
                var qr = window.location.href;
                qr = qr.replace(/[&\?]{1}sort=[^&$]+/i, "");
                if (addfld) {
                    if (qr.match(/[\?]+/i)) {
                        qr += "&";
                    } else {
                        qr += "?";
                    }
                    qr += "sort=" + addfld;
                }
                window.location.href = qr;
                return;
            }

            function hsort(elm) {
                var sfld = $.trim($(elm).attr("rel"));
                if (sfld == "") {
                    return false;
                }
                if ($(elm).hasClass("sort_up")) {
                    sort(sfld + "-2");
                    return;
                }
                sort(sfld + "-1");
            }

            $(document).ready(function() {
                /*$("img.up").click(function () {
                 var sfld = $.trim($(this).attr("rel"));
                 if (sfld == "") {
                 return false;
                 }
                 sort(sfld + "-1");
                 });
                 
                 $("img.down").click(function () {
                 var sfld = $.trim($(this).attr("rel"));
                 if (sfld == "") {
                 return false;
                 }
                 sort(sfld + "-2");
                 });*/

                $("th.hsort").click(function() {
                    hsort($(this));
                });


                $("#bulkActsApply").submit(function() {
                    if ($.trim($("#bulkact").val()) == '') {
                        alert("No action was selected");
                        return false;
                    }
                    if ($(".listId:checked").length == 0) {
                        alert("No campaigns is selected");
                        return false;
                    }
                    if (confirm("Execute bulk action?")) {
                        $("#bulkActs").submit();
                    }
                    return false;
                });
                $("#dateFilter").placeholder();
                $("#dateFilter").datepicker({dateFormat: 'mm-dd-yy', beforeShowDay: function(date) {
                        var str = $.datepicker.formatDate('yy-mm-dd', date);
                        if (datesArray && !datesArray[str]) {
                            return [false];
                        }
                        return [true];
                    }});
                $('.excerpt_all').click(function() {
                    if ($(this).hasClass('active')) {
                        $(this).removeClass("active");
                        $(".excerpt_block").hide();
                        $.cookie('excerpt_all', 0, {expires: 30, path: '/'});
                    } else {
                        $(this).addClass("active");
                        $(".excerpt_block").show();
                        $.cookie('excerpt_all', 1, {expires: 30, path: '/'});
                    }
                    return false;
                });
                $('.expand_all').click(function() {
                    if ($(this).hasClass('active')) {
                        $(this).removeClass("active");
                        $('.ico_expand').removeClass("active");
                        $('tr.light').hide();
                        $.cookie('expand_all', 0, {expires: 30, path: '/'});
                    } else {
                        $(this).addClass("active");
                        $('.ico_expand').addClass("active");
                        $('tr.light').show();
                        $.cookie('expand_all', 1, {expires: 30, path: '/'});
                    }
                    return false;
                });
                $('.ico_expand').click(function() {
                    var idx = $(this).attr("id").replace('ex', '');
                    $(".tt" + idx).toggle();
                    if ($(this).hasClass('active')) {
                        $(this).removeClass("active");
                    } else {
                        $(this).addClass("active");
                    }
                    return false;
                });
                $("#selectAll").click(function(evt) {
                    evt.stopPropagation();
                    if ($(this).get(0).checked) {
                        $(".listId").attr("checked", "checked");
                    } else {
                        $(".listId").removeAttr("checked");
                    }
                });
                /*$("#selectAll").change(function (evt) {
                 });*/
                $("#bactionSelect").change(function() {
                    $("#bulkact").val($(this).val());
                });

                var exp = parseInt($.cookie('expand_all'));
                if (!isNaN(exp) && exp > 0) {
                    $('.expand_all').click();
                }
                var exc = parseInt($.cookie('excerpt_all'));
                if (!isNaN(exc) && exc > 0) {
                    $('.excerpt_all').click();
                }
            });
        </script>
		
    </body>
</html>