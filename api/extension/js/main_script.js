var is_loged_in = false;
var TickleUser =  null ;
var ContactList =  null ;
var jc = null;
var API_URL = "https://client.tickletrain.com/api/api.php" ; 
var logo =	chrome.extension.getURL('images/dashboard_logo.png'); 
var default_contect_image =	chrome.extension.getURL('images/defaul_user36*36.png'); 
var defaul_user =	chrome.extension.getURL('images/defaul_user.png');
var right_arrow =	chrome.extension.getURL('images/arrow-point-to-right.svg');
var edit_icon =	chrome.extension.getURL('images/edit.svg');
var autocompleteList =[];
var Preview_tickle_dates = [];
var datepickler =  null ;
var CurrentSlide = 0 ;
var wd = 0;
  
//var rte = chrome.extension.getURL('/assets/css/rte.css'); 

(function($){

	// This will append datalist to Gmail body 
 	function func1() {
 		var postData = { 'method':'campaignList' , user_id : TickleUser.UserID };
 		$.post('https://client.tickletrain.com/api/api.php',postData , function(response,status) {
			//console.log(response);
			console.log(response);
			if(status == "success"){
				var Obj =  JSON.parse(response);
				if(Obj.status){
					var textnode;
					$.each(Obj.data, function(ind,val){
						if(val.CustomSubject !== '' && val.CustomSubject !== null){
						    textnode = val.CustomSubject;
						}else if(val.Subject !== '' && val.Subject !== null){
							textnode = val.Subject;
						}else{
							return true;
						}
						autocompleteList.push(textnode);
					
					});
				}
			}
		});
	}


	chrome.storage.sync.get(['tickle_user'], function(result) {
	  if(typeof result.tickle_user != 'undefined'){
	  	is_loged_in = true;
	  	TickleUser = result.tickle_user;
	  	display_index(result.tickle_user);
	  	func1();
	  	getDashboard();
	  }
	});

	var height = (($('.Tm.aeJ').height()*80)/100);
	$('.scrl.scroll2').height(height+"px");

	$( window ).resize(function() {
	   var height =  (($('.Tm.aeJ').height()*80)/100);
	   $('.scrl.scroll2').height(height+"px");
	});

	// tickle on chnage
	   $(document).on('change','.tickle-select',function(e){
	   		e.preventDefault();
		   	var tickle_id = $(this).children("option:selected").val();
		   	var tickle_name = $(this).children("option:selected").text();
	   	    if(tickle_id != ''){
	   	 		getTickleFollow(tickle_id,tickle_name,TickleUser.UserID,TickleUser.UserName);
	   	    }
	   });

    // 


	 // view tickle
		$(document).on('click','.view-tickle',function(e) {
			e.preventDefault();
		 	var tickle_id = this.id;
		 	var tickle_type = $(this).data('type');
		 	$.confirm({
	              title: 'View Tickle',
	              content: function(){
	                var self = this;
	                return reviewEmail(self,tickle_id,tickle_type);
	              } ,
	              type: 'red',
	              typeAnimated: true,
	              columnClass: 'col-md-8',
	              buttons: {
	                  close: function () {
	                  }
	              }
	          });
		});
	// 

	// tt-delete-campaign
	$(document).on('click' , '.tt-delete-btn', function(e){
		e.preventDefault();
		var MailID =  $(this).data().id;
		$.confirm({
			title: 'Are you sure?',
			content: 'This will delete your campaign.', 
			type: 'red',
			//icon: 'fa fa-warning',
			typeAnimated: true,
			buttons: {
				confirm: {
					text: 'Yes, Delete it!',
					btnClass: 'btn-red',
					action: function(){

						$('.aDh').find('.tt-reply-compose-box').remove();

						var postData = {'method':'deleteCampaign' , 'MailID':MailID , 'user_id' :  TickleUser.UserID };
						$.post('https://client.tickletrain.com/api/api.php',postData , function(response,status) {
							if(status == "success"){
								$('.tt-thread-status-box').remove();
								$('.tt-thread-lable').css('display','none');
								getTicklesJosn(TickleUser.UserID,'json',MailID);
								
							}
						});
					}
				},
				Cancel: function () {
				}
			}
		});
	});

	
    // dashboard page delete single email icon click event
	$(document).on('click' , '.del-btn', function(e){
		e.preventDefault();
		var TaskID =  $(this).data().task_id;
		$.confirm({
			title: 'Are you sure?',
			content: 'Only this email will be deleted from your campaign. To delete the entire campaign, click the delete icon in collapsed view.', 
			type: 'red',
			//icon: 'fa fa-warning',
			typeAnimated: true,
			buttons: {
				confirm: {
					text: 'Yes, Delete it!',
					btnClass: 'btn-red',
					action: function(){
						var postData = {'method':'deleteCampaign' , 'TaskID':TaskID , 'user_id' :  TickleUser.UserID };
						$.post('https://client.tickletrain.com/api/api.php',postData , function(response,status) {
							if(status == "success"){
								getDashboard();
							}
						});
					}
				},
				Cancel: function () {
				}
			}
		});
	});

	$(document).on('click' , '.del-btn-all', function(e){
		e.preventDefault();
		var MailID =  $(this).data().id;
		$.confirm({
			title: 'Are you sure?',
			content: 'This will delete your campaign to this contact.', 
			type: 'red',
			//icon: 'fa fa-warning',
			typeAnimated: true,
			buttons: {
				confirm: {
					text: 'Yes, Delete it!',
					btnClass: 'btn-red',
					action: function(){
						var postData = {'method':'deleteCampaign' , 'MailID':MailID , 'user_id' :  TickleUser.UserID };
						$.post('https://client.tickletrain.com/api/api.php',postData , function(response,status) {
							if(status == "success"){
								getDashboard();
							}
						});
					}
				},
				Cancel: function () {
				}
			}
		});
	});

    // upcomming page delete icon click event
	$(document).on('click' , '.email-btn.opened', function(e){
		e.preventDefault();
		$timeline_box = $(this).closest('li').find('.timeline-box');
		$(this).removeClass('opened').addClass('toggle_timeline_box').addClass('open');
		var MailID =  $(this).data().id;
		var data =  {'method': 'getMailactivity' , 'MailID' : MailID } ;
			$.ajax({
				url: 'https://client.tickletrain.com/api/api.php',
		        data: data,
		        type: "POST",
		        success: function (response) {
		        	//if(response.status){
		        		$timeline_box.html(response).show();
		        	//}else{
		        	//	$('.compose-tickle-body table tbody').html(response.html);
		        	//}
		        }
			});
	});
	
	$(document).on('click','.email-btn.toggle_timeline_box',function(e){
      var timeline_box = $(this).closest('li').find('.timeline-box');  
          timeline_box.slideToggle();
          $(this).toggleClass('open');
    });
	
	$(document).on('click' , '#tnplusss', function(e){
		var _this = $(this);
		var MailID =  _this.data().id;
	
		var current_status =  _this.attr('data-status');
		var text  =  (current_status ==  'Y')?'Pause':'UnPause';
		var text2  =  (current_status ==  'Y')?'UnPause':'Pause';
		var Approve =  current_status;
			current_status = (current_status ==  'Y')?'N':'Y';

		var content = "This will allow your email to be send at it's scheduled time.";
		var btn_name =  "Yes, Unpause it!";
		if(text2 != 'UnPause' ){
			content = "This will Pause your campaign and your email will not send at it's scheduled time.";
			btn_name =  "Yes, Pause it!";
		}
		 
		$.confirm({
			title: 'Are you sure?',
			content: content, 
			type: 'red',
			//icon: 'fa fa-warning',
			animation: 'zoom',
			buttons: {
				confirm: {
					text: btn_name,
					btnClass: 'btn-red',
					action: function(){
						$('#getdeliverstatus').val(current_status);	
						if(text2 != 'UnPause' ){
							 var text1 = '<span class="pusebtntext"><a href="javascript:void;" id="tnplusss" style="cursor: pointer;text-decoration: underline !important;" class="tt-btn1 tt-pause-btn-new"  data-status="Y"> Paused <img src="https://client.tickletrain.com/images/Extension/play.svg"  width="20" /> </a></span>';
							$('.pusebtntext').html(text1);
						}else{
							var text1 = '<span class="pusebtntext"><a href="javascript:void;" id="tnplusss" style="cursor: pointer;text-decoration: underline !important;"  class="tt-btn1 tt-pause-btn-new"  data-status="N"> Unpaused <img src="https://client.tickletrain.com/images/Extension/pause.svg"  width="20" /> </a></span>';
							$('.pusebtntext').html(text1);
						}
						
					}
					
				},
				Cancel: function () {
				}
			}
		});
	});
	
	

	// tt-pause-unpase-campaign
	$(document).on('click' , '.tt-pause-btn', function(e){
		e.preventDefault();
		var _this = $(this);
		var MailID =  _this.data().id;
	
		var current_status =  _this.attr('data-status');
		var text  =  (current_status ==  'Y')?'Pause':'UnPause';
		var text2  =  (current_status ==  'Y')?'UnPause':'Pause';
		var Approve =  current_status;
			current_status = (current_status ==  'Y')?'N':'Y';

		var content = "This will allow your email to be send at it's scheduled time.";
		var btn_name =  "Yes, Unpause it!";
		if(text2 != 'UnPause' ){
			content = "This will Pause your campaign and your email will not send at it's scheduled time.";
			btn_name =  "Yes, Pause it!";
		}
		 
		$.confirm({
			title: 'Are you sure?',
			content: content, 
			type: 'red',
			//icon: 'fa fa-warning',
			animation: 'zoom',
			buttons: {
				confirm: {
					text: btn_name,
					btnClass: 'btn-red',
					action: function(){
						var postData = {'method':'updateCampaign' , 'MailID':MailID , 'user_id' :  TickleUser.UserID , 'values' : [current_status,Approve] , 'fileds' : ['Pause','Approve'] };
						$.post('https://client.tickletrain.com/api/api.php',postData , function(response,status) {
							//if(status == "success"){
								getTicklesJosn(TickleUser.UserID,'json',MailID);
								
								if($('#tt-upc-c').length > 0  && $('#tt-upc-c').css('display') != 'none'){
									getDashboard();
								}
								$('.tt-thread-lable').trigger('click');
							
							// _this.attr('data-status', current_status); 
							// _this.text(text);
							// console.log(response);
							//}
						});
					}
				},
				Cancel: function () {
				}
			}
		});
	});

	// tt-pause-unpase-single task
	$(document).on('click' , '.pause-btn', function(e){
		e.preventDefault();
		var _this = $(this);
		var TaskID =  _this.data().id;
	
		var current_status =  _this.attr('data-status');
		var text  =  (current_status ==  'Y')?'Pause':'UnPause';
		var text2  =  (current_status ==  'Y')?'UnPause':'Pause';
		var Approve =  current_status;
			current_status = (current_status ==  'Y')?'N':'Y';

		var content = "This will allow your email to be sent at it's scheduled time.";
		var btn_name =  "Yes, Unpause it!";
		if(text2 != 'UnPause' ){
			content = "This will Pause your campaign and your email will not send at it's scheduled time.";
			btn_name =  "Yes, Pause it!";
		}
			 
		$.confirm({
			title: 'Are you sure?',
			content: content, 
			type: 'red',
			//icon: 'fa fa-warning',
			animation: 'zoom',
			buttons: {
				confirm: {
					text: btn_name,
					btnClass: 'btn-red',
					action: function(){
						var postData = {'method':'updateCampaign' , 'TaskID':TaskID , 'user_id' :  TickleUser.UserID , 'values' : [current_status,Approve] , 'fileds' : ['Pause','Approve'] };
						$.post('https://client.tickletrain.com/api/api.php',postData , function(response,status) {
							getDashboard();
						});
					}
				},
				Cancel: function () {
				}
			}
		});
	});

	$(document).on('change' , '.up-tickles', function(e){
		e.preventDefault();
		getDashboard();
	});

	$(document).on('click' , '.top-flders span', function(e){
		e.preventDefault();
		$('.top-flders span').removeClass('active');
		$(this).addClass('active');
		getDashboard();
	});

	$(document).on('click' , '.parent_li .arrow-img', function(e){
		e.preventDefault();
		var id =$(this).data('mailid');
				$(this).toggleClass('open expend');
				$(this).parent().find('.del-cm-cl').toggleClass('del-btn-all').toggleClass('del-btn');
				$('.child_li_'+id).toggleClass('expend');
	});

	// view open email
	$(document).on('click','.tt-view-email',function(){  
	    var trackID = $(this).data().id;
	    var data = { 'method':'view_open_email', 'trackID':trackID , 'user_id' : TickleUser.UserID } ;
	        $.post('https://client.tickletrain.com/api/api.php',data, function(response,status) {
	      
	          if(response !== ""){
	            $.dialog({
	                title: 'View Email',
	                content: response,
	                columnClass: 'col-md-8',
	            });
	          }
	        });
	  });

	// view reply email
	$(document).on('click','.tt-view-reply-email',function(){  
	    var trackID = $(this).data().id;
	    var data = { 'method':'viewReplied', 'trackID':trackID};
	        $.post('https://client.tickletrain.com/api/api.php',data, function(response,status) {
	      
	          if(response !== ""){
	            $.dialog({
	                title: 'Replied Message',
	                content: response,
	                columnClass: 'col-md-8',
	            });
	          }
	        });
	  });

    $(document).on('click','.schedule-time',function(e){
    	e.preventDefault();
		$(this).closest('li').find('.time-holder').fadeToggle('2000');
    	$(this).closest('li').find('.time-holder').toggleClass('r-0');	
    });

	$(document).on('click','.time-holder span',function(e){
    	e.preventDefault();
    	var TaskID = $(this).parent().data().taskid;
    	var MailID = $(this).parent().data().mailid;
    	var weekend = $(this).parent().data().weekend;
    	
    	var text = $(this).text().trim();
	    var data = { 'method':'updateSchedule', 'TaskID':TaskID , 'MailID' : MailID , 'd' : text , 'weekend' : weekend };
	        $.post('https://client.tickletrain.com/api/api.php',data, function(response,status) {
	            getDashboard();
	        });
    });
    
    $(document).on('click','.orignal-email-btn',function(){
    	var that = $(this);
	    var MailID = $(this).closest('li').data().mailid;

	    $(this).closest('li').find('.coment-count').html('');

	    var data = { 'method':'viewOrignalEmail', 'MailID':MailID };
	        $.post('https://client.tickletrain.com/api/api.php',data, function(response,status) {
	         var Obj =  JSON.parse(response);
	          if(response !== ""){
	          	var comments = '';
	          	var add_comment_link = "";
	          	if(Obj.comments.length > 0 ){
	          		    comments = '<hr><ul class="comment-list">';
	          		$.each(Obj.comments, function(inx,val) {
						var necmnt = val.comment;
						 necmnt = (necmnt.replace('/upload-files/', 'https://client.tickletrain.com/upload-files/'));
						 necmnt = (necmnt.replace('https://client.tickletrain.comhttps://client.tickletrain.com/upload-files/', 'https://client.tickletrain.com/upload-files/'));
						//console.log(necmnt);
						//console.log('ss'+val.comment);
	          			comments+='<li>';
                		//comments+='<span class="close" data-id="'+val.id+'">&#215;</span>';
                		comments+='<h6>'+get_comment_user(val)+'<span class="float-right pr-2 small">'+val.created_at+' at '+val.created_time+'</span></h6>';
                	    comments+='<p class="small">'+necmnt+'</p>';
                		comments+='</li>';	
	          		});
	          		comments+='</ul>';
	          	}

	          	if(Obj.add_comment !== '' && Obj.add_comment.length > 0 ){
	          		add_comment_link =  '<a href="'+Obj.add_comment+'" class="float-right addComment"> add comments</a>';
	          	}

	            $.dialog({
	                title: Obj.title+add_comment_link,
	                content: Obj.body_content+comments,
	                columnClass: 'col-md-8',
	            });
	     
	            if(typeof that.data().scroll != 'undefined'){
		            setTimeout(function(){
				            	$(".jconfirm-content-pane").animate({
									    scrollTop:  10000
								}, 400)
				            },1000);
				    }
	            }
	        });
	  });


    $(document).on('click','.eye-btn',function(){
    	var li;
    	li = $(this).closest('li');

	    var TaskID = li.data().id;
	    var MailID = li.data().mailid;
	    var weekend = li.data().weekend;


	    var data = { 'method':'viewAllEmailPreview', 'TaskID':TaskID , 'MailID':MailID };
	    var initialSlide = 0 ;
	    var minDate = "";
	    $.confirm({
	    	columnClass: 'col-md-6 preview-modal',
		    content: function () {
		        var self = this;
		        return $.ajax({
		            url: 'https://client.tickletrain.com/api/api.php',
		            dataType: 'json',
		            method: 'POST',
		            data : data, 
		        }).done(function (response) {
		             self.setContent('' + response.data);
		             initialSlide = response.initialSlide;
		             CurrentSlide =  response.initialSlide;
		             Preview_tickle_dates = response.dates;
		  
		             minDate = (response.minDate != "")? new Date(response.minDate) : new Date();
		            // self.setContentAppend('<br>Version: ' + response.version);
		             self.setTitle("Preview");
		        }).fail(function(){
		            self.setTitle("Error");
		            self.setContent('Something went wrong.');
		        });
		    },
		    buttons: {
			        Cancel: function(helloButton){
			        },
			        Submit: {
			            text: 'Submit', 
			            btnClass: 'btn-blue',
			            action: function(heyThereButton){
			            	//console.log(CurrentSlide);
			            	//if($('#tt-date').css('display') != 'none'){
			            	
			            		var TaskID = Preview_tickle_dates[CurrentSlide].task_id;
			            		
				            	var date  = $('#tt-date').val();
				            	var f_name  = $('#f_name').val();
				            	var l_name  = $('#l_name').val();
								//var delstatus = $('#getdeliverstatus').val();
				            	var data = { 'method':'updateSchedule', 'TaskID':TaskID , 'date' : date , 'f_name' : f_name , 'l_name' : l_name , 'weekend' : weekend };
						        $.post('https://client.tickletrain.com/api/api.php',data, function(response,status) {
						            getDashboard();
						        });
							//}
			            },
			    	}
		    },
	        onContentReady: function () {

	        

	        	datepickler =  $('#tt-date').datetimepicker({ dateFormat:'mm-dd-yy', timeFormat:'hh:mm TT', ampm:true , "minDate" : minDate });

	        	$('.slider-preview').slick({
	        		initialSlide : initialSlide, 
	        		dots : true,
	        		//dotsClass:  "",
	        		appendDots: $('.stages'),
	        		dotsClass: 'custom-dots', //slick generates this <ul.custom-dots> within the appendDots container
		          	customPaging: function (slider, i) {
		             	var slideNumber = (i + 1),
		                    totalSlides = slider.slideCount;
		                return '<span title="' + slideNumber + ' of ' + totalSlides + '">' + slideNumber + '</span>';
		          	},
	        		prevArrow: $('.prev'),
					nextArrow: $('.next'),
	        	});
       		}
		});
	});

	$(document).on('afterChange', '.slider-preview' , function(event,slick, currentSlide){
		CurrentSlide = currentSlide;
	    $('.stage-txt').text('Stage '+(currentSlide+1) + ' of ' +slick.slideCount);
	    var curr =  (CurrentSlide == 0) ? new Date() : new Date((Preview_tickle_dates[currentSlide-1].date2));
	    datepickler.datepicker( "destroy" );
	    datepickler =  $('#tt-date').datetimepicker({ dateFormat:'mm-dd-yy', timeFormat:'hh:mm TT', ampm:true , minDate : curr });
	    $('#s_date').html(Preview_tickle_dates[currentSlide].TaskInitiateDate);
	    $('#tt-date').val(Preview_tickle_dates[currentSlide].date);
	});

	$(document).on('click','#date_edit_icon',function(){
		$('#s_date').toggle();
		$('#tt-date').toggle();
		$(this).hide();
		$('#check-mark').toggle();
	});

	$(document).on('click','#check-mark',function(){
		$('#s_date').toggle();
		$('#tt-date').toggle();
		$('#date_edit_icon').toggle();
		$(this).hide();
	});

	$(document).on('click','.search_input',function(){
		$('#seach_input_holder').slideToggle();
		$('#seach_input_holder').find('input[name="search"]').focus();
	});

    $(document).on('click','.open-site',function(e){
    	e.preventDefault();
		window.open("https://client.tickletrain.com/get_fb_info/extensionapi.php?tickleid="+TickleUser.UserID);
	});

    // search on dashboard page
	$(document).on('keypress','#seach_input_holder input[name="search"]',function(event){
		var keycode = (event.keyCode ? event.keyCode : event.which);
		if(keycode == '13'){
			var str = $(this).val();
			//if(str.trim() != ""){
				getDashboard();
			//}
		}
	});

	// Clear dashboad searchbox text and result;
	$(document).on('click','#clear_search',function(event){
		event.preventDefault();
		if($('#seach_input_holder input[name="search"]').val() != ""){
			$('#seach_input_holder input[name="search"]').val('');
			getDashboard();
		}
	});

	// load more data in dashboard 
	$(document).on('click','.load_more',function(event){
		event.preventDefault();
		getDashboard(true);
	});

	$(document).on('click','.change-shchedule',function(event){
		event.preventDefault();
		$('.tickle-stage').slideToggle();
	});

	// reset shdule 
	$(document).on('click','.reset-shchedule',function(event){
		event.preventDefault();
		$('.time-msg').text($(this).data().txt);
		$( "#st-date" ).val('');
			$(this).hide();
	});

	$(document).on('click','.tickle-stage span',function(event){
		event.preventDefault();
		var text = $(this).text();
		var arr = { 
			'N':'Now',
			'1H':'After 1 hour from now',
			'2H':'After 2 hour from now',
			'3H':'After 3 hour from now',
			'1D':'After 1 Day at 12:00 pm',
			'2D':'After 2 Day at 12:00 pm',
			'3D':'After 3 Day at 12:00 pm',
			'1W':'After 1 week at 12:00 pm',
			'2W':'After 2 week at 12:00 pm',
			'1M':'After 1 month at 12:00 pm'
		};
		$('.time-msg').text(arr[text]);
		$( "#st-date" ).val(text);
		$('.reset-shchedule').show();
	});

	
	$(document).on('click','.add-new-comnt-btn',function(event){

		var $taskid =  $(this).closest('li').data().id;
		var a= $('#tt-comnt-area'+$taskid).val();

		$('iframe.tt-cmnt-frm').hide();
		console.log('hii');
		
		var media_url =  logo.replace("dashboard_logo.png", "");
        var css_path ="https://client.tickletrain.com/api/extension/css/rte.css";
		//initDroppable($('#tt-comnt-area'+$taskid));
		var data_to_send ={
			TaskID : $taskid,
			TickleID:TickleUser.UserID  
		};
		
		$('#tt-comnt-area'+$taskid).rte(css_path, media_url,data_to_send);

	 });


	$(document).on('click','.brC-dA-I-Jw.brC-aMv-auO',function(){
		if($('.nH.bAw.nn').width() != 0){
			$('.sales-sidebar-container').css({ right:'5px' });
		}else{
			$('.sales-sidebar-container').css({ right:'56px' });
		}
	});


})(jQuery);

