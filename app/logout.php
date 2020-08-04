<?php
if(isset($_GET['ttextension'])){
  session_destroy();
  echo "<script>window.close();</script>";
}
else{
session_destroy();
redirect('');
}
?>