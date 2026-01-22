<?php
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

$input = trim($_POST['input'] ?? '');

if (empty($input)) {
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);
    $input = trim($data['input'] ?? '');
}

$codes_json = getenv('CODES_JSON') ?: '{}';
$codes = json_decode($codes_json, true);

if (!is_array($codes)) {
    $codes = [];
}

$emails_txt = getenv('EMAILS_TXT') ?: '';
$emails = [];
if (!empty($emails_txt)) {
    $emails = array_filter(array_map('trim', explode("\n", $emails_txt)));
}

if (empty($input)) {
    echo json_encode([
        "status" => "error",
        "message" => "No input provided",
        "debug" => [
            "post_data" => $_POST,
            "raw_input" => file_get_contents('php://input'),
            "available_codes_count" => count($codes)
        ]
    ]);
    exit;
}

if (isset($codes[$input]) && $codes[$input] == 1) {
    echo json_encode([
        "status" => "allowed",
        "emails" => $emails,
        "matched_code" => $input
    ]);
    exit;
}

echo json_encode([
    "status" => "denied",
    "message" => "CODE NOT FOUND",
    "your_input" => $input,
    "available_codes" => array_keys($codes)
]);
exit;
?>
