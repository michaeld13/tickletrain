<?php 
 $can_edit = false;
 $images = [];

if((isset($_SESSION['TickleID']) &&  $_SESSION['TickleID'] == $TickleID) ||  $is_owner == 'yes' ){
    $can_edit = true;
    $is_owner = 'yes';
}
if(!empty($attachments)){
    $images = explode(',', $attachments);
}


$filerror = $_GET['filerror']; 
if(isset($filerror)){ ?>
	<script type="text/javascript">
	
	function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts= url.split('?');   
    if (urlparts.length>=2) {

        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {    
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                pars.splice(i, 1);
            }
        }

        url= urlparts[0]+'?'+pars.join('&');
        return url;
    } else {
        return url;
    }
}
			
		  alert("We're sorry, your files are over the maximum file size allowed.");
		  var url = "https://client.tickletrain.com"+"<?=$_SERVER['REQUEST_URI'];?>"; 
			// url = url.split('&')[1] ;
			var newurl = removeURLParameter(url, 'filerror');
			window.location.href= newurl;
		  </script>
		  <?php
		 //header("location:https://client.tickletrain.com".$_SERVER['REQUEST_URI']);
	//exit;
	
}

function convertToReadableSize($size){
  $base = log($size) / log(1024);
  $suffix = array("", "KB", "MB", "GB", "TB");
  $f_base = floor($base);
  return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
}

function convertToReadableSizeNEW($size){
  $base = log($size) / log(1024);
  $suffix = array("", "KB", "MB", "GB", "TB");
  $f_base = floor($base);
  return round(pow(1024, $base - floor($base)), 1);
}

function isa_convert_bytes_to_specified($bytes, $to, $decimal_places = 1) {
    $formulas = array(
        'K' => number_format($bytes / 1024, $decimal_places),
        'M' => number_format($bytes / 1048576, $decimal_places),
        'G' => number_format($bytes / 1073741824, $decimal_places)
    );
    return isset($formulas[$to]) ? $formulas[$to] : 0;
}

?>

<link rel="stylesheet" href="/<?= ROOT_FOLDER ?>css/viewer.css" crossorigin="anonymous">
<style type="text/css">
.row { margin-top: 15px;}
.row label {font-size: 15px; font-weight: bold;}
#TickleMailContentNe{width: 100%;	}
.cke_editable {
    min-height: 160px;
	border: 1px solid #ccc;
}
input[type=submit]{
    font-size: 13px;
    background: #1686b7;
    color: #fff;
    font-weight: bold;
    padding: 6px;
    border: 1px solid #1686b7;
    cursor: pointer;
}
.form-input-textarea{width: 90%;margin: 13px 0px; font-size: 16px; padding: 10px;}
.comment-list {
    list-style: decimal-leading-zero;
    width: 100%;
    max-width: 90%;
    color: #09abf5db;
    font-weight: bold;
    font-size: 14px;
}
.comment-list li h3 span{
		float: right;
	    font-size: 16px;
	    font-weight: 400;
	    color: #959595;
}
ul.comment-list li {
    border: 1px solid #ccc;
    margin-bottom: 25px;
    position: relative;
}
ul.comment-list h3 {
    font-size: 16px;
    padding: 2px 10px 0px 10px;
    color: #09abf5db;
    font-weight: 800;
}
ul.comment-list p {
    padding: 2px 10px 0px 10px;
    color: #4e4c4c;
}
ul.comment-list li span.close{
    width: 23px;
    height: 23px;
    position: absolute;
    top: -13px;
    right: -12px;
    font-size: 23px;
    border: 1px solid #fff;
    border-radius: 100px;
    color: #fff;
    text-align: center;
    line-height: 23px;
    background: #bc4f4a;
    opacity: 1;
}
#drop_zone {
    border: 2px dotted;
    width: 100%;
    height: 100px;
    display: flex;
    justify-content: center;
    align-items: center;

}
#drop_zone label{
    display: inline-block;
    background-color: #f5efef;
    padding: 10px;
}
div#drop_zone.active {
    border: 5px dotted #3a94c5;
    background: beige;
}

