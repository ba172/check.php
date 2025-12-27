<?php
header('Content-Type: application/json');

$input = $_POST['input'] ?? '';

// جلب الأكواد من Environment
$codes_json = getenv('CODES_JSON');
$codes = json_decode($codes_json, true);

// تحقق منطقي فقط
if (isset($codes[$input]) && $codes[$input] == 1) {

    // جلب الإيميلات
    $emails_txt = getenv('EMAILS_TXT');
    $emails = array_filter(array_map('trim', explode("\n", $emails_txt)));

    echo json_encode([
        "status" => "allowed",
        "emails" => $emails
    ]);
    exit;
}

// في حال الكود غير موجود
echo json_encode([
    "status" => "denied",
    "message" => "CODE NOT FOUND"
]);


echo json_encode([
    "debug_input" => $input,
    "codes_env" => $codes
]);
exit;

?>
