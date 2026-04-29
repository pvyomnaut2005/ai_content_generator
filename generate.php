<?php
require_once "env.php";
header("Content-Type: application/json");

// Get input
$data = json_decode(file_get_contents("php://input"), true);

$company = $data['company'] ?? 'Test Company';
$topic   = $data['topic'] ?? 'AI Automation';
$tone    = $data['tone'] ?? 'Professional';

$prompt = "Write a".$tone." LinkedIn post for".$company." about ".$topic.". Keep it short and engaging.";



//  CREATE CURL HANDLE FIRST
$ch = curl_init("https://openrouter.ai/api/v1/chat/completions");

// Payload
$payload = [
  "model" => "openai/gpt-4o-mini",
  "messages" => [
    ["role" => "user", "content" => $prompt]
  ]
];

// ✅ NOW set options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
  "Authorization: Bearer " . $apiKey,
  "Content-Type: application/json",
  "HTTP-Referer: http://localhost",
  "X-Title: AI Content Generator"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

// Execute
$response = curl_exec($ch);

// Error handling
if ($response === false) {
    echo json_encode(["text" => "cURL Error: " . curl_error($ch)]);
    exit;
}

$result = json_decode($response, true);

if (isset($result["error"])) {
    echo json_encode(["text" => "API Error: " . $result["error"]["message"]]);
    exit;
}

if (!isset($result["choices"][0]["message"]["content"])) {
    echo json_encode(["text" => "Unexpected response: " . $response]);
    exit;
}

// Success
echo json_encode([
  "text" => $result["choices"][0]["message"]["content"]
]);