div#drop_zone.active label {
    background-color: #3a93c5;
    color: #fff;
}

#new_images{
    display: none; 
}
#thumbnail-ul{
    list-style: none;
}
#thumbnail-ul li {
    display: inline-block;
    position: relative;
}
#thumbnail-ul li img {
    border: 1px solid #e0e0e0 !important;
}

#thumbnail-ul li span.close{
    width: 20px;
    height: 20px;
    position: absolute;
    top: -5px;
    font-size: 20px;
    border: 1px solid #fff;
    border-radius: 100px;
    color: #fff;
    text-align: center;
    line-height: 20px;
    background: #bc4f4a;
    opacity: 1;
}
/** 5--3-2020 **/
@media(max-width:767px){

	body {
		min-width: 100%;
	}
	.main_holder {
		width: 100%;
	}
	iframe.cke_wysiwyg_frame.cke_reset {
		width: 100% !important;
	}
	.header_holder, .nav_holder {
		width: 100%;
	}	
	#main {
		padding: 35px 20px 20px;
	}
	.header_holder strong.logo{
		padding: 0 20px ;
	}
	ul#thumbnail-ul {
		padding: 0;
	}
	.margin-0{
		margin:0 !important;
	}
	input[type=submit] {
		width: 250px;
		margin: 20px auto 30px;
		display: block;
		padding: 10px
	}
	#main h1 {
    font-size: 18px;
    padding: 0;
}
.input_text input {
    height: 30px;
    line-height: 30px;
    width: 320px !important;
    margin: 10px 0 0;
}
.comment-list {
    list-style: none;
    padding: 0;
	max-width: 100%;
}
.comment-list li h3 span {
    font-size: 12px;
    width: 100%;
    margin: 5px 0 0;
}
}
@media(max-width:480px){
	ul#thumbnail-ul {
		text-align: center;
	}

span.input_text {
    width: 100% !important;
}
}

#thumbnail-ul li img{
	 float:left;
}

span.img_nme_lst {
    float: left;
    clear: both;
    width: 92px;
    word-break: break-all;
    height: 22px;
    font-size: 12px;
    text-align: center;
}



</style>

