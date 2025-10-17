<?php
// Set content type
header('Content-Type: application/json');
$headers = getallheaders();
// Read raw POST body
$rawBody = file_get_contents('php://input');

$path = '/webhook.php';
$timestamp = $headers['X-TIMESTAMP'];
$signature = $headers['X-SIGNATURE'];
$payload = hash('sha256', $rawBody);
$data = 'POST:'.$path.':'.$hash.':'.$timestamp;

function verify($data, $publicKey, $signature) {
	$binarySignature = base64_decode($signature);

	return openssl_verify($data, $binarySignature,  $publicKey, OPENSSL_ALGO_SHA256);
}

$publicKeyContent = <<< EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnaKVGRbin4Wh4KN35OPh
ytJBjYTz7QZKSZjmHfiHxFmulfT87rta+IvGJ0rCBgg+1EtKk1hX8G5gPGJs1htJ
5jHa3/jCk9l+luzjnuT9UVlwJahvzmFw+IoDoM7hIPjsLtnIe04SgYo0tZBpEmkQ
vUGhmHPqYnUGSSMIpDLJDvbyr8gtwluja1SbRphgDCoYVXq+uUJ5HzPS049aaxTS
nfXh/qXuDoB9EzCrgppLDS2ubmk21+dr7WaO/3RFjnwx5ouv6w+iC1XOJKar3CTk
X6JV1OSST1C9sbPGzMHZ8AGB51BM0mok7davD/5irUk+f0C25OgzkwtxAt80dkDo
/QIDAQAB
-----END PUBLIC KEY-----
EOD;

$isValid = verify($data, $publicKeyContent, $signature);

if (!$isValid) {
    http_response_code(401);
    echo json_encode(['responseCode' => '5005601', 'message' => 'Invalid signature']);
    exit;
}
// Decode JSON
$data = json_decode($rawBody, true);

// Process webhook payload

// Return HTTP 200 to acknowledge receipt
http_response_code(200);
echo json_encode(['responseCode' => '2005600', 'responseMessage' => 'Successful']);
?>
