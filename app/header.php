<?php
if ($_SERVER[REQUEST_URI] == "/login" || strpos($_SERVER[REQUEST_URI], "/register") === 0) {
//die('xvxcvx');    
} else {
//    $s = empty($_SERVER["HTTPS"]) ? '' : ($_SERVER["HTTPS"] == "on") ? "s" : "";
//    $sp = strtolower($_SERVER["SERVER_PROTOCOL"]);
//    $protocol = substr($sp, 0, strpos($sp, "/")) . $s;
//    if($protocol == 'https'){
//        header ('HTTP/1.1 301 Moved Permanently');
//        header ("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
//        exit();
//        //header("location:http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
//    }
}


/**

 * The Header for our theme.

 *

 * Displays all of the <head> section and everything up till <div id="main">

 *

 * @package WordPress

 * @subpackage Twenty_Eleven

 * @since Twenty Eleven 1.0

 */
?><!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">

<!--[if IE 6]>

<html id="ie6" <?php language_attributes(); ?>>

<![endif]-->

<!--[if IE 7]>

<html id="ie7" <?php language_attributes(); ?>>

<![endif]-->

<!--[if IE 8]>

<html id="ie8" <?php language_attributes(); ?>>

<![endif]-->

<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->

<html <?php language_attributes(); ?>>

    <!--<![endif]-->

    <head>

        <meta charset="<?php bloginfo('charset'); ?>" />

        <!--<meta name="viewport" content="width=device-width" />
        <meta charset="utf-8">-->
        <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1" />
        <!--<meta name="viewport"
        content="width=device-width,initial-scale=1">-->
        <!--<meta name="viewport" content="width=1024" />-->

        <title><?php
/*

 * Print the <title> tag based on what is being viewed.

 */

global $page, $paged;



wp_title('|', true, 'right');



// Add the blog name.

bloginfo('name');



// Add the blog description for the home/front page.

$site_description = get_bloginfo('description', 'display');

if ($site_description && ( is_home() || is_front_page() ))
    echo " | $site_description";



// Add a page number if necessary:

if ($paged >= 2 || $page >= 2)
    echo ' | ' . sprintf(__('Page %s', 'twentyeleven'), max($paged, $page));
?></title>
        <meta property="fb:app_id" content="189567617784574" />

        <meta property="og:title" content="Tickle Train | Send It. And Forget It." />

        <meta property="og:type" content="website" />

        <meta property="og:url" content="http://tickletrain.com/" />

        <meta property="og:image" content="http://tickletrain.com/logo.png" />

        <link rel="profile" href="http://gmpg.org/xfn/11" />

        <link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('stylesheet_url'); ?>" />

        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />



        <!--[if lt IE 9]>
        
        <script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
        
        <![endif]-->

<?php
/* We add some JavaScript to pages with the comment form

 * to support sites with threaded comments (when in use).

 */

if (is_singular() && get_option('thread_comments'))
    wp_enqueue_script('comment-reply');



/* Always have wp_head() just before the closing </head>

 * tag of your theme, or you will break many plugins, which

 * generally use this hook to add elements to <head> such

 * as styles, scripts, and meta tags.

 */

wp_head();
?>
        <?
        ?>

        <!--conditional comments -->

        <!--[if IE]>  
        
                <script src="<?php bloginfo('stylesheet_directory'); ?>/js/html5.js"></script>
        
        <![endif]-->



        <!--[if lte IE 6]>
        
            <script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/pngfix.js"></script>
        
            <script type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/js/ie6.js"></script>
        
            <link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/css/ie6.css" type="text/css" />
        
            <![endif]-->    



        <!--Slider Start-->

<?php /* ?><script src="<?php bloginfo('stylesheet_directory');?>/js/jquery-1.4.1.min.js" type="text/javascript"></script>

  <script src="<?php bloginfo('stylesheet_directory');?>/js/jquery.jcarousel.pack.js" type="text/javascript"></script>

  <script src="<?php bloginfo('stylesheet_directory');?>/js/jquery-func.js" type="text/javascript"></script><?php */ ?>

        <!--Slider End-->