var pix = 0;


    //initDroppable($("#TextArea1"));
	// function initDroppable($elements) {
	//   $elements.droppable({
	//       hoverClass: "textarea",
	//       accept: ":not(.ui-sortable-helper)",
	//       drop: function(event, ui) {
	//           var $this = $(this);
	//           var tempid = ui.draggable.text();
	//           var dropText;
	//           dropText = " {" + tempid + "} ";
	//           //var droparea = document.getElementById('TextArea1');
	//           var droparea = $elements;
	//           var range1 = droparea.selectionStart;
	//           var range2 = droparea.selectionEnd;
	//           var val = droparea.value;
	//           var str1 = val.substring(0, range1);
	//           var str3 = val.substring(range1, val.length);
	//           droparea.value = str1 + dropText + str3;
	//       }
	//   });
	// }


	function display_index(User){
		
	    var avtar_ = avtar_view(User);
	    var pages_list = '';
		var html =  '<div class="sales-sidebar-container tickle">'+
						showLoader()+
						'<div class="tickle sidebar-container">'+
						'<div class="index-view-container">'+
							'<div id="tt-upc-c"></div>'+
						'</div>'+
						'</div>'+
					'</div>';
		if(!$('body .tickle').length > 0){
		    setTimeout(function(){
		    	if(!$('body .tickle').length > 0){
		    	 $('body').addClass('tickle-sidebar-visible');
		    	 $('.AO').after(html);
		    	 	$('.loader-wrapper').fadeOut();
		   		}
		   		getDashboard();

			    // $( ".AO" ).resizable({
			    // 	  alsoResize: ".sales-sidebar-container"
			    // });

			    $( ".AO" ).resizable({
				 	resize: function( event, ui ) {
				  		pix =  ui.originalSize.width-ui.size.width;
				    },
					stop: function( event, ui ) {
						if(pix){
				  	    	orinal_width = $('.sales-sidebar-container').width()+pix;
				  	    	$('.sales-sidebar-container').width(orinal_width+'px');
				  	    }
					}
				});

		   		// $( ".sales-sidebar-container" ).resizable();

		    },1000);
		}else{
			//console.log('body has tickle');
		}
	}

	function avtar_view(User){

		if(User.ShortName == '' || typeof User.ShortName == 'undefined' ) {
			var UDimage = '<img alt="" class="private-image img-circle private-image--circle contact-avatar-image m-bottom-2 img-responsive private-image--responsive" src="'+defaul_user+'"></img>';
		}else{
			var UDimage = '<span class="rounded-circle" >'+User.ShortName +'</span>';
		}

		return  '<div class="contact-avatar mt-3">'+UDimage+
				    '<div><span class="private-truncated-string contact-avatar-name m-bottom-2"><span class="private-truncated-string__inner"><span>'+User.FirstName+' '+User.LastName+'</span></span>'+
				        '</span><span class="private-truncated-string contact-avatar-email"><span class="private-truncated-string__inner"><span>'+User.EmailID+'</span></span>'+
				        '</span>'+
				        '<div class="text-center">'+
				    		'<small class="private-microcopy"><i18n-string data-locale-at-render="en-us">Account ID: </i18n-string>'+User.UserID+'</small>'+
						'</div>'+
				        '<hr>'+
				        '<div class="contact-avatar-alerts"></div>'+
				    '</div>'+
				'</div>';
	}

	function render_pages($page,App_Url){
		return (
			  '<a class="navbar-brand" href="#" id="'+$page.id+'">'+
			     $page.title+
			    '<span class="float-right">'+
			    '<img src="'+right_arrow+'" width="30" height="30" class="d-inline-block align-top" alt="">'+
			    '</span>'+
			  '</a>'
			);
	}

	function set_header(title,App_Url,action){
			return ('<div class="index-view-header nav-bar">'+
						'<span><a href="#" class="open-site" >'+
							'<img alt="img" src="'+App_Url+'/images/Extension/dashboard_logo.png">'+
						'</a></span>'+
						'<span class="private-truncated-string__inner">'+
							'<h6 class="font-weight-bold text-right">'+title+'</h6>'+
						'</span>'+
					'</div>');
	}

	function add_search(){
		return ('<div class="text-center">'+
			'<div class="md-form mt-0 p-2"><input class="form-control filter_contact" type="text" placeholder="Search" aria-label="Search"></div>'+
		'</div>');
	}

	function contact_list(arr){
		var html = '';
		var FirstName = null ;
		var LastName = null ;
		if( Array.isArray(arr) && arr.length > 0){
			for (var i = 0;  i < arr.length;  i++) {

				var name = '<img alt="" class="private-image img-circle private-image--circle img-responsive private-image--responsive" src="'+default_contect_image+'" style="height: 36px; width: 36px;">'; 
					FirstName = ((arr[i].FirstName == '' ||  arr[i].FirstName == 'null' ||  arr[i].FirstName == null ) ?  'null' : arr[i].FirstName);
					LastName =  ((arr[i].LastName == ''  ||  arr[i].LastName == 'null'  ||  arr[i].LastName == null )  ?  'null' :  arr[i].LastName ) ;
				if(arr[i].FirstName != "" &&  arr[i].FirstName != "null" &&  arr[i].FirstName != null  ){
					
					var name =  arr[i].FirstName.charAt(0).toUpperCase();
					if(arr[i].LastName != "" &&  arr[i].LastName != "null" &&  arr[i].LastName != null){
						name+= arr[i].LastName.charAt(0).toUpperCase();

					}

					name = '<span class="rounded-circle" >'+name +'</span>';
				}

				html+='<button class="uiButton contact-button  private-button--unstyled mt-2">'+
					    	'<div class="media private-media m-y-2 m-x-2">'+
					        '<div class="private-media__item private-media__item--left">'+name+'</div>'+
					        '<div class="media-body private-media__body"><a class="private-link contact-name" role="button" title="'+FirstName+' '+ LastName+'" ><span class="private-truncated-string" ><span class="private-truncated-string__inner">'+FirstName+' '+ LastName+'</span>   <img src="'+TickleUser.App_Url+'/images/Extension/edit.svg" class="float-right" id="'+arr[i].ContactID+'" > </span> </a><small class="private-microcopy is--text--help"><span class="private-truncated-string"><span class="private-truncated-string__inner"><span>'+arr[i].EmailID+'</span></span></span></small></div>'+
					    '</div>'+
					'</button>';
			}
		}else{
			html = '<p> No contact Found<p>';
		}
		$('.contact_list').html(html);
	}

	function get_comment_user($comment){

	    if($comment.comment_by == 'owner'){
	        return $comment.FirstName+' '+$comment.LastName;
	    }else{
	        if(($comment.CFN.length > 0)  || ($comment.CLN.length > 0)  ){
	             return ($comment.CFN+' '+$comment.CLN);
	        }else{
	             return '['+($comment.CEID)+']';
	        }
	    }
	}

    function getTickleFollow(tickle_id,tickle_name,user_id,UserName) {

    	var loader = '<tr><td><img class="table-loader" src="https://client.tickletrain.com/images/Extension/loader.gif"></td></tr>';
					$('.compose-tickle-body table tbody').html(loader);

    	    var data =  {'method': 'getTickleFollow' , 'user_id' : user_id , 'tickle_id' : tickle_id ,'tickle_name' : tickle_name ,'UserName' : UserName } ;
			$.ajax({
				url: 'https://client.tickletrain.com/api/api.php',
		        data: data,
		        type: "POST",
		        dataType: "json",
		        success: function (response) {
		        	if(response.status){
		        		$('.compose-tickle-body table tbody').html(response.html);
		        	}else{
		        		$('.compose-tickle-body table tbody').html(response.html);
		        	}
		        }
			});
    }

    function reviewEmail(self,tickle_id,tickle_type){
  
	  return $.ajax({
	                url: 'https://client.tickletrain.com/api/api.php?method=reviewEmail&tickle_id='+tickle_id+'&tickle_type='+tickle_type,
	                dataType: 'json',
	                method: 'get'
	            }).done(function (response) {
	                if(response.status){
	                  self.setContent(response.html);
	                  self.setTitle('View Tickles');
	                }
	            }).fail(function(){
	            	self.setTitle('internal Server Error');
	                self.setContent('Something went wrong.');
	            });
	}

	function getDashboard(load_more = null){
		
		var date  =  $('.top-flders span.active').data('tag');
		var tickle_id =  $('.up-tickles').find("option:selected").val();
		var str =  $('#seach_input_holder input[name="search"]').val();
		var page = null ;
		var data =  { 'method':'getDashboard' , 'user_id':TickleUser.UserID , "TimeZone":TickleUser.TimeZone, 'date' : date , 'tickle_id':tickle_id , 'str':str };

		if(load_more == true){
			data.page = $('.load_more').data().page;
			data.load_more = true;
		}

		$('.loader-wrapper').show();
		$.ajax({
			url: API_URL,
			data: data,
			type: "POST",
			dataType: "json",
			success: function (response) {
				//return false;
				if(load_more == null){
					var html='<div class="index-view-header nav-bar">'+
							'<span><a href="#" class="open-site" >'+
								'<img alt="img" class="logo" src="'+logo+'">'+
							'</a></span>'+
							'<span class="private-truncated-string__inner mt-4">'+
								'<h6 class="font-weight-bold text-right mb-0">Dashboard</h6>'+
							'</span>'+
						'</div>'
					// html+= '<div class="top-flders"><span data-tag="today" class="'+today+'" >Today</span><span data-tag="tomorrow" class="'+tomorrow+'" > Tomorrow </span> <span class="'+week+'" data-tag="week">This Week</span> <span class="'+anytime+'" data-tag="anytime" >Anytime</span></div>';
					 	html+=response.html;
					$('.index-view-container #tickle-root-view').hide();
					$('.index-view-container #tt-upc-c').html(html).show();
					
				}else{
					$('.uct-ul').append(response.html);
					
					if(response.has_more_data == true || response.has_more_data == 'true' ){
						$('.load_more').data('page',response.page);
					}else{
						$('.load_more').remove();
					}

					var f = $("#progress-bar").width() / $('#progress-bar').parent().width() * 100;
					var presntage =  (f+response.percentage);
				
					$("#progress-bar").css('width',presntage+'%');

				}
				var height = (($('.Tm.aeJ').height()*80)/100);
							 $('.scrl.scroll2').height(height+"px");
				$('.loader-wrapper').fadeOut();

				wd =  $('.nH.bAw.nn').width();
				if(wd == 0){
					$('.sales-sidebar-container').css({ right:'5px'});
				}
				//setTimeout(function(){ autocomplete(document.getElementById("search_input"), autocompleteList);	},5000);
	  			setTimeout(function(){  autocomplete(document.querySelectorAll("#search_input")[0],autocompleteList); },3000);

			}
		});
	}

    function showLoader() {
    	// body...
    	var html = '<div class="loader-wrapper"><img src="https://client.tickletrain.com/images/Extension/loader.gif" alt="loader image"></div>';
    	return html ; 
    }