<!--script src="https://ckeditor.com/assets/libs/ckeditor4/4.14.0/ckeditor.js"></script-->
<script src="https://cdn.ckeditor.com/4.8.0/full-all/ckeditor.js"></script>
<!--script type="text/javascript" src="<?php echo 'https://'.$_SERVER['SERVER_NAME'];?>/ckeditorn/ckeditor_4_11.js"></script-->
<div class="main_holder edit_page" >

	<div class="heading">
        <h1>Add Comments </h1>
    </div>

    <form action="" method="post"  enctype="multipart/form-data">

    	<input type="hidden" name="MailID" value="<?= $MailID ?>">
        <input type="hidden" name="TickleID" value="<?= $TickleID ?>">
    	<input type="hidden" name="is_owner" value="<?= $is_owner ?>">
        <input type="hidden" name="TickleTrainID" value="<?= $TickleTrainID ?>">
        <input type="hidden" name="attachments" value="<?= $attachments ?>">
        <input type="hidden" id="deleted_attachments" name="deleted_attachments" value="">
    	<input type="hidden" name="RawPath" value="<?= $RawPath ?>">

    	<fieldset>

		        <div class="row">
	                <label for="Custom Subject">Subject <span class="req">*</span> <small style="font-weight: 100; float: right;" >Orignal Subject : "<?=  $OrignalSubject; ?>"</small></label>
	                <div style="clear:both;"></div>
	                <span class="input_text" style="width:360px;background-position:0 -44px;">
	                	<input name="CustomSubject" id="CustomSubject" style="width:327px;"  value="<?=  $Subject; ?>" required <?php if($is_owner != 'yes') { ?>readonly="readonly"<?php } ?>/>
	                </span>
	            </div>

	            <div style="clear:both;"></div>

                <div class="row">
                    <label for="TickleMailContent">Email Message <span class="req">*</span></label>
                    <div style="clear: both;"></div>
                    <div class="plugin_holder">
                        <textarea name="TickleMailContent" id="TickleMailContentNe" rows="30"  class="tinymce" required  <?php /*if($can_edit) { ?>readonly="readonly"<?php } */?>><?= $MessageHtml ?></textarea>
                    </div>
                </div>
                  

                <div class="row" > 
                    <div id="drop_zone" ondrop="dropHandler(event);" >
                      <label for="new_images">Upload or Drag File</label>
                      <input type="file" name="new_images[]" id="new_images" onchange="dropHandler(event)" multiple>
                    </div>
                </div>

				
				
				
                <ul id="thumbnail-ul" class="docs-pictures@@@">
                    <?php 
					 $totalsize = 0;
					foreach ($images as $key => $image): //echo '<pre>';print_r($image);
					$file_ext=strtolower(end(explode('.',$image)));
					$extensions= array("jpeg","jpg","png","gif");
					?>
                    	<?php
						//echo '<li> <img style="height: 75px; border: 1px solid #000; margin: 5px" src="'.$RawPath.$image.'" /></li>';
					$filenamee = '/var/www/vhosts/client.tickletrain.com/httpdocs/'.$RawPath.$image;
					 $sizesss = filesize($filenamee);
					$totalsize = $totalsize + $sizesss;
					if(in_array($file_ext, $extensions)){
		        		echo '<li>  <span class="close">&#215;</span><a target="_blank" href="'.$RawPath.$image.'"><img style="height: 75px; border: 1px solid #000; margin: 5px" alt="'.$RawPath.$image.'" src="'.$RawPath.$image.'" /></a><span class="img_nme_lst">'.$image.'</span></li>';
		        	}elseif($file_ext == 'pdf'){
						echo "<li><span class='close'>&#215;</span><a target='_blank' href='".$RawPath.$image."'><img style='height: 75px; border: 1px solid #000; margin: 5px' alt='".$RawPath.$image."' src='https://client.tickletrain.com/images/Extension/icon-pdf.png' /></a><span class='img_nme_lst'>".$image."</span></li>";
		        	}elseif(($file_ext == 'doc') || ($file_ext == 'docx')){
						echo "<li><span class='close'>&#215;</span><a  href='".$RawPath.$image."'><img style='height: 75px; border: 1px solid #000; margin: 5px' alt='".$RawPath.$image."' src='https://client.tickletrain.com/images/Extension/icon-doc.png' /></a><span class='img_nme_lst'>".$image."</span></li>";
		        	}elseif($file_ext == 'txt'){
						echo "<li><span class='close'>&#215;</span><a target='_blank' href='".$RawPath.$image."'><img style='height: 75px; border: 1px solid #000; margin: 5px' alt='".$RawPath.$image."' src='https://client.tickletrain.com/images/Extension/icon-txt.png' /></a><span class='img_nme_lst'>".$image."</span></li>";
		        	}elseif($file_ext == 'psd'){
					   $RawmaildirPSD = "/var/www/vhosts/client.tickletrain.com/httpdocs".$RawPath;
						//echo "<li><span class='close'>&#215;</span><a target='_blank' href='".$RawPath.$image."'><img style='height: 75px; border: 1px solid #000; margin: 5px' alt='".$RawPath.$image."' src='https://client.tickletrain.com/images/Extension/icon-psd.png' /></a><span class='img_nme_lst'>".$image."</span></li>";
		        	?>
					<li><span class='close'>&#215;</span><a target='_blank' href="https://client.tickletrain.com/psddownload.php?filePath=<?php echo urlencode($RawmaildirPSD);?>&filePSD=<?php echo urlencode($image);?>"><img style='height: 75px; border: 1px solid #000; margin: 5px' alt=<?php echo $RawPath.$image;?> src='https://client.tickletrain.com/images/Extension/icon-psd.png' /></a><span class='img_nme_lst'><?php echo $image;?></span></li>
					<?php }else{
		        		echo "<li><span class='close'>&#215;</span><a target='_blank' href='".$RawPath.$image."'><a href='https://client.tickletrain.com".$RawPath.$image."'><img style='height: 75px; border: 1px solid #000; margin: 5px' src='https://client.tickletrain.com/images/Extension/icon-empty.png' /></a><span class='img_nme_lst'>".$image."</span></li>";
		        	}?>
                        
                   
                    <?php endforeach; ?>
                </ul>
				
				<?php
				 $widthsize = convertToReadableSizeNEW($totalsize);
				 if(is_nan($widthsize))
					$totalwidthsize = 0;
				else
					$totalwidthsize = $widthsize;
				
				
					if(is_nan($widthsize))
						$showsize = '';
				else{
						$showsize = convertToReadableSize($totalsize);
				}
				
				 $getmbsize = isa_convert_bytes_to_specified($totalsize, 'M');
				?>
				<div class="totalsizee"><h2 style="float:left;">Uploaded files size:</h2><div class="w3-border">
				  <div class="w3-grey" style="height:24px;background: blue;width:<?=$getmbsize*10;?>%"></div>
				</div><h2 style="float:left;margin-top: 2px;"><?=($showsize);?> (Max Size 10mb)</h2></div>
				<input type="hidden" name="totalsizeee" id="totalsizeee" value="<?=$totalsize;?>">
				
					
                <ul class="comment-list">
                	<?php foreach($all_comments as $comment): ?>

                	<li>
                        <?php if($can_edit ||  $comment['comment_by']   == 'contact' ){ ?>
                		 <span class="close" data-id="<?= $comment['id'] ?>">&#215;</span>
                        <?php } ?>
                		<h3> <?=  get_comment_user($comment) ?> <span> <?php echo $comment['created_at']; ?> at <?php echo ($comment['created_time']); ?>
					    </span></h3>
                	    <p><?= $comment['comment'] ?></p>
						
						<!--p>For test =============
						<?php /*$newcmnt = $comment['comment'];
						   $newcmnt = str_replace('https://docs.google.com/viewer?url=','',$newcmnt);
						   echo $newcmnt; */
						?>   </p-->
						
                	</li>

                	<?php endforeach;  ?>
                </ul>


                <div class="row margin-0" style="margin-left: 40px;float:left;width: 100%;max-width:90% !important;">
                		<label>Add Comment </label>
                   		<div style="clear: both;"></div>
                		<textarea class="form-input-textarea" id="addcommentsNe" name="message" rows="7" placeholder="Add some Comments..."></textarea autofocus>
                		<input type="submit" name="add_comment" value="Save" style="margin-top:15px;">
                </div>
				  
  <script>
    var introduction = document.getElementById('addcommentsNe');
    introduction.setAttribute('contenteditable', true);

    CKEDITOR.inline('addcommentsNe', {
      // Allow some non-standard markup that we used in the introduction.
      extraAllowedContent: 'a(documentation);abbr[title];code',
      removePlugins: 'stylescombo',
      extraPlugins: 'sourcedialog,uploadimage',
      // Show toolbar on startup (optional).
	  toolbar: [
			{ name: 'styles', items: [ 'Format'] },
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Strike' ] },
			{ name: 'colors', items: [ 'TextColor'] },
			{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'insert', items: [ 'Image','Smiley' ] },
		],
		
		 height: 300,

      // Upload images to a CKFinder connector (note that the response type is set to JSON).
     // uploadUrl: 'https://ckeditor.com/apps/ckfinder/3.4.5/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',

     //filebrowserBrowseUrl = '/kcfinder/browse.php?type=files',
			filebrowserImageBrowseUrl: '<?php echo "https://".$_SERVER["SERVER_NAME"];?>/kcfinder/browse.php?type=images',
			//filebrowserFlashBrowseUrl = '/kcfinder/browse.php?type=flash',
			//filebrowserUploadUrl = '/kcfinder/upload.php?type=files',
			filebrowserImageUploadUrl: '<?php echo "https://".$_SERVER["SERVER_NAME"];?>/kcfinder/upload.php?type=images',
			//filebrowserFlashUploadUrl = '/kcfinder/upload.php?type=flash
			
			
      // The following options are not necessary and are used here for presentation purposes only.
      // They configure the Styles drop-down list and widgets to use classes.

      stylesSet: [{
          name: 'Narrow image',
          type: 'widget',
          widget: 'image',
          attributes: {
            'class': 'image-narrow'
          }
        },
        {
          name: 'Wide image',
          type: 'widget',
          widget: 'image',
          attributes: {
            'class': 'image-wide'
          }
        }
      ],
	  
	   image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
      image2_disableResizer: true,
		
      startupFocus: true
    });
  </script>
  
    	</fieldset>
    </form>
	
