<?php if ($addtickle){
$addfuncs = "'Firstname','Lastname','Signature','-',";
}
include 'GetBrowser.php';
$BrObject = new Browser();
$BrowserName = $BrObject->getBrowser();
if($BrowserName == 'Firefox') { ?>
    <script type="text/javascript" src="/<?=ROOT_FOLDER?>ckeditorn/ckeditor.js"></script>
<?php } else { ?>
    <script type="text/javascript" src="/<?=ROOT_FOLDER?>ckeditorn/ckeditor.js"></script>
<?php }  ?>


<script type="text/javascript" src="/<?=ROOT_FOLDER?>js/dialog-patch.js"></script>
<script type="text/javascript">
var config = {
		toolbar:
		[
                    ['Format','Font','FontSize','-','Bold','Italic','Underline','RemoveFormat','-','TextColor','BGColor','-','NumberedList','BulletedList','DecreaseIndent','IncreaseIndent','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','Table'],
                    '/',
                    [<?=$addfuncs?>'Image','HorizontalRule','SpecialChar','-','Source','Preview','Templates','-','Cut','Copy','Paste','PasteText','PasteFromWord','Undo','Redo','-','Link','Unlink','-','Maximize','ShowBlocks']
     		],
			skin:'kama',
			jqueryOverrideVal:true,
			startupFocus: true,
            extraPlugins: 'firstname,lastname,signature',
			image_previewText: 'This is what your image will look like in a paragraph. If you want to add spacing or other attributes to your image use the image property tools.',
			//filebrowserBrowseUrl = '/kcfinder/browse.php?type=files';
			filebrowserImageBrowseUrl: '/<?=ROOT_FOLDER?>kcfinder/browse.php?type=images',
			//filebrowserFlashBrowseUrl = '/kcfinder/browse.php?type=flash';
			//filebrowserUploadUrl = '/kcfinder/upload.php?type=files';
			filebrowserImageUploadUrl: '/<?=ROOT_FOLDER?>kcfinder/upload.php?type=images'
			//filebrowserFlashUploadUrl = '/kcfinder/upload.php?type=flash';			
};
</script>