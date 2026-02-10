<?php
// Function to fetch data using cURL
function fetchGoldPriceData($url) {
    // Initialize cURL session
    $ch = curl_init();
    
    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
    
    // Execute cURL request
    $response = curl_exec($ch);
    
    // Check for errors
    if (curl_errno($ch)) {
        $error = curl_error($ch);
        curl_close($ch);
        return json_encode([
            'success' => false,
            'error' => $error
        ]);
    }
    
    // Get HTTP status code
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    // Close cURL session
    curl_close($ch);
    
    // Check if request was successful
    if ($httpCode !== 200) {
        return json_encode([
            'success' => false,
            'error' => 'HTTP Error: ' . $httpCode
        ]);
    }
    
    // Try to decode the response as JSON to validate it
    $decoded = json_decode($response);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        // If response is not valid JSON, wrap it
        return json_encode([
            'success' => true,
            'data' => $response,
            'note' => 'Response was not valid JSON, returned as string'
        ]);
    }
    
    // Return the response as formatted JSON
    return json_encode([
        'success' => true,
        'data' => $decoded
    ], JSON_PRETTY_PRINT);
}

// Set header to return JSON
header('Content-Type: application/json');

// Get currency parameter from GET or POST request
// Default to USD if not provided
$currency = isset($_GET['currency']) ? $_GET['currency'] : (isset($_POST['currency']) ? $_POST['currency'] : 'USD');

// Sanitize currency input (only allow alphanumeric characters, 3 chars max)
$currency = strtoupper(preg_replace('/[^A-Za-z]/', '', $currency));
$currency = substr($currency, 0, 3);

// Validate currency code length
if (strlen($currency) !== 3) {
    echo json_encode([
        'success' => false,
        'error' => 'Invalid currency code. Must be 3 characters (e.g., USD, EUR, GBP)'
    ]);
    exit;
}

// Build URL with currency parameter
$url = 'https://data-asg.goldprice.org/dbXRates/' . $currency;

// Fetch and output the data
echo fetchGoldPriceData($url);
?>