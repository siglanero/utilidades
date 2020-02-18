<?php 

$path = 'SEU_DIRETORIO';
$files = array_slice(scandir($path), 2);
$getEmpresa = [];

foreach ($files as $value) {

	$info = $path;
	if(realpath($info)){

		$getEmpresa[] = getDirContents($info);
	}
}


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


echo '<pre>';
print_r($getEmpresa);
echo '</pre>';
?>
