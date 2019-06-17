<?php

function tagName($string){

	$remove_separator = str_replace(" ", "_", $string);

	$name = strtolower(preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $remove_separator ) ));

	return utf8_encode($name);

}

echo tagName('Eu moro em São Paulo');

//eu_moro_em_sao_paulo
?>