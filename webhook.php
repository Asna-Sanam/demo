<?php
$secret = '123456'; // Optional: use same secret when creating GitHub webhook

// Verify GitHub secret (optional for security)
$payload = file_get_contents('php://input');
$headers = getallheaders();

if (isset($headers['X-Hub-Signature-256'])) {
    $signature = 'sha256=' . hash_hmac('sha256', $payload, $secret);
    if (!hash_equals($signature, $headers['X-Hub-Signature-256'])) {
        http_response_code(403);
        die('Invalid signature');
    }
}

// Log for debugging (optional)
file_put_contents('/tmp/webhook.log', date('c') . " - Received webhook\n", FILE_APPEND);

// Run deployment script
exec('/var/www/asna/deploy.sh >> /tmp/deploy.log 2>&1');
echo "Deployment triggered";
echo "Starting deployment..."
git pull origin main
echo "Deployment complete."

