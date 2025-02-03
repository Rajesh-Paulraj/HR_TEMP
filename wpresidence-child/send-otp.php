<?php
session_start();

// Your MSG91 API Key
$apiKey = '429398TfsMXQlt66d45ee4P1';
$templateId = '66d45c75d6fc054c33669e02';

// Get the mobile number from the request
$data = json_decode(file_get_contents('php://input'), true);
$mobile = $data['mobile'];

// Generate a random OTP
$otp = rand(100000, 999999);

// MSG91 API endpoint
// $url = "https://api.msg91.com/api/v5/otp";

// Request headers and payload
// $headers = [
//     'authkey: ' . $apiKey,
//     'Content-Type: application/json',
// ];
// $payload = json_encode([
//     'mobile' => '91' . $mobile, // Ensure the mobile number has country code 91 for India
//     'otp' => $otp,
//     'sender' => 'OTPMSG', // Your approved sender ID
//     'message' => "Your OTP is: $otp",
//     'expiry' => '5', // OTP expiry time in minutes
// ]);

// Initialize CURL and send the request
// $ch = curl_init($url);
// curl_setopt($ch, CURLOPT_POST, true);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
// $response = curl_exec($ch);
// curl_close($ch);

$curl = curl_init();

curl_setopt_array($curl, [
  CURLOPT_URL => "https://control.msg91.com/api/v5/otp?otp_expiry=5&template_id=" . $templateId . "&mobile=" . $mobile . "&authkey=" . $apiKey . "&realTimeResponse=1",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "{\n  \"Param1\": \"value1\",\n  \"Param2\": \"value2\",\n  \"Param3\": \"value3\"\n}",
  CURLOPT_HTTPHEADER => [
    "Content-Type: application/JSON"
  ],
]);

$response = curl_exec($curl);
// $err = curl_error($curl);

curl_close($curl);

// if ($err) {
//   echo "cURL Error #:" . $err;
// } else {
//   echo $response;
// }

$responseData = json_decode($response, true);

// Check the response status
if ($responseData['type'] == 'success') {
    // Store OTP temporarily in session
    $_SESSION['otp'] = $otp;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $responseData['message']]);
}
