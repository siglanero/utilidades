<?php 

$path = 'SEU_DIRETORIO';

$files = array_slice(scandir($path), 2);
$getFiles = [];

function getDirContents($dir, &$results = array()){
	$files = scandir($dir);

	foreach($files as $key => $value){
		$path = realpath($dir.DIRECTORY_SEPARATOR.$value);
		if(!is_dir($path)) {
			$results[] = $path;
		} else if($value != "." && $value != "..") {
			getDirContents($path, $results);
			$results[] = $path;
		}
	}

	return $results;
}

foreach ($files as $value) {

	$info = $path;
	if(realpath($info)){
		$getFiles[] = getDirContents($info);
	}
}


echo '<pre>';
print_r($getFiles);
echo '</pre>';
?>