<?php /* ?><link rel="stylesheet" href="<?php bloginfo('stylesheet_directory');?>/css/base_packaged.css" type="text/css"><?php */ ?>
<?php /* ?><script src="<?php bloginfo('stylesheet_directory');?>/js/base_packaged.js" type="text/javascript"></script>
  <script src="<?php bloginfo('stylesheet_directory');?>/js/42u.js" type="text/javascript"></script><?php */ ?>

        <style type="text/css">

            @font-face {

                font-family: 'UNIVCD';

                src: url('<?php bloginfo('stylesheet_directory'); ?>/fonts/UNIVCD.eot');

                src: local('fonts/UNIVCD.eot'), url('<?php bloginfo('stylesheet_directory'); ?>/fonts/UNIVCD.woff') format('woff'), url('<?php bloginfo('stylesheet_directory'); ?>/fonts/UNIVCD.ttf') format('truetype'), url('<?php bloginfo('stylesheet_directory'); ?>/fonts/UNIVCD.svg') format('svg');

                font-weight: normal;

                font-style: normal;

            }


            #fancybox-content{
                width:590px !important;
                height:540px !important;
                overflow-y :scroll !important;
            }
        </style>
        <!--<script>
        
        var timer;
        
        
        function startCount()
        {
                timer = setInterval(count,4000);
        }
        
        var isInteger_re     = /^\s*(\+|-)?\d+\s*$/;
        function isInteger (s) {
           return String(s).search (isInteger_re) != -1
        }
        function count()
        {
                var el = document.getElementById('randomnumber').value;
                var aa = parseInt(el) + Math.floor((Math.random()*4)+1);
                document.getElementById('counter').innerHTML = aa ;
                //alert(aa);
                document.getElementById('randomnumber').value = aa;
        }
        
        
        </script>-->
        <script type="text/javascript">var switchTo5x = true;</script>

        <link rel="shortcut icon" type="image/x-icon" href="<?php bloginfo('stylesheet_directory'); ?>/images/favicon.ico">
        <script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.3.min.js"></script>
        <!--<script>
            
            $(document).ready(function(){
                 $(".putR").click(function(){
                 alert('4234324234');
                 $(".fancybox").fancybox();
                 $.fancybox({
                        'width'		: '100%',   
                       'height'	: '100%',
                        'autoScale'     : true,
                        'transitionIn'	: 'none',
                      'transitionOut'	: 'none',
                            'scrolling':'auto',
                        'type'		: 'iframe',
                        'content'       : "<h1>Amit Kaushik</h1>"    
                });
                
              }); 
            });
        </script>-->
        <!-- Add jQuery library -->
        <!--	<script type="text/javascript" src="../lib/jquery-1.9.0.min.js"></script>-->

        <!-- Add mousewheel plugin (this is optional) -->
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/fancyBox/lib/jquery.mousewheel-3.0.6.pack.js"></script>

        <!-- Add fancyBox main JS and CSS files -->
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/fancyBox/source/jquery.fancybox.js?v=2.1.4"></script>
        <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/fancyBox/source/jquery.fancybox.css?v=2.1.4" media="screen" />

        <!-- Add Button helper (this is optional) -->
        <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/fancyBox/source/helpers/jquery.fancybox-buttons.css?v=1.0.5" />
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/fancyBox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>

        <!-- Add Thumbnail helper (this is optional) -->
        <link rel="stylesheet" type="text/css" href="<?php echo get_template_directory_uri(); ?>/fancyBox/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7" />
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/fancyBox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>

        <!-- Add Media helper (this is optional) -->
        <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/fancyBox/source/helpers/jquery.fancybox-media.js?v=1.0.5"></script>
        <script type="text/javascript">
            $(document).ready(function() {

                $('.fancybox-media').fancybox({
                    openEffect: 'none',
                    closeEffect: 'none',
                    width: '1000',
                    helpers: {
                        media: {}
                    }
                });
            });
        </script>
        <script language="javascript" type="text/javascript">

            function randomString() {

                var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZ";
                var string_length = 5;
                var randomstring = '';
                for (var i = 0; i < string_length; i++) {
                    var rnum = Math.floor(Math.random() * chars.length);
                    randomstring += chars.substring(rnum, rnum + 1);

                }

                document.mailingfrm.randomfield.value = randomstring;
            }
        </script>
        <script type="text/javascript">
            function validatemailingfrm()
            {
                if (document.mailingfrm.nme.value == '')
                {
                    alert("Please Enter Your Name");
                    document.mailingfrm.nme.focus();
                    return false
                }
                else if (document.mailingfrm.email.value == '')
                {
                    alert("Please Enter Email Address");
                    document.mailingfrm.email.focus();
                    return false
                }
                else if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('email').value)))
                {
                    alert("Please Enter Valid Email Address");
                    document.getElementById('email').focus();
                    return false;
                }
                else if (document.mailingfrm.sub.value == '')
                {
                    alert("Please Enter Subject");
                    document.mailingfrm.sub.focus();
                    return false
                }
                else if (document.mailingfrm.message.value == '')
                {
                    alert("Please Enter Your Message");
                    document.mailingfrm.message.focus();
                    return false
                }
                else if (document.mailingfrm.input_captcha.value == "")
                {
                    alert("Please enter above show code");
                    document.mailingfrm.input_captcha.focus();
                    return false;
                }
                // Validate the Entered input aganist the generated security code function  
                var str1 = document.getElementById('randomfield').value;
                var str2 = document.getElementById('input_captcha').value;
                if (str1 == str2)
                    return true;
                alert('Invaild code please enter again above code');
                document.mailingfrm.input_captcha.focus();
                return false;
                return false;
            }
        </script>


        <script type="text/javascript">
            function validatemailingfrm1()
            {
                if (document.mailingfrm1.email.value == '')
                {
                    alert("Please Enter Your Email Address or Username");
                    document.mailingfrm1.email.focus();
                    return false
                }

                //	else if(!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('email').value)))
        //		{
        //		alert("Please Enter Valid Username or Email Address");
        //		document.getElementById('email').focus();
        //		return false;
        //		}

                else
                    (document.mailingfrm1.password.value == '')
                {
                    alert("Please Enter Your Password");
                    document.mailingfrm1.password.focus();
                    return false
                }
            }
        </script>
        <script type="text/javascript">
            function validatemailingfrm2()
            {

                if (document.mailingfrm2.username.value == '')
                {
                    alert("Please Enter Your Username");
                    document.mailingfrm2.username.focus();
                    return false
                }
                else if (document.mailingfrm2.password.value == '')
                {
                    alert("Please Enter Password");
                    document.mailingfrm2.password.focus();
                    return false
                }
                else if (document.mailingfrm2.email.value == '')
                {
                    alert("Please Enter Your Email Address");
                    document.mailingfrm2.email.focus();
                    return false
                }

                else if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('email').value)))
                {
                    alert("Please Enter Valid Email Address");
                    document.getElementById('email').focus();
                    return false;
                }
                //else if (document.mailingfrm2.timezone.value =='')
                //{
                //	alert("Please Select Time Zone");
                //        document.mailingfrm2.timezone.focus();
                //      return false
                // }

                else if (document.mailingfrm2.RPassword.value == '')
                {
                    alert("Please Enter Repeat Password");
                    document.mailingfrm2.RPassword.focus();
                    return false
                }

                else if (document.mailingfrm2.RPassword.value != document.mailingfrm2.password.value)
                {
                    alert("Password and  Repeat password must be same");
                    document.mailingfrm2.RPassword.focus();
                    return false
                }


                else if (document.mailingfrm2.REmailID.value != document.mailingfrm2.email.value)
                {
                    alert("Email and Repeat Email must be same");
                    document.mailingfrm2.REmailID.focus();
                    return false
                }

                else if (document.mailingfrm2.REmailID.value == '')
                {
                    alert("Please Enter Repeat Email");
                    document.mailingfrm2.REmailID.focus();
                    return false
                }

                else if (document.mailingfrm2.firstname.value == '')
                {
                    alert("Please Enter Your First Name");
                    document.mailingfrm2.firstname.focus();
                    return false
                }
                else if (document.mailingfrm2.lastname.value == '')
                {
                    alert("Please Enter Your Last Name");
                    document.mailingfrm2.lastname.focus();
                    return false
                }

                else if (document.mailingfrm2.Phone.value == '' && document.mailingfrm2.Plan.value != '1')
                {
                    alert("Please Enter Your Phone Number");
                    document.mailingfrm2.Phone.focus();
                    return false
                }
                else if (document.mailingfrm2.Address.value == '' && document.mailingfrm2.Plan.value != '1')
                {
                    alert("Please Enter Your Address");
                    document.mailingfrm2.Address.focus();
                    return false
                }
                else if (document.mailingfrm2.City.value == '' && document.mailingfrm2.Plan.value != '1')
                {
                    alert("Please Enter Your City");
                    document.mailingfrm2.City.focus();
                    return false
                }
                else if (document.mailingfrm2.PostCode.value == '' && document.mailingfrm2.Plan.value != '1')
                {
                    alert("Please Enter Your Post Code");
                    document.mailingfrm2.PostCode.focus();
                    return false
                }
                else if (document.mailingfrm2.country.value == '' && document.mailingfrm2.Plan.value != '1')
                {
                    alert("Please Select Your Country");
                    document.mailingfrm2.country.focus();
                    return false
                }
                else if (document.mailingfrm2.state.value == '' && document.mailingfrm2.Plan.value != '1')
                {
                    alert("Please Select Your State");
                    document.mailingfrm2.state.focus();
                    return false
                }



            }
        </script>

        <script type="text/javascript">
            function myFunction()
            {
                setInterval(function() {
                    myTimer()
                }, 4000);
            }

            function myTimer()
            {
                var store_val = document.getElementById("random_no").value;

                if (!store_val) {
                    var seconds = <?= mktime() ?> - 1352184308;
                    document.getElementById("random_no").value = seconds;
                    store_val = seconds;
                }

                var final = Math.floor((Math.random() * 4) + 1);
                seconds = parseInt(store_val) + final;
                document.getElementById("random_no").value = seconds;
                document.getElementById("counter1").innerHTML = seconds;

            }







            function validatemailingfrm3(event)
            {

                if (document.mailingfrm2.username.value == '')
                {
                    alert("Please Enter Your Username");
                    document.mailingfrm2.username.focus();
                    return false
                }
                else if (document.mailingfrm2.password.value == '')
                {
                    alert("Please Enter Password");
                    document.mailingfrm2.password.focus();
                    return false
                }
                else if (document.mailingfrm2.email.value == '')
                {
                    alert("Please Enter Your Email Address");
                    document.mailingfrm2.email.focus();
                    return false
                }

                else if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.getElementById('email').value)))
                {
                    alert("Please Enter Valid Email Address");
                    document.getElementById('email').focus();
                    return false;
                }
                //else if (document.mailingfrm2.timezone.value =='')
                //	{
                //		alert("Please Select Time Zone");
                //		document.mailingfrm2.timezone.focus();
                //		return false
                //	}

                else if (document.mailingfrm2.RPassword.value == '')
                {
                    alert("Please Enter Repeat Password");
                    document.mailingfrm2.RPassword.focus();
                    return false
                }

                else if (document.mailingfrm2.RPassword.value != document.mailingfrm2.password.value)
                {
                    alert("Password and  Repeat password must be same");
                    document.mailingfrm2.RPassword.focus();
                    return false
                }


                else if (document.mailingfrm2.REmailID.value != document.mailingfrm2.email.value)
                {
                    alert("Email and Repeat Email must be same");
                    document.mailingfrm2.REmailID.focus();
                    return false
                }

                else if (document.mailingfrm2.REmailID.value == '')
                {
                    alert("Please Enter Repeat Email");
                    document.mailingfrm2.REmailID.focus();
                    return false
                }

                else if (document.mailingfrm2.firstname.value == '')
                {
                    alert("Please Enter Your First Name");
                    document.mailingfrm2.firstname.focus();
                    return false
                }
                else if (document.mailingfrm2.lastname.value == '')
                {
                    alert("Please Enter Your Last Name");
                    document.mailingfrm2.lastname.focus();
                    return false
                }



            }
        </script>

        <script type="text/javascript">
            function myFunction()
            {
                setInterval(function() {
                    myTimer()
                }, 4000);
            }

            function myTimer()
            {
                var store_val = document.getElementById("random_no").value;

                if (!store_val) {
                    var seconds = <?= mktime() ?> - 1352184308;
                    document.getElementById("random_no").value = seconds;
                    store_val = seconds;
                }

                var final = Math.floor((Math.random() * 4) + 1);
                seconds = parseInt(store_val) + final;
                document.getElementById("random_no").value = seconds;
                document.getElementById("counter1").innerHTML = seconds;

            }


		
		
        </script>

    </head>
    <!--<body onLoad="startCount(); randomString();">-->
    <body onload = "myFunction();
        randomString();">

        <script type="text/javascript" charset="utf-8">
            var is_ssl = ("http:" == document.location.protocol);
            var asset_host = is_ssl ? "http://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";
            document.write(unescape("%3Cscript src='" + asset_host + "javascripts/feedback-v2.js' type='text/javascript'%3E%3C/script%3E"));
        </script>

        <script src="ShareThis%20-%20Share%20Buttons,%20Share%20Plugin,%20Share%20Analytics,%20Media%20Solutions_files/feedback-v2.js" type="text/javascript"></script>

        <!--<script type="text/javascript" charset="utf-8">
          var feedback_widget_options = {};
          feedback_widget_options.display = "overlay";  
          feedback_widget_options.company = "sharethis";
          feedback_widget_options.placement = "right";
          feedback_widget_options.color = "#666";
          feedback_widget_options.style = "question";
          var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);
        </script>
    <a href="#" id="fdbk_tab" class="fdbk_tab_right" style="background-color:#666; position:fixed; top:25%; right:0px;">FEEDBACK</a>
    <div id="fdbk_overlay" style="display:none"><div id="fdbk_container"><a href="#" id="fdbk_close"></a></div><div id="fdbk_screen"></div></div>-->

        <!--wraper Start-->

        <div id="fancy" style="width:100%; height:100%; display: none">
