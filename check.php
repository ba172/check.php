<?php
header("Content-Type: application/json");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");

$input = $_POST['input'] ?? '';

if (!preg_match('/^\d{8}$/', $input)) {
    echo json_encode(["status" => "denied", "message" => "wrong code"]);
    exit;
}

// قراءة الأكواد من Environment Variable
$codes_env = getenv("CODES_JSON");
if (!$codes_env) {
    echo json_encode(["status" => "error", "message" => "code no't found"]);
    exit;
}

$codes = json_decode($codes_env, true);

// تحقق من الكود
if (!isset($codes[$input])) {
    echo json_encode(["status" => "denied", "message" => "code no't work !! ]);
    exit;
}

// قراءة الإيميلات من Environment Variable
$emails_env = getenv("EMAILS_TXT");
$emails = $emails_env ? explode("\n", $emails_env) : [];

// إرجاع JSON
echo json_encode([
    "status" => "allowed",
    "count"  => count($emails),
    "emails" => $emails
], JSON_UNESCAPED_UNICODE);
?>
