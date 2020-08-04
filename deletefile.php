<?php
session_start();
include("includes/includes.php");
$fl = $db->select_row("files",''," where FileID='".@intval($_REQUEST['fid'])."'");
@unlink(FULL_UPLOAD_FOLDER.$fl['FileName']);
$db->delete("files", " where FileID='".@intval($_REQUEST['fid'])."'");
?>