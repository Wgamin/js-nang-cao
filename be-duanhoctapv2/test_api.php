<?php
$ch = curl_init("http://localhost:8000/api/admin/schedules");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
$result = curl_exec($ch);
if ($result === false) {
    echo "cURL Error: " . curl_error($ch);
} else {
    $data = json_decode($result, true);
    echo json_encode($data, JSON_PRETTY_PRINT);
}
curl_close($ch);
?>
