 *----------------------------------------------
 * @method 	Encrypt
 * @param 	$input 	Informe o texto que será criptografado.
 * @param 	$key1 	Informe sua primeira senha ou deixe em branco.
 * @param 	$key2 	Informe sua segunda senha ou deixe em branco.
 * @example Encrypt("apenas um teste", "123", "321");
 *----------------------------------------------
 *
 * 	Esse método irá criar uma criptografia do seu texto
 *
 **/

 //==============================================================================

 public function Encrypt($input, $key1, $key2){



 	$first_key 	= $key1;

 	$second_key = $key2;



 	$method = "aes-256-cbc";

 	$iv_length = openssl_cipher_iv_length($method);

 	$iv = openssl_random_pseudo_bytes($iv_length);



 	$first_encrypted = openssl_encrypt($input,$method,$first_key, OPENSSL_RAW_DATA ,$iv);

 	$second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);



 	$output = base64_encode($iv.$second_encrypted.$first_encrypted);

 	return $output;

 }//fim do método Encrypt();



 //==============================================================================

 /**
 *
 *----------------------------------------------
 * @method 	Decrypt
 * @param 	$output 	coloque aqui a criptografia gerada no Método Encrypt().
 * @param 	$key1 	coloque a primeira senha informada no Método Encrypt().
 * @param 	$key2 	coloque a segunda senha informada no Método Encrypt().
 *----------------------------------------------
 *
 * 	Esse método irá criar uma criptografia do seu texto
 *
 **/

 //==============================================================================

 public function Decrypt($output, $key1, $key2){



 	$first_key 	= $key1;

 	$second_key = $key2;

 	$mix = base64_decode($output);



 	$method = "aes-256-cbc";

 	$iv_length = openssl_cipher_iv_length($method);



 	$iv = substr($mix,0,$iv_length);

 	$second_encrypted = substr($mix,$iv_length,64);

 	$first_encrypted = substr($mix,$iv_length+64);



 	$data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);

 	$second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);



 	if (hash_equals($second_encrypted,$second_encrypted_new))

 		return $data;



 	return false;

 }//fim do método Decrypt();
