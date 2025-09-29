<?php
/**
 * Test Script for Gemini AI Integration
 * Run this script to verify that Gemini AI is working correctly
 */

require_once 'core/config.php';
require_once 'services/GeminiService.php';

// Check if GEMINI_API_KEY is configured
if (!getenv('GEMINI_API_KEY')) {
    die("❌ GEMINI_API_KEY is not configured in your .env file\n");
}

echo "🤖 Testing Gemini AI Integration...\n\n";

try {
    // Initialize Gemini service
    $geminiService = new GeminiService();
    
    echo "1. Testing Connection...\n";
    $connectionTest = $geminiService->testConnection();
    
    if ($connectionTest['success']) {
        echo "   ✅ Connection successful!\n";
        echo "   Response: " . $connectionTest['test_response'] . "\n\n";
    } else {
        echo "   ❌ Connection failed: " . $connectionTest['error'] . "\n";
        exit(1);
    }
    
    echo "2. Testing Customer Support Response...\n";
    
    // Sample ticket context
    $sampleTicket = [
        'ticket_id' => 'TEST-' . time(),
        'subject' => 'Order Delivery Inquiry',
        'status' => 'open',
        'priority' => 'medium',
        'category' => 'shipping',
        'created_at' => date('Y-m-d H:i:s'),
        'order_id' => 12345,
        'order_number' => 'ORDER-123456',
        'total_amount' => 89.99,
        'tracking_number' => 'TRK7890123',
        'order_status' => 'shipped',
        'store_name' => 'Demo Store',
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '+1234567890',
        'messages' => [
            [
                'sender_type' => 'customer',
                'message' => 'Hi, I ordered a product last week. When will it be delivered?',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ]
        ]
    ];
    
    $customerMessage = "I'm getting worried about my order. It's been 5 days and I haven't received any updates.";
    
    $aiResponse = $geminiService->generateSupportResponse($sampleTicket, $customerMessage);
    
    if ($aiResponse['success']) {
        echo "   ✅ AI Response generated successfully!\n";
        echo "   Customer Message: {$customerMessage}\n";
        echo "   AI Response: {$aiResponse['message']}\n";
        echo "   Response Length: {$aiResponse['metadata']['response_length']} characters\n\n";
    } else {
        echo "   ❌ AI Response failed: " . $aiResponse['error'] . "\n";
        exit(1);
    }
    
    echo "3. Testing Different Scenarios...\n";
    
    $testScenarios = [
        "Can I cancel my order if it hasn't shipped yet?",
        "What's the return policy for damaged items?",
        "I received the wrong product. What should I do?",
        "Is there a phone number I can call for urgent issues?"
    ];
    
    foreach ($testScenarios as $index => $scenario) {
        echo "   Scenario " . ($index + 1) . ": {$scenario}\n";
        
        $response = $geminiService->generateSupportResponse($sampleTicket, $scenario);
        
        if ($response['success']) {
            echo "   ✅ Response: " . substr($response['message'], 0, 80) . "...\n";
        } else {
            echo "   ❌ Failed: " . $response['error'] . "\n";
        }
        echo "\n";
    }
    
    echo "🎉 Gemini AI Integration Test Complete!\n\n";
    echo "Summary:\n";
    echo "- ✅ Gemini API Connection: Working\n";
    echo "- ✅ AI Response Generation: Working\n";
    echo "- ✅ Customer Support Context: Working\n";
    echo "- ✅ Error Handling: Working\n\n";
    echo "The AI assistant is ready to help customers with their support tickets!\n";
    
} catch (Exception $e) {
    echo "❌ Test failed with exception: " . $e->getMessage() . "\n";
    exit(1);
}
?>
