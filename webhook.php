<?php
// Set content type
header('Content-Type: application/json');

// Read raw POST body
$rawBody = file_get_contents('php://input');

// Decode JSON
$data = json_decode($rawBody, true);

// Optional: Log incoming payload for debugging
file_put_contents('webhook_log.txt', date('Y-m-d H:i:s') . " - Payload: " . $rawBody . PHP_EOL, FILE_APPEND);

// Example: Verify HMAC signature (if your webhook provider sends one)
$headers = getallheaders();
$secret = 'YOUR_WEBHOOK_SECRET'; // change this to your actual secret

if (isset($headers['X-Signature'])) {
    $computed = hash_hmac('sha256', $rawBody, $secret);
    if (!hash_equals($headers['X-Signature'], $computed)) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
        exit;
    }
}

// Process webhook payload
if (isset($data['event'])) {
    switch ($data['event']) {
        case 'payment.success':
            // Example: handle success event
            file_put_contents('webhook_log.txt', "Payment success for ID: {$data['id']}" . PHP_EOL, FILE_APPEND);
            break;

        case 'payment.failed':
            // Example: handle failure event
            file_put_contents('webhook_log.txt', "Payment failed for ID: {$data['id']}" . PHP_EOL, FILE_APPEND);
            break;

        default:
            file_put_contents('webhook_log.txt', "Unknown event: {$data['event']}" . PHP_EOL, FILE_APPEND);
            break;
    }
}

// Return HTTP 200 to acknowledge receipt
http_response_code(200);
echo json_encode(['status' => 'ok']);
?>
