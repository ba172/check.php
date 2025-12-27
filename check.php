<?php
header('Content-Type: application/json');

// تقرير الأخطاء للتصحيح
error_reporting(E_ALL);
ini_set('display_errors', 1);

// الحصول على البيانات المدخلة
$input = trim($_POST['input'] ?? '');

// إذا لم يكن هناك input في POST، جرب قراءة JSON
if (empty($input)) {
    $json_input = file_get_contents('php://input');
    $data = json_decode($json_input, true);
    $input = trim($data['input'] ?? '');
}

// جلب الأكواد من البيئة - مع معالجة الأخطاء
$codes_json = getenv('CODES_JSON') ?: '{}';
$codes = json_decode($codes_json, true);

// إذا فشل تحويل JSON، استخدم مصفوفة فارغة
if (!is_array($codes)) {
    $codes = [];
}

// جلب الإيميلات من البيئة
$emails_txt = getenv('EMAILS_TXT') ?: '';
$emails = [];
if (!empty($emails_txt)) {
    $emails = array_filter(array_map('trim', explode("\n", $emails_txt)));
}

// المنطق الرئيسي
if (empty($input)) {
    // إذا كان الإدخال فارغًا
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
    // الكود صحيح
    echo json_encode([
        "status" => "allowed",
        "emails" => $emails,
        "matched_code" => $input
    ]);
    exit;
}

// الكود غير موجود
echo json_encode([
    "status" => "denied",
    "message" => "CODE NOT FOUND",
    "your_input" => $input,
    "available_codes" => array_keys($codes)
]);
exit;
?>
