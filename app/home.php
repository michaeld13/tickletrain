<!-- <?php
function siteURL() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "http://" : "http://";
    $domainName = $_SERVER['HTTP_HOST'] . '/';
    return $protocol . $domainName . 'dashboard/';
}
?>
<? $facebookaccount = $_SESSION["facebookaccount"]; ?>
<script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/cloud-zoom.1.0.2.min.js"></script>
<!-- <script src="/<?= ROOT_FOLDER ?>js/jquery.ticker.js" type="text/javascript"></script>
 <script src="/<?= ROOT_FOLDER ?>js/site.js" type="text/javascript"></script> -->
<link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/cloud-zoom.css" type="text/css" media="all"/>
<link href="/<?= ROOT_FOLDER ?>css/ticker_style2.css?v=2011-04-25" rel="stylesheet" type="text/css" />
<link href="/<?= ROOT_FOLDER ?>css/ticker-style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/reveal.css" type="text/css" />
<script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery.reveal.js"></script>
<script type="text/javascript" src="/<?= ROOT_FOLDER ?>js/jquery.reveal.js"></script>
<style>

    @font-face {
        font-family: 'af_pepsiregular';
        src: url('https://client.tickletrain.com/afpepsi_-webfont.eot');
        src: url('https://client.tickletrain.com/afpepsi_-webfont.eot?#iefix') format('embedded-opentype'),
            url('https://client.tickletrain.com/afpepsi_-webfont.woff') format('woff'),
            url('https://client.tickletrain.com/afpepsi_-webfont.ttf') format('truetype'),
            url('https://client.tickletrain.com/afpepsi_-webfont.svg#af_pepsiregular') format('svg');
        font-weight: normal;
        font-style: normal;

    }


    .slider {
        color: #5D5D5D;
        float: left;
        font: 12px/21px Arial, Helvetica, sans-serif;
        margin: 0 0 0 10px;
        overflow: hidden;
        position: relative;
        width:600px;
    }
    .slider h2 {
        color: #FF5300;
        float: left !important;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 18px;
        margin: 0;
        padding: 13px 0 0;
        width: 100%;
    }
    .slider > ul {
        /* styled by JS to match the added width and height of all <li>Ã¢â‚¬â„¢s */
        position: relative;
        -webkit-transition: .5s left;
        -moz-transition: .5s left;
        -ms-transition: .5s left;
        -o-transition: .5s left;
        width:3000px!important;
        list-style: none;
        margin: 0;
        padding: 0;
    }
    .slider > ul > li {
        float: left;
        width:600px;
    }
    .text-holder123 {
        background: url("https://client.tickletrain.com/images/STEP1_img.png") no-repeat scroll 0 0 transparent;
        border: 2px solid #007BB3;
        float: left;
        margin: 0px 0 0 5px;
        padding: 0;
        width: 500px;
    }
    .text-holder123 img {
        float: left;
    }
    .text-holder123 h6 {
        color: #197BBA;
        float: right;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 20px;
        font-weight: bold;
        margin: 0;
        padding: 25px 0 14px 18px;
        width: 257px;
    }
    .text-holder123 p {
        clear: both;
        color: #FF5200;
        float: right;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 18px;
        line-height: 28px;
        margin: 0;
        padding: 8px 0 0;
        width: 380px;
    }
    .bottom_text {
        float: right;
        margin:24px 16px 23px 0px;
        padding: 0;
        width: 383px;
    }
    .bottom_text p {
        color: #197BBA;
        float: left;
        font-family: Arial,Helvetica,sans-serif;
        font-weight: bold;
        margin: 0;
        padding: 4px 0 0;
        width: 255px;
    }
    .go_button {
        background: url("../images/btn_green_r.png") no-repeat scroll 100% 0 transparent;
        border:none;
        color: #FFFFFF;
        cursor: pointer;
        float: right;
        border-radius: 3px 3px 3px 3px;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 14px;
        text-shadow: 0 -1px 1px #154354;
        font-weight: bold;
        margin:8px 2px 0 0;
        padding:2px;
        text-align: center;
        width:82px;
    }
    .text-holder1 {
        float: left;
        width: 500px;
        margin: 0px 0 0 0;
        border: 2px solid #007bb3;
        background: url(https://client.tickletrain.com/images/STEP1_img.png) no-repeat;
        padding: 0px;
    }
    .text-holder1 h5 {
        color: #197BBA;
        float: left;
        font-family: Arial, Helvetica, sans-serif;
        font-size:22px;
        font-weight: normal;
        margin: 0;
        padding: 20px 0 0 98px;
    }
    .text-holder1 p {
        color: #FF5200;
        float: right;
        font-family: Arial, Helvetica, sans-serif;
        font-size:18px;
        line-height:28px;
        margin: 0;
        width: 380px;
        padding:16px 10px 0 0;
    }
    .bottom_text1 {
        float: right;
        margin: 15px 8px 19px 0;
        padding: 0;
        width: 383px;
    }
    .bottom_text1 p {
        color: #197BBA;
        float: left;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 18px;
        font-weight: normal;
        margin: 0;
        padding: 0;
        width: 374px;
    }
    .BACK_button {
        background: url("https://client.tickletrain.com/images/button_backimg.png") repeat-x scroll 0 0 transparent;
        border: 2px solid #007BB3;
        color: #FFFFFF;
        cursor: pointer;
        float: left;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 19px;
        font-weight: normal;
        margin:0 0 0 180px!important;
        padding: 10px;
        text-align: center;
        width: 92px;
    }
    .NEXT_button {
        background: url("https://client.tickletrain.com/images/button_backimg.png") repeat-x scroll 0 0 transparent;
        border: 2px solid #007BB3;
        color: #FFFFFF;
        cursor: pointer;
        float: right;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 19px;
        font-weight: normal;
        margin: 0 12px 0 0;
        padding: 10px;
        text-align: center;
        width: 92px;
    }
    .text-holder_b {
        float: left;
        width: 500px;
        margin: 0px 0 0 0;
        border: 2px solid #007bb3;
        background: url(https://client.tickletrain.com/images/STEP1_img.png) no-repeat;
        padding: 0px;
    }
    .text-holder_b h5 {
        color: #197BBA;
        float: left;
        font-family: Arial,Helvetica,sans-serif;
        font-size:22px;
        font-weight: normal;
        margin: 0;
        padding: 10px 0 0 97px;
    }
    .text-holder_b p {
        color: #FF5200;
        float: right;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 18px;
        line-height: 28px;
        margin-top: 15px;
        text-align: justify;
        padding: 0 22px 0 0;
        text-align: justify;
        width: 380px;
    }
    #fome_div {
        float: left;
        margin: 0 0 0 95px;
        padding: 0;
    }
    .Message_div {
        float: left;
        width: 395px;
        margin: 0px;
        background: #4189bb;
        padding: 0px;
    }
    .Message_div h5 {
        float: left;
        margin: 0px;
        padding:5px;
        font-family: Arial, Helvetica, sans-serif;
        color: #FFF;
        font-size: 17px;
    }
    .Message_div span {
        float: right;
        padding:5px 10px 0 0;
    }
    .fome_box {
        background: none repeat scroll 0 0 #E8EDF1;
        border-radius: 12px 12px 12px 12px;
        float: left;
        margin: 9px 0 9px 7px;
        padding: 0 0 9px;
        width: 381px;
    }
    .fome_div_top {
        float: left;
        margin: 10px 0 0 8px;
        width: 603px;
        padding: 0px;
    }
    .From {
        background: none repeat scroll 0 0 #F4F4FF;
        border: 1px solid #A6A4A7;
        border-radius: 2px 2px 2px 2px;
        color: #464646;
        cursor: pointer;
        float: left;
        font-family: Calibri;
        font-size: 15px;
        height: 28px;
        padding: 4px 0 0;
        text-align: center;
        width: 68px;
    }
    .From img {
        float: right;
        margin: 12px 11px 0 0;
    }
    .fome_div_top p {
        color: #464646;
        float: left;
        font-family: Calibri;
        font-size:14px;
        margin: 0 0 0 12px;
        padding: 0;
    }
    .input_b {
        background: none repeat scroll 0 0 #FFFFFF;
        border: 2px solid #9B9EA3;
        color: #141414;
        float: left;
        font-family: Calibri;
        font-size: 14px;
        height: 26px;
        margin: 2px 0 0 12px;
        padding: 0 13px;
        width:251px;
    }
    .input_c {
        border: 2px solid #9f9b9c;
        float: left;
        font-family: Calibri;
        font-size: 18px;
        height: 26px;
        margin: -32px 0 0 102px;
        background: #dad4d6;
        border-right: none;
        text-decoration: underline;
        color: #141414;
        padding: 0 13px;
        text-transform: uppercase;
        width: 468px;
    }
    .bottom_text_b {
        float: right;
        margin: 7px 0 14px;
        padding: 0;
        width: 492px;
    }

    .bottom_text_b p {
        color: #FF5200;
        float: left;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 18px;
        font-weight: normal;
        line-height: 28px;
        margin-bottom: 5px;
        margin: 0;
        padding: 0 0 11px 87px;
        text-align: left;
    }
    .BACK_button {
        background: url("../images/btn_blue_r.png") no-repeat scroll 100% 0 transparent;
        border: medium none;
        border-radius: 3px 3px 3px 3px;
        color: #FFFFFF;
        cursor: pointer;
        text-shadow: 0 -1px 1px #154354;
        float: left;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 14px;
        font-weight: normal;
        margin: 0 0 0 119px;
        padding: 2px;
        text-align: center;
        width: 82px;
    }
    .NEXT_button {
        background: url("../images/btn_blue_r.png") no-repeat scroll 100% 0 transparent;
        border: medium none;
        border-radius: 3px 3px 3px 3px;
        color: #FFFFFF;
        cursor: pointer;
        float: right;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 14px;
        font-weight: normal;
        margin: 0 12px 0 0;
        text-shadow: 0 -1px 1px #154354;
        padding: 2px;
        text-align: center;
        width: 82px;
    }
    .Finish_button {
        background: url("../images/btn_green_r.png") no-repeat scroll 100% 0 transparent;
        border: medium none;
        border-radius: 3px 3px 3px 3px;
        color: #FFFFFF;
        cursor: pointer;
        float: right;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 14px;
        font-weight: normal;
        text-shadow: 0 -1px 1px #154354;
        margin: 0 12px 0 0;
        padding: 2px;
        text-align: center;
        width: 82px;
        margin-bottom: 10px;
    }
    .text-holder_c {
        float: left;
        width:500px;
        margin: 0px 0 0 0;
        border: 2px solid #007bb3;
        background: url(https://client.tickletrain.com/images/STEP1_img.png) no-repeat;
        padding: 0px;
    }
    .text-holder_c h5 {
        color: #197BBA;
        float: left;
        font-family: Arial, Helvetica, sans-serif;
        font-size:18px;
        font-weight: normal;
        margin: 0;
        padding:15px 0 0 96px;
    }
    .text-holder_c p {
        color: #FF5200;
        float: right;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 18px;
        line-height: 28px;
        margin: 0;
        padding: 7px 14px 0 13px;
        text-align: left;
        width: 389px;
    }
    .bottom_text_c {
        float: right;
        margin: 14px 19px 15px 0;
        padding: 0;
        width: 383px;
    }
    .bottom_text_c p {
        color: #197BBA;
        float: left;
        font-family: Arial,Helvetica,sans-serif;
        font-size: 16px;
        font-weight: normal;
        margin: 0;
        padding: 0 0 0 0;
        width: 404px;
        margin-bottom: 10px;
    }
    .text-holder_e {
        float: left;
        width: 500px;
        margin: 0px 0 0 0;
        border: 2px solid #007bb3;
        background: url(https://client.tickletrain.com/images/STEP1_img.png) no-repeat;
        padding: 0px;
    }
    .text-holder_e h1 {
        color: #197BBA;
        float: left;
        font-family: Arial, Helvetica, sans-serif;
        font-size: 21px;
        font-weight: normal;
        margin: 0;
        padding: 20px 0 0 170px;
    }
    .text-holder_e p {
        color: #FF5200;
        float: right;
        font-family: Arial,Helvetica,sans-serif;
        font-size:18px;
        line-height:28px;
        margin: 0;
        padding: 9px 0 0;
        text-align: left;
        width: 397px;
    }
    .bottom_text_e {
        float: left;
        margin: 14px 0 31px 26px;
        padding: 0;
        width: 496px;
    }
    .bottom_text_e p {
        color: #000000;
        float: left;
        font-family: Arial,Helvetica,sans-serif;
        font-size:23px;
        font-weight: normal;
        margin: 0;
        padding: 0 0 0 80px;
        width: 308px;
    }

	.maintr select {
	    width: 100px !important;
	}

</style>

<!--  Start fade In --  fade out javascript  -->
<script>
    $(document).ready(function() {
        var delay = 6000, fade = 1000;
        var banners = $('.banner123');
        var len = banners.length;
        var i = 0;
        setTimeout(cycle, delay);

        function cycle() {
            $(banners[i % len]).fadeOut(fade, function() {
                $(banners[++i % len]).fadeIn(fade, function() {
                    setTimeout(cycle, delay);
                });
            });
        }
    });
</script>
<!--  End fade In --  fade out javascript  -->

<script>
    var datesArray = <?= json_encode($dates) ?>;
    function preview(TaskIDs, MailIDs, PreviewType, subj, susp) {
        var title = subj;
        if (PreviewType != 'Mail' && PreviewType != 'MailAttach') {
            title = "Preview of Tickle scheduled";
        }
        var url = "<?= Url_Create('previewmail') ?>?TaskID=" + TaskIDs + "&MailID=" + MailIDs + "&Mails=" + PreviewType;
        if (susp) {
            url += "&suspended=yes";
        }
        //alert(url);
        $("#uploadFrame").src(url, function() {
            mdialog(title, $(this).contents().find("body").html(), false, {'height': 500, 'width': 800});
        });
    }


    function preview1(TaskIDs, MailIDs, PreviewType, subj) {
        preview(TaskIDs, MailIDs, PreviewType, subj, true);
    }


    function ChangeTask(TaskIDs, MailIDs ,hashtag) {
        $.get('<?= Url_Create('edittask') ?>', {TaskID: TaskIDs, MailID: MailIDs, hashtag: hashtag ,page:'dashboard', qstr: '<?php echo json_encode($_GET);?>'}, function(data) {
            mdialog("Adjust send time", data,false,{'height':150, 'width':'300px'});
        });
        return false;
    }
    function EditContact(ContactID) {
        $.get('<?= Url_Create('contactmanager') ?>', {ContactID: ContactID, action: 'EditContactForm', redirect: 'home'}, function(data) {
            mdialog("Contact edit", data);
        });
        return false;
    }

    function FacebookFillContact(ContactID, fbUser) {
        $.get('<?= Url_Create('contactmanager') ?>', {ContactID: ContactID, action: 'EditContactForm', redirect: 'home', fbid: fbUser.id}
        , function(data) {
            mdialog("Contact edit", data);
        });
        return false;
    }

    function ExtendedEditContact(ContactID, email,qstrvalue , hashtag) {
        $.get('<?= Url_Create('contactmanager') ?>', {ContactID: ContactID, action: 'EditContactForm', redirect: 'home', email: email, qstr: qstrvalue , hashtag: hashtag}
        , function(data) {
            mdialog("Contact edit", data);
        });
        return false;
    }

    function DeleteConfirm(url, subval, qstrvalue) {
        var cancel = {text: 'Cancel', click: function() {
                $(this).dialog('close')
            }};
        var deleteone = {text: 'Delete', click: function() {
				if(qstrvalue!='')
				{
					window.location.href = url + "&DeleteAll=Y&redirectUrl=<?php echo base64_encode(json_encode($_GET));?>";
				}	
				else
				{
					window.location.href = url + "&DeleteAll=Y&redirectUrl="+qstrvalue;
				}
            }};
        var message = "";
        mdialog("Delete this Campaign?", message, [deleteone,cancel]);
        $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "325px" );
        $(".ui-dialog:first" ).css( "margin-left", "-125px" );
        return false;
    }


    function ApproveConfirm(url) {

        var cancel = {text: 'Cancel', click: function() {
                $(this).dialog('close')
            }};
        var approve = {text: 'Approve', click: function() {
                window.location.href = url;
            }};
        var approveAll = {text: 'Approve All', click: function() {
                window.location.href = url + "&ApproveAll=Y";
            }};
        var message = "<b>Approve</b> this Tickle to be sent?<br/><b>Approve All</b> will approve this and follow-up Tickles to be sent.";
        mdialog("Approve confirmation", message, [approve, approveAll, cancel]);
        return false;
    }
    function PauseConfirm(url,qstrvalue) {
       var cancel = {text: 'Cancel', click: function() {
                $(this).dialog('close')
            }};
        var pause = {text: 'Pause', click: function() {
				if(qstrvalue!='')
				{
					window.location.href = url + "&PauseAll=Y&redirectUrl="+qstrvalue;
				}
				else
				{
					window.location.href = url + "&PauseAll=Y&redirectUrl=<?php echo base64_encode(json_encode($_GET));?>";
				}
            }};
        //var pauseAll = {text:'Pause All', click: function() {window.location.href = url+"&PauseAll=Y";}};
        var message = "";
        mdialog("Pause this Tickle?", message, [pause, cancel]);
        $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "210px" );
        $(".ui-dialog:first" ).css( "margin-left", "-105px" );
        return false;
    }
    function UnPauseConfirm(url,qstrvalue) {
        var cancel = {text: 'Cancel', click: function() {
                $(this).dialog('close')
            }};
        var unpause = {text: 'Send', click: function() {
				if(qstrvalue!='')
				{
					window.location.href = url+"&redirectUrl="+qstrvalue;
				}else
				{    
					window.location.href = url+"&redirectUrl=<?php echo base64_encode(json_encode($_GET));?>"; 
				}     
            }};
        var message = "";
        mdialog("Send this Tickle?", message, [unpause, cancel]);
        $(".ui-dialog:first" ).css("float", "none");
        $(".ui-widget-header :first" ).css("font-size", "18px");
        $(".ui-dialog-titlebar").parent().css("left",'50%');
        $("#malert" ).css( "min-height", "" );
        $(".ui-dialog:first" ).css( "width", "210px" );
        $(".ui-dialog:first" ).css( "margin-left", "-105px" );
        return false;
    }





    function UnPgradeConfirm() {
        var url = "<?php echo $alertupgrademessage; ?>";
        var upgrade = {text: 'Upgrade Plan', click: function() {
                window.location.href = url;
            }};
        var donotshow = {text: 'Do not show this message again', click: function() {
                setcookie()
                $(this).dialog('close')

            }};

        var message = 'We suggest you upgrade your plan. You are approaching your limit.';
        mdialog("Upgrade Plan", message, [upgrade, donotshow]);
        return false;
    }

    function setcookie() {
        $.cookie("example", null);
        var tickleid = <?php echo $tickleid; ?>;
        //alert(tickleid);
        // $("#dontshow").live("click", function(){
        $.cookie("example", tickleid, {expires: 15});
        //  parent.$.fancybox.close();
        // });

    }
    function positionPopup(formselector) {
        if (!$(formselector).is(':visible')) {
            return;
        }

        $(formselector).css({
            left: ($(window).width() - $(formselector).width()) / 2,
            top: ($(window).width() - $(formselector).width()) / 7,
            position: 'absolute'
        });

    }
    function fancybox_close() {
        $('#fancy_outer').hide();
        $('#fancy_overlay').hide();
        $('#fancy_title').hide();
        $('#fancy_loading').hide();
        $('#fancy_ajax').remove();

    }

    function tick() {
        $('#ticker li:first').animate({'opacity': 0}, 500, function() {
            $(this).appendTo($('#ticker')).css('opacity', 1);
        });
    }

//Start Function to get time diffrence in javascript
    function TimeDiff(a, b)
    {

        var first = a.split(":")
        var second = b.split(":")

        var xx;
        var yy;

        if (parseInt(first[0]) < parseInt(second[0])) {

            if (parseInt(first[1]) < parseInt(second[1])) {

                yy = parseInt(first[1]) + 60 - parseInt(second[1]);
                xx = parseInt(first[0]) + 24 - 1 - parseInt(second[0])

            } else {
                yy = parseInt(first[1]) - parseInt(second[1]);
                xx = parseInt(first[0]) + 24 - parseInt(second[0])
            }



        } else if (parseInt(first[0]) == parseInt(second[0])) {

            if (parseInt(first[1]) < parseInt(second[1])) {

                yy = parseInt(first[1]) + 60 - parseInt(second[1]);
                xx = parseInt(first[0]) + 24 - 1 - parseInt(second[0])

            } else {
                yy = parseInt(first[1]) - parseInt(second[1]);
                xx = parseInt(first[0]) - parseInt(second[0])
            }

        } else {


            if (parseInt(first[1]) < parseInt(second[1])) {

                yy = parseInt(first[1]) + 60 - parseInt(second[1]);
                xx = parseInt(first[0]) - 1 - parseInt(second[0])

            } else {
                yy = parseInt(first[1]) - parseInt(second[1]);
                xx = parseInt(first[0]) - parseInt(second[0])
            }


        }



        if (xx < 10)
            xx = "0" + xx


        if (yy < 10)
            yy = "0" + yy

        return yy
    }

//End Function to get time diffrence in javascript

//Start timer function in javascript
    function timer(datehere) {
        var BigDay = new Date(datehere);
        var msPerDay = 24 * 60 * 60 * 1000;
        var tid = window.setInterval(function() {
            var today = new Date();
            var timeLeft = (BigDay.getTime() - today.getTime());

            var e_daysLeft = timeLeft / msPerDay;
            var daysLeft = Math.floor(e_daysLeft);

            var e_hrsLeft = (e_daysLeft - daysLeft) * 24;
            var hrsLeft = Math.floor(e_hrsLeft);

            var e_minsLeft = (e_hrsLeft - hrsLeft) * 60;
            var minsLeft = Math.floor(e_minsLeft);
            if (minsLeft < 10) {
                minsLeft = "0" + minsLeft;
            }
            var e_secsLeft = (e_minsLeft - minsLeft) * 60;
            var secsLeft = Math.floor(e_secsLeft);
            if (secsLeft < 10) {
                secsLeft = "0" + secsLeft;
            }
            if (minsLeft == 0 && secsLeft == 0) {
                jQuery.ajax({
                    url: "<?php echo siteURL(); ?>",
                    data: {'taskexist': 'taskexist'},
                    type: "POST",
                    success: function(response) {
                        if (response == "0" || response == "") {
                            $("#errorslide").show();
                        } else {
                            $("#successslide").show();
                        }
                    }
                });
                $("#nextbutton").show();
                clearInterval(tid);
                $('#nextbutton').trigger('click');
            }
            var timeString = minsLeft + ":" + secsLeft;
            //alert(timeString);
            if (timeString != '0:00') {
                $('#countdown').html(timeString);
            }
        }, 1000);
    }



    $(document).ready(function() {

<?php if (count($tasks) == 0 && !$search && !isset($suspendedorder) && !isset($campaign_exist)) { ?>
            $(".big-link").trigger('click');
<?php } ?>

        $("#lastcron").click(function() {

            jQuery.ajax({
                url: "<?php echo siteURL(); ?>",
                data: {'lastcron': 'lastcron'},
                type: "POST",
                dataType: "json",
                success: function(response) {
                    var lastcrontime = response.lastcrontime
                    var date1 = response.currentime;
                    var timediff = TimeDiff(date1, lastcrontime);
                    // alert(timediff);
                    var timeremaindiff = 3 - timediff
                    var myDate = new Date();
                    myDate.setMinutes(myDate.getMinutes() + timeremaindiff);
                    timer(myDate);
                    //  disablebutton(timeremaindiff)
                }

            });
        });

        setInterval("location.reload(true)", 600000);
        setInterval(function() {
            tick()
        }, 10000);

        var tickleid = <?php echo $tickleid; ?>;
        var ccok = $.cookie("example");
        //alert(ccok);
        if (ccok == tickleid) {
        }
        else {		
<?php  if (isset($alertupgrademessage)) { ?>
                //UnPgradeConfirm();
<?php } ?>

        }
        $("a.show_attach").fancybox({
            'transitionIn': 'elastic',
            'transitionOut': 'elastic',
            'speedIn': 600,
            'speedOut': 200,
            'overlayShow': true,
            'overlayColor': '#000',
            'onComplete': function(arg, cur) {
                $('#fancybox-img').wrap(
                        $('<a>')
                        .attr('href', $(arg[cur]).attr('href'))
                        .addClass('cloud-zoom')
                        .attr('rel', "position: 'inside'")
                        );
                $('.cloud-zoom').CloudZoom();
            }

        });

        $(".choosecompaign").click(function() {
            $('#checkbox').show();

        });
    });

</script>
<iframe id="uploadFrame" name="uploadFrame" style="width:0px;height:0px" frameborder="0"></iframe>

<!---Start  Slide Show Coding -->

<!-- <div style="display:none"><a href="#" class="big-link" data-reveal-id="myModal">Fade and Pop</a></div>
<div id="myModal" class="reveal-modal" style="height:510px;">

<!--    <div class="button123" onclick="javascript:sliders[0].goToPrev()">[back]</div>
    <div class="button123" style="float:right;margin: 15px 27px 0 0;" onclick="javascript:sliders[0].goToNext()">[next]</div>
    <a class="close-reveal-modal">&#215;</a> </div>

<!---End Slide Show Coding -->

<?php
//echo $paynowlink;
//echo '<pre>';
//print_r($subject);
//echo count($subject);
//echo '</pre>';
?>
<!-- Starting of Welocme Page with slider -->
<?php if (count($tasks) == 0 && !$search && !isset($suspendedorder) && !isset($campaign_exist)) { ?>


<div class="main_holder register_area" style="padding: 3px 4px 0 155px;">
<script type="text/JavaScript" src="https://secure.tickletrain.com/modules/livehelp/scripts/jquery-latest.js"></script>
</div>

<link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/welcome_screen.css">

<script src="/<?= ROOT_FOLDER ?>js/jquery.appear.js" type="text/javascript"></script>

<script type="text/javascript">
		$(document).ready(function(){
			$(".welcome_inner").appear(function(){
				$(".welcome_right").addClass("appeared");
			}, {accY: -400});
		});
	</script>

<!--welcome_screen start-->
<?php //print_r($_SERVER['REMOTE_ADDR']); if(isset($_SESSION['appendvideo'])) { ?>
<?php //if($_SERVER['REMOTE_ADDR']=='202.164.47.148') { ?>

<div style="margin: 0 auto;width:600px;">
<link rel="stylesheet" href="/css/colorbox.css" />
<script src="/js/jquery.colorbox.js" type="text/javascript"></script>
<script>
        $(document).ready(function(){ 
         $(".youtube").colorbox({iframe:true, innerWidth:800, innerHeight:410});
         $(".youtube").click(function(){
              $('#welcome_screen').show();
              $('.youtube').hide();
          });
        });
 </script>
<a class="youtube" href="https://www.youtube.com/embed/2zAaQGWlymI?rel=0&autoplay=1&amp;wmode=transparent;" style="display:block">
<img src="/images/playvideo.png">
</a>

</div>
<?php //} else  { ?>
<div id="welcome_screen" style="display:none">
	<div class="welcome_inner">
    	<h1>Welcome, <span class="bold"><?php echo $firstname; ?>!</span> We're glad you're here.</h1>
        <div class="divider">&nbsp;</div>

    	<div class="welcome_left">
            <p style="font-size:18px;">TickleTrain's ease of use is one of it’s best features.  Head over to the <a href="http://secure.tickletrain.com/knowledgebase.php?action=displayarticle&id=34" style="color:#FFFFFF; font-weight:bold; text-decoration:underline;">Training & Tutorials</a> tab and you’ll be using it in a matter on minutes!</p>
            <p>In addition, a "Getting Started" Tickle is preloaded in your account.  It emails a daily tip so you can get the most from TickleTrain.  To use it, just send an email to yourself and include:
<u>gettingstarted+<?php echo $userName; ?>@tickletrain.com</u> in the BCC field and press send.</p>
            <p class="small">*Be sure you are using TickleTrain with the email address you signed up with.  If you need further assistance, please use the Support link at the top of the page.  We are here to help!</p>
            <p class="thanks">Thank you for choosing TickleTrain!</p>
        </div>

        <div class="welcome_right">
            <div class="gmail"><img src="/<?= GetRootFolder() ?>images/gmail_mail_logo.png"></div>
            <div class="yahoo"><img src="/<?= GetRootFolder() ?>images/yahoo_mail_logo.png"></div>
            <div class="outlook"><img src="/<?= GetRootFolder() ?>images/outlook_mail_logo.png"></div>
            <div class="thunderbird"><img src="/<?= GetRootFolder() ?>images/thunderbird_mail_logo.png"></div>
            <div class="android"><img src="/<?= GetRootFolder() ?>images/android_mail_logo.png"></div>
            <div class="apple"><img src="/<?= GetRootFolder() ?>images/apple_mail_logo.png"></div>
            <div class="text"><img src="/<?= GetRootFolder() ?>images/send-it-text.png"></div>
            <div class="tickle"><img src="/<?= GetRootFolder() ?>images/tickle_mail_logo.png"></div>
        </div>

    </div>
</div>

<!--welcome_screen end-->


    <?php
 //}
    return;
}

/* End of Welocme Page with slider */
if (isset($_SESSION['allowedhere']) && isset($_SESSION['downgradecheck'])) {
    ?>
    <form method="POST" action="https://client.tickletrain.com/dashboard/">
        <div class="main_holder">
            <?php if (isset($morethenallowed)) { ?>
                <div class="heading">
                    <h1><?php echo $morethenallowed; ?></h1>
                </div>
            <?php } else { ?>
                <div class="heading">
                    <h1>Downgrade</h1>
                </div>
            <?php } ?>
            <div class="bar1" style="text-align:center">
              <!--  <p style="font-size: 13px; text-align: justify; line-height: 20px;">We're sorry...but we have'nt received your payment. To resume services please visit<a href = "<?php //echo $paynowlink;    ?>"> ACCOUNT&nbsp;/&nbsp;MY INVOICES</a> and view your outstanding invoice. We suggest you to signup for recurrent billing to avoid possible issue in future. You can cancel at any time.</p>
              <a href = "<?php //echo $paynowlink;    ?>"><img src="http://client.tickletrain.com/app/img/make-payment.png" alt="Pay Now" title="Pay Now"/></a> -->
                <p style="font-size: 13px; text-align: justify; line-height: 20px;">Your plan has been changed successfully. Your new plan offers <b><?php echo $_SESSION['allowedhere']; ?></b> campaigns but you have <b><?php echo $_SESSION['totalcampaign']; ?></b> currently active. Please select the <b><?php echo $_SESSION['allowedhere']; ?></b> campaigns you want to keep below and click the proceed button.</p>
                <p style="color: red; font-size: 13px; text-align: justify; line-height: 20px; margin: 0px auto;">Note: All other campaigns will be removed. If you decide you want to keep all current campaigns please <a href ="<?php echo $supportpage; ?>">upgrade your plan.</a></p>
                <br/>
            </div>
            <?php
            //echo '<pre>';
            //print_r($tasks);
            //print_r($maddress);
            //    echo '</pre>';
            //    die();
            ?>
            <input type="hidden" name="baction" value="" id="bulkact"/>
            <fieldset>
                <table cellpadding="0" cellspacing="0" id="maintbl">
                    <thead>
                        <tr>
                            <th style="width:135px" class="hsort<?= GetIf($sfld == 1 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 1 && $sord == 2, " sort_down", "") ?>"
                                rel="1"><input type="checkbox" id="selectAll"/>
                                First Name<span class="sort"><img
                                        src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6" height="4" alt="" rel="1"/><img
                                        src="/<?= GetRootFolder() ?>images/arrow_down.png"
                                        class="down" width="6"
                                        height="4" alt="" rel="1"/></span> </th>
                            <th class="hsort<?= GetIf($sfld == 2 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 2 && $sord == 2, " sort_down", "") ?>"
                                rel="2"<? /* class="sort_up" */ ?>>Last Name <span class="sort"><img
                                        src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                        height="4" alt="" rel="2"/><img
                                        src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="2"/></span> </th>
                            <th
                                class="hsort<?= GetIf($sfld == 3 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 3 && $sord == 2, " sort_down", "") ?>"
                                rel="3">E-mail <span class="sort"><img src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                                                   height="4" rel="3"
                                                                   alt=""/><img src="/<?= GetRootFolder() ?>images/arrow_down.png"
                                                                   class="down" width="6"
                                                                   height="4" alt="" rel="3"/></span></th>
                            <th
                                class="hsort<?= GetIf($sfld == 4 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 4 && $sord == 2, " sort_down", "") ?>"
                                rel="4"<? /* class="sort_down" */ ?>>Original Subject <span class="sort"><img
                                        src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                        height="4" alt="" rel="4"/><img
                                        src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="4"/></span> </th>
                            <th class="hsort<?= GetIf($sfld == 5 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 5 && $sord == 2, " sort_down", "") ?>"
                                rel="5">Tickle <span class="sort"><img src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up"
                                                                   width="6" height="4"
                                                                   alt="" rel="5"/><img
                                                                   src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6"
                                                                   height="4" alt="" rel="5"/></span></th>
                            <th class="hsort<?= GetIf($sfld == 6 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 6 && $sord == 2, " sort_down", "") ?>"
                                rel="6">Schedule <span class="sort"><img src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                                                     height="4"
                                                                     alt="" rel="6"/><img
                                                                     src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6"
                                                                     height="4" alt="" rel="6"/></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $i = 0;
                        $ix = 1;
						
						
                        foreach ($tasks as $MailID => $rows) {
                            $is_approve = false;
                            $is_pause = false;
                            $i = 0;
                           // ksort($rows);
                            foreach ($rows as $rs) {
                                $TickleContact = $rs['TickleContact'];
                                $TickleTrainID = $rs['TickleTrainID'];
                                $FollowTickleTrainID = $rs['FollowTickleTrainID'];
                                $TApprove = $rs['TTApprove'];
                                $IsApproved = $rs['Approve'];
                                $IsPaused = $rs['Pause'];
                                $actions = "";
                                if ($FollowTickleTrainID) {
                                    $TApprove = $rs['FollowTApprove'];
                                }
                                if ($TApprove == 'Y' && $IsApproved != 'Y' && !$is_approve || $IsPaused == 'Y') {
                                    //$actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return ApproveConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Approve=Y') . "');\" class=\"ico_play_pause\">Approve</a></li>";
                                    $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
                                    //$is_approve = true;
                                }
                                if (($TApprove == 'N' || $IsApproved == 'Y') && $IsPaused != 'Y' && !$is_pause) {
                                    $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return PauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Pause=Y') . "');\" class=\"ico_play_pause pause\">Pause</a></li>";
                                    //$is_pause = true;
                                }
                                /* if ($IsPaused == 'Y' && !$is_pause) {
                                  $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
                                  //$is_pause = true;
                                  } */

                                $cid = $rs['ContactID'];
                                $crow = $maddress[$cid];
                                $crow['FirstName'] = str_replace("'","",$crow['FirstName']);
                                $crow['LastName'] = str_replace("'","",$crow['LastName']);
                                $FirstName = $crow['FirstName'];
                                $LastName = $crow['LastName'];
                                $EmailAddr = $crow['EmailID'];



                                $attFiles = GetMailAttachments($rs['RawPath'], $rs['attachments']);
                                if (count($attFiles) == 0) {
                                    $rs['attachments'] = '';
                                }
                                $basepath = preg_replace("/\.txt$/i", "/", $rs['RawPath']);
                                $relpath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $basepath);
                                $TaskCretedDate = convert_date($rs['TaskCretedDate']);
                                //$TaskInitiateDate=convert_date($rs['TaskInitiateDate']);
                                $TaskInitiateDate = convert_date(getlocaltime($rs['TaskGMDate'], $rs['TimeZone']));
                                $time = strtotime($rs['TaskInitiateDate']);
                                $TickleTime = date("h:i A", $time);
                                $TickleDate = date("m-d-y", $time);
                                $message = strip_tags(trim($rs['MessageHtml']));
                                if ($message == "") {
                                    $message = trim($rs['Message']);
                                }
                                if (mb_strlen($message) > 200) {
                                    $message = mb_substr($message, 0, 200) . "...";
                                }
                                $img = "/" . GetRootFolder() . "images/1.jpg";
                                ?>
                                <tr class="maintr<?= (($i) ? ' light tt' . $rs['MailID'] : '') ?>"<?= (($i) ? ' style="display:none"' : '') ?>>
                                    <td><div class="txt"><nobr>
                                                <input type="checkbox" class="listId" name="mailid[]" value="<?= $rs['MailID'] ?>"
                                                       id="task<?= $rs['TaskID'] ?>"/>
                                                <label
                                                    for="task<?= $rs['TaskID'] ?>">
                                                        <?= GetVal($crow['FirstName'], '') ?>
                                                </label>
                                            </nobr> </div></td>
                                    <td><div class="txt"> <nobr>
                                                <?= GetVal($crow['LastName'], '') ?>
                                            </nobr> </div></td>
                                    <td><div class="txt">
                                            <?= GetVal($crow['EmailID'], '-') ?>
                                        </div></td>
                                    <td><div class="txt">
                                            <?= GetVal($rs['Subject'], '(no-subject)') ?>
                                        </div>
                                        <div class="excerpt_block block" style="display:none;">
                                            <p>
                                                <?= $message ?>
                                            </p>
                                            <!--div class="attached"><a href="#">invoice.pdf</a>, <a href="#">quote.pdf</a></div-->
                                        </div></td>
                                    <td><div class="txt">
                                            <?= GetVal($rs['TickleName'], '-') ?>
                                        </div></td>
                                    <td><div class="txt"><nobr>
                                                <?= $TickleDate ?>
                                                <?= $TickleTime ?>
                                            </nobr></div></td>
                                </tr>
                                <!--tr class="h_txt-holder"-->
                                <tr class="childtr<?= (($i) ? ' light tt' . $rs['MailID'] : '') ?>"<?= (($i) ? ' style="display:none"' : '') ?>>
                                    <td><!--
                                    <ul class="h_txt first">
                                        <li>
                                            <a href="#"
                                               onclick="return ExtendedEditContact('<?= $cid ?>','<?= $EmailAddr ?>')">Edit</a>
                                        </li>
                                    </ul> --></td>
                                    <td><!--
                                    <ul class="h_txt">
                                        <li>
                                            <a href="#"
                                               onclick="return ExtendedEditContact('<?= $cid ?>','<?= $EmailAddr ?>')">Edit</a>
                                        </li>
                                    </ul> --></td>
                                    <td><!--
                                    <ul class="h_txt">
                                        <li><a href="#"
                                               onclick="return ExtendedEditContact('<?= $cid ?>','<?= $EmailAddr ?>')">Edit</a></li>
                                        <li><a
                                            href="<?= Url_Create('compose', 'Email=' . $EmailAddr . '&TaskID=' . $rs['TaskID']) ?>">Send
                                            e-mail</a></li>
                                    </ul>
                                        --></td>
                                    <td><ul class="h_txt">
                                            <li> <nobr><a href="#"
                                                          onclick="preview1('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'Mail', '<?= htmlspecialchars($rs['Subject']) ?>');
                            return false;">Original email</a>
                                                          <? if (count($attFiles) > 1) { ?>
                                                    <a href="#"
                                                       onclick="preview1('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'MailAttach', '<?= htmlspecialchars($rs['Subject']) ?>');
                        return false;"><img src="/images/attachment.png" border="0"/></a>
                                                   <? } ?>
                                                   <?
                                                   if (count($attFiles) == 1) {
                                                       $imgArr = getimagesize($basepath . $attFiles[0]);
                                                       if ($imgArr) {
                                                           ?>
                                                        <a href="<?= $relpath . $attFiles[0] ?>" class="show_attach"><img src="/images/attachment.png" border="0"/></a>
                                                    <? } else { ?>
                                                        <a href="#"
                                                           onclick="preview1('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'MailAttach', '<?= htmlspecialchars($rs['Subject']) ?>');
                            return false;"><img src="/images/attachment.png" border="0"/></a>
                                                       <?
                                                       }
                                                   }
                                                   ?>
                                            </nobr></li>
                                        </ul></td>
                                    <td><ul class="h_txt">
                                            <li><a href="#"
                                                   onclick="preview1('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'Tickle');
                    return false;">Preview
                                                    tickle</a></li>
                                        </ul></td>
                                    <td></td>
                                </tr>
                                <?
                                $i++;
                            }
                            //foreach
                        }//foreach
                        if (count($tasks) == 0) {
                            ?>
                            <tr>
                                <td colspan="8" align="center">No Tickle campaigns scheduled</td>
                            </tr>
    <? } ?>
                    </tbody>
                </table>
    <? if ($ps > 1) { ?>
                    <div class="pagination">
                        <div class="holder">
                            <ul>
                                    <? for ($j = 1; $j <= $ps; $j++) { ?>
                                    <li<?= (($j == $pg) ? ' class="current"' : '') ?>>
                                        <?= (($j == $pg) ? '<span>' : '<a href="?pg=' . $j . '&q=' . trim($_REQUEST['q']) . '&qdate=' . trim($_REQUEST['qdate']) . '&sort=' . trim($_REQUEST['sort']) . '">') ?>
                                        <?= $j ?>
                                    <?= (($j == $pg) ? '</span>' : '</a>') ?>
                                    </li>
        <? } ?>
                            </ul>
                        </div>
                    </div>
    <? } ?>
            </fieldset>
        </div>
        <div style="text-align:center">
            <input type="submit" name="downgradeplanhere" value="" class="btn_downgrade1"/>
        </div>
    </form>
    <?php
    return;
} elseif (isset($suspendedorder)) {
    ?>
    <form method="POST" action="">
        <div class="main_holder">
    <?php if (isset($morethenfive)) { ?>
                <div class="heading">
                    <h1><?php echo $morethenfive; ?></h1>
                </div>
    <?php } else { ?>
                <div class="heading">
                    <h1>Order Suspended</h1>
                </div>
    <?php } ?>
            <div class="bar1" style="text-align:center">
                <p style="font-size: 13px; text-align: justify; line-height: 20px;">We're sorry...but we have not received your payment. To resume services please visit&nbsp;<a href = "<?php echo $paynowlink; ?>"> ACCOUNT&nbsp;/&nbsp;MY INVOICES</a> and view your outstanding invoice. We suggest you to signup for recurrent billing to avoid possible issues in future. You can cancel at any time.</p>
                <a href = "<?php echo $makepaymet; ?>"><img src="http://client.tickletrain.com/app/img/make-payment.png" alt="Pay Now" title="Pay Now"/></a>
                <p style="font-size: 13px; text-align: justify; line-height: 20px;">If you are unable to pay at this time, we can offer our free Blue Line plan, and you can continue to use TickleTrain. It is limited to 10 campaigns so any other campaigns will be permanently deleted. If you wish to downgrade, select any 10 below and click downgrade. If you are canceling for another reason, we would really appreciate your feedback. Thanks for using TickleTrain!</p>
            </div>
            <?php
            //echo '<pre>';
            //print_r($tasks);
            //print_r($maddress);
            //    echo '</pre>';
            //    die();
            ?>
            <input type="hidden" name="baction" value="" id="bulkact"/>
            <fieldset>
                <table cellpadding="0" cellspacing="0" id="maintbl">
                    <thead>
                        <tr>
                            <th style="width:135px" class="hsort<?= GetIf($sfld == 1 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 1 && $sord == 2, " sort_down", "") ?>"
                                rel="1"><input type="checkbox" id="selectAll"/>
                                First Name<span class="sort"><img
                                        src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6" height="4" alt="" rel="1"/><img
                                        src="/<?= GetRootFolder() ?>images/arrow_down.png"
                                        class="down" width="6"
                                        height="4" alt="" rel="1"/></span> </th>
                            <th class="hsort<?= GetIf($sfld == 2 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 2 && $sord == 2, " sort_down", "") ?>"
                                rel="2"<? /* class="sort_up" */ ?>>Last Name <span class="sort"><img
                                        src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                        height="4" alt="" rel="2"/><img
                                        src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="2"/></span> </th>
                            <th
                                class="hsort<?= GetIf($sfld == 3 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 3 && $sord == 2, " sort_down", "") ?>"
                                rel="3">E-mail <span class="sort"><img src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                                                   height="4" rel="3"
                                                                   alt=""/><img src="/<?= GetRootFolder() ?>images/arrow_down.png"
                                                                   class="down" width="6"
                                                                   height="4" alt="" rel="3"/></span></th>
                            <th
                                class="hsort<?= GetIf($sfld == 4 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 4 && $sord == 2, " sort_down", "") ?>"
                                rel="4"<? /* class="sort_down" */ ?>>Original Subject <span class="sort"><img
                                        src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                        height="4" alt="" rel="4"/><img
                                        src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="4"/></span> </th>
                            <th class="hsort<?= GetIf($sfld == 5 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 5 && $sord == 2, " sort_down", "") ?>"
                                rel="5">Tickle <span class="sort"><img src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up"
                                                                   width="6" height="4"
                                                                   alt="" rel="5"/><img
                                                                   src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6"
                                                                   height="4" alt="" rel="5"/></span></th>
                            <th class="hsort<?= GetIf($sfld == 6 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 6 && $sord == 2, " sort_down", "") ?>"
                                rel="6">Schedule <span class="sort"><img src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                                                     height="4"
                                                                     alt="" rel="6"/><img
                                                                     src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6"
                                                                     height="4" alt="" rel="6"/></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?
                        $i = 0;
                        $ix = 1;
                        foreach ($tasks as $MailID => $rows) {
                            $is_approve = false;
                            $is_pause = false;
                            $i = 0;
                            //ksort($rows);
                            foreach ($rows as $rs) {
                                $TickleContact = $rs['TickleContact'];
                                $TickleTrainID = $rs['TickleTrainID'];
                                $FollowTickleTrainID = $rs['FollowTickleTrainID'];
                                $TApprove = $rs['TTApprove'];
                                $IsApproved = $rs['Approve'];
                                $IsPaused = $rs['Pause'];
                                $actions = "";
                                if ($FollowTickleTrainID) {
                                    $TApprove = $rs['FollowTApprove'];
                                }
                                if ($TApprove == 'Y' && $IsApproved != 'Y' && !$is_approve || $IsPaused == 'Y') {
                                    //$actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return ApproveConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Approve=Y') . "');\" class=\"ico_play_pause\">Approve</a></li>";
                                    $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
                                    //$is_approve = true;
                                }
                                if (($TApprove == 'N' || $IsApproved == 'Y') && $IsPaused != 'Y' && !$is_pause) {
                                    $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return PauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Pause=Y') . "');\" class=\"ico_play_pause pause\">Pause</a></li>";
                                    //$is_pause = true;
                                }
                                /* if ($IsPaused == 'Y' && !$is_pause) {
                                  $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
                                  //$is_pause = true;
                                  } */

                                $cid = $rs['ContactID'];
                                $crow = $maddress[$cid];
                                $crow['FirstName'] = str_replace("'","",$crow['FirstName']);
                                $crow['LastName'] = str_replace("'","",$crow['LastName']);
                                $FirstName = $crow['FirstName'];
                                $LastName = $crow['LastName'];
                                $EmailAddr = $crow['EmailID'];



                                $attFiles = GetMailAttachments($rs['RawPath'], $rs['attachments']);
                                if (count($attFiles) == 0) {
                                    $rs['attachments'] = '';
                                }
                                $basepath = preg_replace("/\.txt$/i", "/", $rs['RawPath']);
                                $relpath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $basepath);
                                $TaskCretedDate = convert_date($rs['TaskCretedDate']);
                                //$TaskInitiateDate=convert_date($rs['TaskInitiateDate']);
                                $TaskInitiateDate = convert_date(getlocaltime($rs['TaskGMDate'], $rs['TimeZone']));
                                $time = strtotime($rs['TaskInitiateDate']);
                                $TickleTime = date("h:i A", $time);
                                $TickleDate = date("m-d-y", $time);
                                $message = strip_tags(trim($rs['MessageHtml']));
                                if ($message == "") {
                                    $message = trim($rs['Message']);
                                }
                                if (mb_strlen($message) > 200) {
                                    $message = mb_substr($message, 0, 200) . "...";
                                }
                                $img = "/" . GetRootFolder() . "images/1.jpg";
                                ?>
                                <tr class="maintr<?= (($i) ? ' light tt' . $rs['MailID'] : '') ?>"<?= (($i) ? ' style="display:none"' : '') ?>>
                                    <td><div class="txt"><nobr>
                                                <input type="checkbox" class="listId" name="mailid[]" value="<?= $rs['MailID'] ?>"
                                                       id="task<?= $rs['TaskID'] ?>"/>
                                                <label
                                                    for="task<?= $rs['TaskID'] ?>">
            <?= GetVal($crow['FirstName'], '') ?>
                                                </label>
                                            </nobr> </div></td>
                                    <td><div class="txt"> <nobr>
            <?= GetVal($crow['LastName'], '') ?>
                                            </nobr> </div></td>
                                    <td><div class="txt">
            <?= GetVal($crow['EmailID'], '-') ?>
                                        </div></td>
                                    <td><div class="txt">
            <?= GetVal($rs['Subject'], '(no-subject)') ?>
                                        </div>
                                        <div class="excerpt_block block" style="display:none;">
                                            <p>
            <?= $message ?>
                                            </p>
                                            <!--div class="attached"><a href="#">invoice.pdf</a>, <a href="#">quote.pdf</a></div-->
                                        </div></td>
                                    <td><div class="txt">
            <?= GetVal($rs['TickleName'], '-') ?>
                                        </div></td>
                                    <td><div class="txt"><nobr>
                                                <?= $TickleDate ?>
            <?= $TickleTime ?>
                                            </nobr></div></td>
                                </tr>
                                <!--tr class="h_txt-holder"-->
                                <tr class="childtr<?= (($i) ? ' light tt' . $rs['MailID'] : '') ?>"<?= (($i) ? ' style="display:none"' : '') ?>>
                                    <td><!--
                                    <ul class="h_txt first">
                                        <li>
                                            <a href="#"
                                               onclick="return ExtendedEditContact('<?= $cid ?>','<?= $EmailAddr ?>')">Edit</a>
                                        </li>
                                    </ul> --></td>
                                    <td><!--
                                    <ul class="h_txt">
                                        <li>
                                            <a href="#"
                                               onclick="return ExtendedEditContact('<?= $cid ?>','<?= $EmailAddr ?>')">Edit</a>
                                        </li>
                                    </ul> --></td>
                                    <td><!--
                                    <ul class="h_txt">
                                        <li><a href="#"
                                               onclick="return ExtendedEditContact('<?= $cid ?>','<?= $EmailAddr ?>')">Edit</a></li>
                                        <li><a
                                            href="<?= Url_Create('compose', 'Email=' . $EmailAddr . '&TaskID=' . $rs['TaskID']) ?>">Send
                                            e-mail</a></li>
                                    </ul>
                                        --></td>
                                    <td><ul class="h_txt">
                                            <li> <nobr><a href="#"
                                                          onclick="preview1('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'Mail', '<?= htmlspecialchars($rs['Subject']) ?>');
                    return false;">Original email</a>
            <? if (count($attFiles) > 1) { ?>
                                                    <a href="#"
                                                       onclick="preview1('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'MailAttach', '<?= htmlspecialchars($rs['Subject']) ?>');
                        return false;"><img src="/images/attachment.png" border="0"/></a>
                                                   <? } ?>
                                                   <?
                                                   if (count($attFiles) == 1) {
                                                       $imgArr = getimagesize($basepath . $attFiles[0]);
                                                       if ($imgArr) {
                                                           @chmod($relpath . $attFiles[0], 0777);
                                                           @usleep(100);
                                                           ?>
                                                        <a href="<?= $relpath . $attFiles[0] ?>" class="show_attach"><img src="/images/attachment.png" border="0"/></a>
                <? } else { ?>
                                                        <a href="#"
                                                           onclick="preview1('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'MailAttach', '<?= htmlspecialchars($rs['Subject']) ?>');
                            return false;"><img src="/images/attachment.png" border="0"/></a>
                                                       <?
                                                       }
                                                   }
                                                   ?>
                                            </nobr></li>
                                        </ul></td>
                                    <td><ul class="h_txt">
                                            <li><a href="#"
                                                   onclick="preview1('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'Tickle');
                    return false;">Preview
                                                    tickle</a></li>
                                        </ul></td>
                                    <td></td>
                                </tr>
                                <?
                                $i++;
                            }
                            //foreach
                        }//foreach
                        if (count($tasks) == 0) {
                            ?>
                            <tr>
                                <td colspan="8" align="center">No appropriate campaigns found</td>
                            </tr>
                <? } ?>
                    </tbody>
                </table>
    <? if ($ps > 1) { ?>
                    <div class="pagination">
                        <div class="holder">
                            <ul>
                                    <? for ($j = 1; $j <= $ps; $j++) { ?>
                                    <li<?= (($j == $pg) ? ' class="current"' : '') ?>>
                                        <?= (($j == $pg) ? '<span>' : '<a href="?pg=' . $j . '&q=' . trim($_REQUEST['q']) . '&qdate=' . trim($_REQUEST['qdate']) . '&sort=' . trim($_REQUEST['sort']) . '">') ?>
                                    <?= $j ?>
                                    <?= (($j == $pg) ? '</span>' : '</a>') ?>
                                    </li>
        <? } ?>
                            </ul>
                        </div>
                    </div>
    <? } ?>
            </fieldset>

            <!--

             <div class="form">
                <div class="holder">
                    <div class="frame">
                        <div class="text-holder">
                            <p>Your order has been suspended. And because of that you are not able to see your current compaign. We are suggesting you to either Pay or select five any five compaign from given below <br/> Note : Dowgrade button will downgrade your Plan to Basic <br/><ul>
                             <li><a href="#" class="choosecompaign">Choose Any Five</a></li><?php if (isset($paynowlink)) { ?><li><a href="<?php echo $paynowlink; ?>" class="pay">Pay Now</a></li><?php } ?>
                             </ul></p>


                             <div id ="checkbox" style="display:none; margin-top: 100px">
                                 <form method="POST" action="" style="margin: -90px 0 0">
                                     <ul>
            <?php foreach ($subject as $key => $value) { ?>
                                                 <li style="width:200px;"><input type="checkbox" name="mailid[]"  value="<?php echo $key; ?>"/><?php echo trim($value); ?></li>
    <?php } ?>
                                     </ul>
                                     <input type = "submit" name="choosecompaign" value="Save"/>
                                 </form>
                             </div>

                            <span class="txt">*The Dashboard updates approximately every 5 minutes.</span>
                            <span class="text-ty">Thank you!</span>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
        <div style="text-align:center">
            <input type="submit" name="choosecompaign" value="" class="btn_downgrade"/>
            <div style="color: red; font-size: 11px; margin: 0px auto;">Note: Downgrade button will downgrade your plan and all campaigns other than the 10 selected above will be permanently deleted.</div>
        </div>
    </form>
    <?php
    return;
}
?>
<div class="main_holder">
    <div class="heading">
        <h1>Dashboard</h1>
		<?php if(isset($alertupgrademessage)){ ?>
                
		<a href="<?php echo $alertupgrademessage; ?>" style="float:right; font-size:12px; text-align:center;">Upgrade to the Unlimted Plan<br /> for <strong>less than $5/month.</strong></a>
		<?php } ?>
        <?php
        $percentahecampaign1 = ($campaignallowed / 100) * $warningthresold;
        $percentahecampaign = floor($campaignallowed - $percentahecampaign1);
      //  echo $warningthresold.'<br/>';
      //  echo $campaignallowed;
      //  die();
	 // echo 'sss'.$cnt.'==='.$percentahecampaign;
        if ($cnt >= $percentahecampaign) {
            ?>
            <div class="pages_block2">
                <div class="holder">
                    <div class="num"> <span>
                            <?= $cnt ?>
    <?php if ($campaignallowed != "1000000") { ?> of <?php echo $campaignallowed;
    }
    ?></span> </div>
                    <a href="<?php if($userdetails['Plan'] =='1') echo $supportpage; else echo'#'; ?>" class="btn_up2">up</a> </div>
            </div>
                        <?php } else { ?>
            <div class="pages_block">
                <div class="holder">
                    <div class="num"> <span>
    <?= $cnt ?>
            <?php if ($campaignallowed != "1000000") { ?>  of <?php echo $campaignallowed;
            }
            ?></span> </div>
                    <a href="<?php if($userdetails['Plan'] =='1') echo $supportpage; else echo'#'; ?>" class="btn_up">up</a> </div>
            </div>
<?php } ?>
<? if (!isset($_SESSION['access_token']) && $_SESSION['TickleID'] != "") { ?>
            <div class="fb_block">
                <div id="fb-root"></div>
                <div id="facebookerror" style="margin-left: 5px; display: none;">Get more information about your
                    contacts by <a
                        href="/<?= ROOT_FOLDER ?>fb/settoken/">logging into</a> Facebook.
            <?php if (isset($alertupgrademessage)) { ?>
                        <!-- Your campaign limit is either complete or near about complete. So we suggest you to <a href = "<?php echo $alertupgrademessage; ?>">Upgrade product</a> -->
    <?php } ?>
                </div>
            </div>
<? } ?>
    </div>

    <div class="alert alert-primary" style="display: none;">
        TickleTrain's max email size is 15mb. Please make sure email attachments do not exceed 15mb in total. Thank you!
        
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>

    </div>
    <div class="bar">

        <div class="align_left">
            <form id="bulkActsApply">
                <fieldset>
                    <select id="bactionSelect">
                        <option value="">Bulk actions</option>
                        <option value="delete">Delete</option>
                        <!--option value="approve">Approve</option-->
                        <option value="pause">Pause</option>
                        <option value="unpause">Unpause</option>
                    </select>
                    <input type="submit" value="Apply" class="btn_apply"/>
                </fieldset>
            </form>
        </div>
        <div class="align_right">
	<?php //if($_SERVER['REMOTE_ADDR']=='202.164.47.148'){ ?>
        <label>Show</label>
	<form method="post" id="formperpage">
		<select name='recordperpage' id="selectrec" onchange="this.form.submit();" style="width: 48px;">
			<option value="10">10</option>
			<option value="25">25</option>
			<option value="50">50</option>
			<option value="100">100</option>
		</select>
        </form>
	<label>per page</label>
        <?php //} ?>
            <ul class="bar_buttons">
                <li><a href="#" class="excerpt_all">excerpt</a></li>
                <li><a href="#" class="expand_all">expand</a></li>
            </ul>
            <form>
                <fieldset>
                    <select id="dateFilter" name="qdate" onchange="this.form.submit()">
                        <option value="">Show all dates</option>
                        <? foreach ($tfilter as $ind => $frow): ?>
                            <option
                                value="<?= $ind ?>"<?= getIf(@trim($_REQUEST['qdate']) == $ind, ' selected', '') ?>>
                        <?= $frow ?>
                            </option>
                    <? endforeach ?>
                    </select>
