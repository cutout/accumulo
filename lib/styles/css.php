<?php
	$s_contentType = 'text/css';
	$s_fileType = 'css';
	$s_baseFolder = '/scripts/';

	$files = $_GET['f'];
	$files = addslashes (stripslashes($files));

	$fileData = '';

	if ($files != null) {
		// content type
		header ('Content-type: ' . $s_contentType);

		// caching
		/*
		$offset = 3600 * 24;
		header ('Expires: ' . gmdate('D, d M Y H:i:s', time() + $offset) . ' GMT');
		header ('Cache-Control: max-age=' . $offset);
		*/
		
		// get files
		$files = split (',', $files);

		// print out the files
		foreach ($files as $file) {
			$file = str_replace('.css', '', $file);
			if ($file != '') {
				$file = $_SERVER['DOCUMENT_ROOT'] . $s_baseFolder . $file . '.' . $s_fileType;
				if (file_exists($file)) {
					$fileData .= file_get_contents ($file);
				} else {
					echo $file;
				}
			}
		}
	}

	echo $fileData;
?>