</div>

<?php include_once "includes/ckeditor_inc.php";  ?>
<script src="/<?= ROOT_FOLDER ?>js/viewer/js/viewer.js" crossorigin="anonymous"></script>
<script src="/<?= ROOT_FOLDER ?>js/viewer/js/jquery-viewer.js"></script>
<script src="/<?= ROOT_FOLDER ?>js/viewer/js/main.js"></script>
<script type="text/javascript">
    var uploaded_images = "<?php echo $attachments; ?>";
    var img_arr = [];
    var new_images = [];
    var deleted_attachments = [];
    var files;
    var dataTransfer_itmes =[];
    var obj = new DataTransfer();
    var Input_File = document.getElementById('new_images');

    if(uploaded_images.length > 0 ){
        img_arr =  uploaded_images.split(",");
    }

    function dropHandler(ev) {
      // Prevent default behavior (Prevent file from being opened)
        ev.preventDefault();
		
        if (typeof ev.dataTransfer != 'undefined') {

                if (typeof ev.dataTransfer.items){
                    files =  filder_items(ev.dataTransfer.items);
                }else {
                    files = ev.dataTransfer.files;
                }

        }else{
            files = ev.target.files;
            for (var i = 0; i < files.length; i++) {
                obj.items.add(files[i]);
            }
          }
		 
       handleFileSelect(files);
    }

    function filder_items(items){
        //Use DataTransferItemList interface to access the file(s)
        for (var i = 0; i < items.length; i++) {
          // If dropped items aren't files, reject them
          if (items[i].kind === 'file') {
            var file = items[i].getAsFile();
            obj.items.add(file);
          }
        }
        return obj.files;
    }

    function dragOverHandler(ev) {
      console.log('File(s) in drop zone');
       $('#drop_zone').addClass('active');
      // Prevent default behavior (Prevent file from being opened)
      ev.preventDefault();
    }

    function dragLeave(argument) {
       $('#drop_zone').removeClass('active');
    }

    function handleFileSelect(files) {
        // Loop through the FileList and render image files as thumbnails.
		
		/*document.getElementById('thumbnail-ul').innerHTML = '';
		for (var i = 0; i < files.length; i++) { //for multiple files          
    (function(file) {
        var name = file.name;
        var reader = new FileReader();  
        reader.onload = function(e) {  
            // get file content  
            var text = e.target.result; 
            var li = document.createElement("li");
            li.innerHTML = name + "____" ;
            document.getElementById('thumbnail-ul').appendChild(li);
        }
        reader.readAsText(file, "UTF-8");
    })(files[i]);
} */

     for (var i = 0, f; f = files[i]; i++) {
              var extnsn = (files[i].name.split('.')[1]);
            var $file = files[i];
            // Only process image files.
            //if (!f.type.match('image.*')) {
             //   continue;
           // }

			
			var totllgnth = files.length;
			console.log(totllgnth);
			console.log('beforeee'+i);
		if(i == (totllgnth - 1)){  
		console.log(i);
		console.log(files[i]);
            var reader = new FileReader();
			
            // Closure to capture the file information.
            reader.onload = (function (theFile) {
				
                return function (e) {
                    // Render thumbnail.
					if(extnsn == 'pdf'){
					 var srcdata = 'https://client.tickletrain.com/images/Extension/icon-pdf.png';
				 }else if(extnsn == 'doc' || extnsn == 'docx'){
					 var srcdata = 'https://client.tickletrain.com/images/Extension/icon-doc.png';
				 }else if(extnsn == 'txt'){
					 var srcdata = 'https://client.tickletrain.com/images/Extension/icon-txt.png';
				 }else if(extnsn == 'txt'){
					 var srcdata = 'https://client.tickletrain.com/images/Extension/icon-txt.png';
				 }else if(extnsn == 'psd'){
					 var srcdata = 'https://client.tickletrain.com/images/Extension/icon-psd.png';
				 }else if(extnsn == 'jpeg' || extnsn == 'jpg' || extnsn == 'png' || extnsn == 'gif'){
					  var srcdata = e.target.result;
				 }else{
					  var srcdata = 'https://client.tickletrain.com/images/Extension/icon-empty.png';
                     }
					 
						 console.log(srcdata);
					 var span = document.createElement('li');
                        span.innerHTML ='<span class="close jq">&#215;</span><img style="height: 75px; border: 1px solid #e6e5e5; margin: 5px" src="'+srcdata+'" />';
					document.getElementById('thumbnail-ul').insertBefore(span,null);
                };
            })($file);
            // Read in the image file as a data URL.
           
            reader.readAsDataURL($file);
            $('#drop_zone').removeClass('active');
		} // endif
        } 
        Input_File.files = files;
    }

    function remove_files(i) {
        if(obj.items.length > 0){
            obj.items.remove(i);
            Input_File.files = obj.files;
        }else{
            console.log(files);
        }
    }


    $(document).ready(function() {
		var sw = "<?php echo 'https://'.$_SERVER['SERVER_NAME'];?>/ckeditorn/mystyles.css";
		//console.log(sw);
        CKEDITOR.env.isCompatible = true;
        
        <?php if(!$can_edit){ ?>
            CKEDITOR.replace('TickleMailContentNe',{
				
				toolbar: [
			{ name: 'document', items: [ 'Print' ] },
			{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
			{ name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
			{ name: 'insert', items: [ 'Image', 'Table' ] },
			{ name: 'tools', items: [ 'Maximize' ] },
			{ name: 'editing', items: [ 'Scayt' ] }
		],
			contentsCss: [ 'https://cdn.ckeditor.com/4.8.0/full-all/contents.css',"<?php echo 'https://'.$_SERVER['SERVER_NAME'];?>/ckeditorn/mystyles.css" ],
			bodyClass: 'document-editor',
		
			 height: 300,

      // Upload images to a CKFinder connector (note that the response type is set to JSON).
     // uploadUrl: 'https://ckeditor.com/apps/ckfinder/3.4.5/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',

      // Configure your file manager integration. This example uses CKFinder 3 for PHP.
	  
 //filebrowserBrowseUrl = '/kcfinder/browse.php?type=files',
			filebrowserImageBrowseUrl: '<?php echo "https://".$_SERVER["SERVER_NAME"];?>/kcfinder/browse.php?type=images',
			//filebrowserFlashBrowseUrl = '/kcfinder/browse.php?type=flash',
			//filebrowserUploadUrl = '/kcfinder/upload.php?type=files',
			filebrowserImageUploadUrl: '<?php echo "https://".$_SERVER["SERVER_NAME"];?>/kcfinder/upload.php?type=images',
			//filebrowserFlashUploadUrl = '/kcfinder/upload.php?type=flash	
			
      // The following options are not necessary and are used here for presentation purposes only.
      // They configure the Styles drop-down list and widgets to use classes.

      stylesSet: [{
          name: 'Narrow image',
          type: 'widget',
          widget: 'image',
          attributes: {
            'class': 'image-narrow'
          }
        },
        {
          name: 'Wide image',
          type: 'widget',
          widget: 'image',
          attributes: {
            'class': 'image-wide'
          }
        }
      ],
	  
	   image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
      image2_disableResizer: true,
		
				readOnly : true
				
				});
        <?php }else{ ?>
            CKEDITOR.replace('TickleMailContentNe',{
				
				toolbar: [
			{ name: 'document', items: [ 'Print' ] },
			{ name: 'clipboard', items: [ 'Undo', 'Redo' ] },
			{ name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'CopyFormatting' ] },
			{ name: 'colors', items: [ 'TextColor', 'BGColor' ] },
			{ name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
			{ name: 'links', items: [ 'Link', 'Unlink' ] },
			{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
			{ name: 'insert', items: [ 'Image', 'Table' ] },
			{ name: 'tools', items: [ 'Maximize' ] },
			{ name: 'editing', items: [ 'Scayt' ] }
		],
		
		 height: 300,

      // Upload images to a CKFinder connector (note that the response type is set to JSON).
    //  uploadUrl: 'https://ckeditor.com/apps/ckfinder/3.4.5/core/connector/php/connector.php?command=QuickUpload&type=Files&responseType=json',


      // Configure your file manager integration. This example uses CKFinder 3 for PHP.
    /*  filebrowserBrowseUrl: 'https://ckeditor.com/apps/ckfinder/3.4.5/ckfinder.html',
      filebrowserImageBrowseUrl: 'https://ckeditor.com/apps/ckfinder/3.4.5/ckfinder.html?type=Images',
      filebrowserUploadUrl: 'https://ckeditor.com/apps/ckfinder/3.4.5/core/connector/php/connector.php?command=QuickUpload&type=Files',
      filebrowserImageUploadUrl: 'https://ckeditor.com/apps/ckfinder/3.4.5/core/connector/php/connector.php?command=QuickUpload&type=Images',*/
	  
	  
	//filebrowserBrowseUrl = '/kcfinder/browse.php?type=files',
			filebrowserImageBrowseUrl: '<?php echo "https://".$_SERVER["SERVER_NAME"];?>/kcfinder/browse.php?type=images',
			//filebrowserFlashBrowseUrl = '/kcfinder/browse.php?type=flash',
			//filebrowserUploadUrl = '/kcfinder/upload.php?type=files',
			filebrowserImageUploadUrl: '<?php echo "https://".$_SERVER["SERVER_NAME"];?>/kcfinder/upload.php?type=images',
			//filebrowserFlashUploadUrl = '/kcfinder/upload.php?type=flash',	

      // The following options are not necessary and are used here for presentation purposes only.
      // They configure the Styles drop-down list and widgets to use classes.

      stylesSet: [{
          name: 'Narrow image',
          type: 'widget',
          widget: 'image',
          attributes: {
            'class': 'image-narrow'
          }
        },
        {
          name: 'Wide image',
          type: 'widget',
          widget: 'image',
          attributes: {
            'class': 'image-wide'
          }
        }
      ],
	  
	   image2_alignClasses: ['image-align-left', 'image-align-center', 'image-align-right'],
      image2_disableResizer: true,
		

			contentsCss: [ 'https://cdn.ckeditor.com/4.8.0/full-all/contents.css',"<?php echo 'https://'.$_SERVER['SERVER_NAME'];?>/ckeditorn/mystyles.css" ],
			bodyClass: 'document-editor',
			
			});
        <?php } ?>

		//CKEDITOR.disableAutoInline = true;
	
	
        CKEDITOR.replace('addcomments',{
            startupFocus : true
        }); 

    });

    $(document).on('click','#thumbnail-ul li span.close',function(e) { 
        var index  = $(this).parent().index();
        if(index >= img_arr.length){
            $(this).closest('li').fadeOut().remove();
            var ind =  index-img_arr.length;
			console.log(ind);
            remove_files(ind);
        }else{
			
            deleted_attachments.push(img_arr[index]);
            img_arr.splice(index,1);
            $(this).closest('li').fadeOut().remove();
            $('input[name=attachments]').val(img_arr);
            //$('input[name=deleted_attachments]').val(img_arr);
			$('input[name=deleted_attachments]').val(deleted_attachments);
        }
    });

    $('ul.comment-list li span.close').on('click',function(e) {
        var that  = $(this);
        var id  =  $(this).data().id;
        $.ajax({
                url: 'https://client.tickletrain.com/addcomments/?delete_comment='+id,
               // dataType: 'json',
                method: 'GET',
            }).done(function (response) {
                that.closest('li').fadeOut();
            }).fail(function(){
                console.log('error');
            });
    });
</script>
