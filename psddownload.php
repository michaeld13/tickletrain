<?php

if(isset($_REQUEST["filePSD"])){ 
    // Get parameters
    $file = urldecode($_REQUEST["filePSD"]); // Decode URL-encoded string
	$filePath = urldecode($_REQUEST["filePath"]);

    /* Test whether the file name contains illegal characters
    such as "../" using the regular expression */
    if(preg_match('/^[^.][-a-z0-9_.() ]+[a-z]$/i', $file)){
        $filepath = $filePath.$file;

        // Process download
        if(file_exists($filepath)) { 
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filepath));
            flush(); // Flush system output buffer
            readfile($filepath);
            die();
        } else { 
            http_response_code(404);
	        die();
        }
    } else {
        die("Invalid file name!");
    }
}