<? /* span class="input_text"><input type="text" id="dateFilter" name="qdate"
  value="<?=@trim($_REQUEST['qdate'])?>"
  placeholder="Show all dates"/></span */ ?>
                    <span class="input_text">
                        <input type="text" name="q" value="<?= @trim($_REQUEST['q']) ?>"/>
                    </span>
                    <input type="submit" value="Filter" class="btn_filter"/>
                </fieldset>
            </form>
        </div>
    </div>
    <form id="bulkActs" method="post">
        <input type="hidden" name="baction" value="" id="bulkact"/>
        <fieldset>
            <table cellpadding="0" cellspacing="0" id="maintbl">
                <thead>
                    <tr>
                        <th style="width:135px" class="hsort<?= GetIf($sfld == 1 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 1 && $sord == 2, " sort_down", "") ?>"
                            rel="1"><input type="checkbox" id="selectAll"/>
                            First Name<span class="sort"><img
                                    src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6" height="4" alt="" rel="1"/><img
                                    src="/<?= GetRootFolder() ?>images/arrow_down.png"
                                    class="down" width="6"
                                    height="4" alt="" rel="1"/></span> </th>
                        <th class="hsort<?= GetIf($sfld == 2 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 2 && $sord == 2, " sort_down", "") ?>"
                            rel="2"<? /* class="sort_up" */ ?>>Last Name <span class="sort"><img
                                    src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                    height="4" alt="" rel="2"/><img
                                    src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="2"/></span> </th>
                        <th class="hsort<?= GetIf($sfld == 3 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 3 && $sord == 2, " sort_down", "") ?>"
                            rel="3"><!--E-mail -->Recipient <span class="sort"><img src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                                               height="4" rel="3"
                                                               alt=""/><img src="/<?= GetRootFolder() ?>images/arrow_down.png"
                                                               class="down" width="6"
                                                               height="4" alt="" rel="3"/></span></th>
                        <th>Sender</th>
                        <th class="hsort<?= GetIf($sfld == 4 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 4 && $sord == 2, " sort_down", "") ?>"
                            rel="4"<? /* class="sort_down" */ ?>>Original Subject <span class="sort"><img
                                    src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                    height="4" alt="" rel="4"/><img
                                    src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6" height="4" alt="" rel="4"/></span> </th>
                        <th class="hsort<?= GetIf($sfld == 5 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 5 && $sord == 2, " sort_down", "") ?>"
                            rel="5">Tickle <span class="sort"><img src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up"
                                                               width="6" height="4"
                                                               alt="" rel="5"/><img
                                                               src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6"
                                                               height="4" alt="" rel="5"/></span></th>
                        <th class="hsort<?= GetIf($sfld == 6 && $sord == 1, " sort_up", "") ?><?= GetIf($sfld == 6 && $sord == 2, " sort_down", "") ?>"
                            rel="6">Schedule <span class="sort"><img src="/<?= GetRootFolder() ?>images/arrow_up.png" class="up" width="6"
                                                                 height="4"
                                                                 alt="" rel="6"/><img
                                                                 src="/<?= GetRootFolder() ?>images/arrow_down.png" class="down" width="6"
                                                                 height="4" alt="" rel="6"/></span></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?
                    $i = 0;
                    $ix = 1;
                    if($_SERVER['REMOTE_ADDR']=='202.164.47.148'){
                      //  echo "<pre>"; print_r($tasks); die();
                    } 
				//	echo '<pre>';print_r($tasks);
                    foreach ($tasks as $MailID => $rows) {
                        $is_approve = false;
                        $is_pause = false;
                        $i = 0;
                        //ksort($rows);
                        foreach ($rows as $key=>$rs) {
			
			//Multiple emails array 22-jan-2016
			    $dropdownemails ='';
			    $tickledata = mysqli_fetch_assoc(mysqli_query($db->conn,"select * from tickleuser where TickleID='".$_SESSION['TickleID']."'"));
			    //echo $tickledata['email_addon'];
			    if($tickledata['email_addon']==''){
				
				$dropdownemails = $tickledata['EmailID'];
			     }
			     else{
				$secid = mysqli_query($db->conn,"select * from secondaryEmail where TickleID='".$_SESSION['TickleID']."'");
                               // $secid = mysqli_fetch_assoc($secidQ);
				$useemails = $rs['secondaryEmailId'];
				//$sender = $secid['EmailID'];
				$usemultiple = true;
				$dropdownemails = '<select name="changeemail" onchange="changeEmail(this.value,'.trim($rs['TaskID']).');return false;"><option value="primary">'.$tickledata['EmailID'].'</option>';
					while($emailsData = mysqli_fetch_assoc($secid)){
						if($emailsData["id"] == $rs['secondaryEmailId'])
							$option ='selected';
						else
							$option = '';
						$dropdownemails .= '<option value="'.$emailsData["id"].'" '.$option.'>'.$emailsData["EmailID"].'</option>';

					}
				$dropdownemails .= '<select name="changeemail">';
			     }
 
			//Multiple emails array 22-jan-2016


				
                            // echo '<pre>';
                            // print_r($rs);
                            // echo '</pre>';
                            //echo $rs['TimeZone'];
                            //echo date_default_timezone_get();

                            $TickleContact = $rs['TickleContact'];
                            $TickleTrainID = $rs['TickleTrainID'];
                            $FollowTickleTrainID = $rs['FollowTickleTrainID'];
                            $TApprove = $rs['TTApprove'];
                            $IsApproved = $rs['Approve'];
                            $IsPaused = $rs['Pause'];
                            $actions = "";
                            if ($FollowTickleTrainID) {
                                $TApprove = $rs['FollowTApprove'];
                            }
                            
                            $get_var = $_GET; if($i!='0'){ $get_var['extended'] = $cid; } 
                            $get_var01 = base64_encode(json_encode($get_var));
                            
                            if ($TApprove == 'Y' && $IsApproved != 'Y' && !$is_approve || $IsPaused == 'Y') {
                                //$actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return ApproveConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Approve=Y') . "');\" class=\"ico_play_pause\">Approve</a></li>";
                                
                                $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "','".$get_var01."' );\" class=\"ico_play_pause\">UnPause</a></li>";
                                //$is_approve = true;
                            }
                            if (($TApprove == 'N' || $IsApproved == 'Y') && $IsPaused != 'Y' && !$is_pause) {
                                $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return PauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Pause=Y') . "','".$get_var01."');\" class=\"ico_play_pause pause\">Pause</a></li>";
                                //$is_pause = true;
                            }
                            /* if ($IsPaused == 'Y' && !$is_pause) {
                              $actions .= "<li><a href=\"javascript:void(0);\"	onclick=\"javascript:return UnPauseConfirm('" . Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&UnPause=Y') . "');\" class=\"ico_play_pause\">UnPause</a></li>";
                              //$is_pause = true;
                              } */
                            $cid = $rs['ContactID'];
                            $crow = $maddress[$cid];
                            $crow['FirstName'] = str_replace("'","",$crow['FirstName']);
                            $crow['LastName'] = str_replace("'","",$crow['LastName']);
                            $FirstName = $crow['FirstName'];
                            $LastName = $crow['LastName'];
                            $EmailAddr = $crow['EmailID'];

//                            echo '<pre>';
//                            print_r($crow);
//                            echo '</pre>';
//                            die();
                            $attFiles = GetMailAttachments($rs['RawPath'], $rs['attachments']);
                            if (count($attFiles) == 0) {
                                $rs['attachments'] = '';
                            }
                            $basepath = preg_replace("/\.txt$/i", "/", $rs['RawPath']);
                            $relpath = str_replace($_SERVER['DOCUMENT_ROOT'], "", $basepath);
                            $TaskCretedDate = convert_date($rs['TaskCretedDate']);
                            //$TaskInitiateDate=convert_date($rs['TaskInitiateDate']);
                            $TaskInitiateDate = convert_date(getlocaltime($rs['TaskGMDate'], $rs['TimeZone']));
                            $time = strtotime($rs['TaskInitiateDate']);
                            $TickleTime = date("h:i A", $time);
                            $TickleDate = date("m-d-y", $time);
                            $message = strip_tags(trim($rs['MessageHtml']));
                            if ($message == "") {
                                $message = trim($rs['Message']);
                            }
                            if (mb_strlen($message) > 200) {
                                $message = mb_substr($message, 0, 200) . "...";
                            }
                            $img = "/" . GetRootFolder() . "images/1.jpg";
                            ?>
                            <tr class="maintr<?= (($i) ? ' light tt' . $rs['MailID'] : '') ?>"<?php if($i=='0' || $_GET['extended']==$cid){ echo ' style="display:"'; }else{ echo ' style="display:none"'; } ?> id="maintr<?php echo $key; ?>">
                                <td><div class="txt"><nobr>
                                            <input type="checkbox" class="listId" name="TaskID[]" value="<?= $rs['TaskID'] ?>"
                                                   id="task<?= $rs['TaskID'] ?>"/>
                                            <label for="task<?= $rs['TaskID'] ?>"> <?php if ($crow['FirstName'] != $crow['EmailID']) { ?>  <?= GetVal($crow['FirstName'], '') ?>
                                                    <?php } ?>
                                            </label>
                                        </nobr> </div></td>
                                <td><div class="txt"> <nobr>
        <?= GetVal($crow['LastName'], '') ?>
                                        </nobr> </div></td>
                                <td><div class="txt">
                                            <?= GetVal($crow['EmailID'], '-') ?>
                                    </div></td>
                                <td><div class="txt">
					<!---- multiple Emails code---->
					
					    <?= $dropdownemails ?>

					<!---- multiple Emails code---->
                                        <!--<ul class="social_nw">
        <? if (isset($_SESSION['access_token'])) { ?>
                                                <li id="fb-<?= $rs['TaskID'] ?>" style="display:none"></li>
                                                <script>$('#fb-<?= $rs['TaskID'] ?>').load('<?= Url_Create("fb/image", "email=" . $EmailAddr . "&cid=" . $cid) ?>', function(data) {
            if (data != '') {
            $(this).show()
            }
            })</script>
        <? } ?>
                                        <? /* li><img src="/<?=GetRootFolder()?>images/ico_linked_in.png" width="13" height="13"
                                          alt=""/></li */ ?>
                                        </ul>-->
                                    </div></td>
                                <td><div class="txt">
                                            <?= GetVal($rs['Subject'], '(no-subject)') ?>
                                    </div>
                                    <div class="excerpt_block block" style="display:none;">
                                        <p>
                                        <?= $message ?>
                                        </p>
                                        <!--div class="attached"><a href="#">invoice.pdf</a>, <a href="#">quote.pdf</a></div-->
                                    </div></td>
                                <td><div class="txt">
                                            <?= GetVal($rs['TickleName'], '-') ?>
                                    </div></td>
                                <td><div class="txt">
                                    <nobr>
                                        <?= $TickleDate ?>
                                        <?= $TickleTime ?>
                                        <?php echo ($rs['EndAfterFollow'] == 13)?'∞':''; ?>
                                    </nobr>
                                    </div></td>
                                <td><div class="txt">
                                        <ul class="icons">
                                            <li><a href="javascript:void(0);"
                                                   onclick="javascript:return DeleteConfirm('<?= Url_Create('home', 'TaskID=' . $rs['TaskID'] . '&Delete=Y') ?>', '','<?php echo $get_var01; ?>');"
                                                   class="ico_basket">Delete</a></li>
        <?= $actions ?>
        <? if (!$i && count($tasks[$rs['MailID']."=key"]) > 1) { ?>
                                                <li><a href="#" class="ico_expand" id="ex<?= $rs['MailID'] ?>">expand/exerpt</a> </li>
        <? } ?>
                                        </ul>
                                    </div></td>
                            </tr>
                            <!--tr class="h_txt-holder"-->
                            <tr class="childtr<?= (($i) ? ' light tt' . $rs['MailID'] : '') ?>" <?php if($i=='0' || $_GET['extended']==$cid){ echo ' style="display:"'; }else{ echo ' style="display:none"'; } ?>>
                                <td><ul class="h_txt first">
                                        <li> <a href=#"
                                                onclick="return ExtendedEditContact('<?= $cid ?>', '<?= $EmailAddr ?>','<?php $get_var = $_GET; if($i!='0'){ $get_var['extended'] = $cid; } echo base64_encode(json_encode($get_var));?>', 'maintr<?php echo $key; ?>') ">Edit </a> </li>
                                    </ul></td>
                                <td><ul class="h_txt">
                                        <li> <a href="#"
                                                onclick="return ExtendedEditContact('<?= $cid ?>', '<?= $EmailAddr ?>','<?php $get_var = $_GET; if($i!='0'){ $get_var['extended'] = $cid; } echo base64_encode(json_encode($get_var));?>','maintr<?php echo $key; ?>') ">Edit</a> </li>
                                    </ul></td>
                                <td><!-- <ul class="h_txt">
                                        <li><a href="#"
                                               onclick="return ExtendedEditContact('<?= $cid ?>', '<?= $EmailAddr ?>','<?php $get_var = $_GET; if($i!='0'){ $get_var['extended'] = $cid; } echo base64_encode(json_encode($get_var));?>', 'maintr<?php echo $key; ?>')">Edit</a></li>
                                        <li><a
                                                href="<?= Url_Create('compose', 'Email=' . $EmailAddr . '&TaskID=' . $rs['TaskID']) ?>">Send
                                                e-mail</a></li>
                                    </ul> --></td>
                                <td></td>
                                <td><ul class="h_txt">
                                        <li><nobr><a href="#"
                                                     onclick="preview('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'Mail', '<?= htmlspecialchars(str_replace("'", "", $rs['Subject'])) ?>');
                return false;">Original email</a>
                                               <? if (count($attFiles) > 1) { ?>
                                                <a href="#"
                                                   onclick="preview('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'MailAttach', '<?= htmlspecialchars(str_replace("'", "", $rs['Subject'])) ?>');
                    return false;"><img src="/images/attachment.png" border="0"/></a>
                                               <? } ?>
                                               <? 
                                               
                                               if (count($attFiles) == 1) {
                                                   $imgArr = getimagesize($basepath . $attFiles[0]);
                                                   if ($imgArr) {
                                                       // @chmod($relpath . $attFiles[0], 0777);
                                                       // @usleep(100);
                                                       ?>
                                                    <a href="<?= $relpath . $attFiles[0] ?>" class="show_attach"><img src="/images/attachment.png" border="0"/></a>
                                                   <? } else { ?>
                                                    <a href="#"
                                                       onclick="preview('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'MailAttach', '<?= htmlspecialchars(str_replace("'", "", $rs['Subject'])) ?>');
                        return false;"><img src="/images/attachment.png" border="0"/></a>
            <?
            }
        }
        ?>
                                        </nobr></li>
                                    </ul></td>
                                <td><ul class="h_txt">
                                        <li><a href="#"
                                               onclick="preview('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>', 'Tickle');
                return false;">Preview tickle</a></li>
                                    </ul></td>
                                <td><ul class="h_txt">
                                        <li><a href="#" onclick="return ChangeTask('<?= $rs['TaskID'] ?>', '<?= $rs['MailID'] ?>' , 'maintr<?php echo $key; ?>');"> Edit</a> </li>
                                    </ul></td>
                                <td></td>
                            </tr>
                            <?
                            $i++;
                        }
                        //foreach
                    }//foreach
                    if (count($tasks) == 0) {
                        ?>
                        <tr>
                            <td colspan="8" align="center">No appropriate campaigns found</td>
                        </tr>
<? } ?>
                </tbody>
            </table>
                            <? if ($ps > 1) { ?>
                <div class="pagination">
                    <div class="holder">
                        <ul>
                            <? for ($j = 1; $j <= $ps; $j++) { ?>
                                <li<?= (($j == $pg) ? ' class="current"' : '') ?>>
        <?= (($j == $pg) ? '<span>' : '<a href="?pg=' . $j . '&q=' . trim($_REQUEST['q']) . '&qdate=' . trim($_REQUEST['qdate']) . '&sort=' . trim($_REQUEST['sort']) . '">') ?>
        <?= $j ?>
                    <?= (($j == $pg) ? '</span>' : '</a>') ?>
                                </li>
    <? } ?>
                        </ul>
                    </div>
                </div>
<? } ?>
        </fieldset>
    </form>
</div>
<?php
$email = $mainemail;
$autoauthkey = "abcXYZ123";
$timestamp = time(); # Get current timestamp
$hash = sha1($email . $timestamp . $autoauthkey);

if (isset($_GET['chromeextension'])) {
    //header("location:https://client.tickletrain.com/fb/image/?email=".$_GET['emailid']."&ret=infocmupd&chromeextension=yes");
}
?>
<!--   <div style="margin-left:180px;">
 <ul id="js-news" class="js-hidden">


                <li class="news-item"></li>


 </ul>
      </div> -->
<script type="text/javascript">
   $(document).ready(function(){


    if(Cookies.get('Mail Acknowledge') != 'true'){
       $('.alert').fadeIn(); 
    }

    $('.alert .close').on('click',function(){
        $(this).parent().fadeOut();
        Cookies.defaults = {
            path: '/',
            secure: true
        };
        Cookies.set('Mail Acknowledge', 'true');
    });

   });
</script>