<?php ?>   
        </div>


        <div id="wraper">

            <!--Header Start-->

            <header id="header">

                <div class="headerCntr">

                    <!--Logo Start-->

                    <h1><a href="/">Tickle Train for Email Follow-Up</a></h1>

                    <!--Logo End-->
                    <div style="padding-top: 29px;
                         position: absolute;
                         z-index: 9999;
                         margin-left: 935px;width:65px;"><?php if(!is_user_logged_in()) { ?><a href="/login">Log In</a><?php }else{ ?><a href="<?php echo wp_logout_url();  ?>">Logout</a> <?php }?></div> 
                    <!--hdRgt Start-->

                    <div class="hdRgt">

                        <!--Main Menu Start-->

                        <nav>

                            <div class="menu2">

                                <div class="menu_mid">

                                    <div class="menu_lft">

                                        <div class="menu_rgt">

                                            <ul>

<?php wp_nav_menu(array('menu' => 'topmenu', 'link_before' => '<span>', 'link_after' => '</span>')); ?>

                                            </ul>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </nav>                        						

                        <!--Main Menu End-->

                        <div class="cl"></div>

<?php /* ?><div class="tMenu">

  <ul>

  <li><a href="#">Account</a></li>

  <li class="pad">|</li>

  <li><a href="#">Support</a></li>

  <li class="pad">|</li>

  <li><a href="#" class="last">Sign Up</a></li>

  </ul>

  </div>

  <div class="cl"></div>

  <p class="wel">Welcome, <span>Jason</span></p>

  <p class="wel"><?php if ( is_active_sidebar( 'sidebar-7' ) ) : ?>

  <?php dynamic_sidebar( 'sidebar-7' ); ?>

  <?php endif; ?></p><?php */ ?>

                    </div>

                    <!--hdRgt End-->

                    <div class="cl"></div>

                </div>

            </header>

            <!--Header End-->





