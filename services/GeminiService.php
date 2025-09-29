<?php

class GeminiService
{
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta';

    public function __construct()
    {
        $this->apiKey = getenv('GEMINI_API_KEY');
        
        if (!$this->apiKey) {
            throw new Exception('GEMINI_API_KEY not configured in environment variables');
        }
    }

    /**
     * Generate AI response for support ticket
     * 
     * @param array $ticketData Complete ticket and order information
     * @param string $customerMessage Customer's current message
     * @return array Response with generated message and metadata
     */
    public function generateSupportResponse($ticketData, $customerMessage)
    {
        try {
            // Prepare context for Gemini
            $context = $this->buildContext($ticketData, $customerMessage);
            
            // Generate prompt
            $prompt = $this->buildPrompt($context, $customerMessage);
            
            // Call Gemini API
            $response = $this->callGeminiAPI($prompt);
            
            // Parse response
            $aiMessage = $this->parseResponse($response);
            
            return [
                'success' => true,
                'message' => $aiMessage,
                'metadata' => [
                    'model' => 'gemini-2.5-flash',
                    'ticket_id' => $ticketData['ticket_id'] ?? '',
                    'context_length' => strlen($context),
                    'response_length' => strlen($aiMessage)
                ]
            ];
            
        } catch (Exception $e) {
            error_log("Gemini AI error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'I apologize, but I\'m having trouble processing your request right now. Please try again or contact our support team directly.',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Build context from ticket and order data
     */
    private function buildContext($ticketData, $customerMessage)
    {
        $context = "SUPPORT TICKET CONTEXT:\n\n";
        $context .= "Ticket ID: " . ($ticketData['ticket_id'] ?? 'N/A') . "\n";
        $context .= "Subject: " . ($ticketData['subject'] ?? 'N/A') . "\n";
        $context .= "Status: " . ucfirst(str_replace('_', ' ', $ticketData['status'] ?? 'open')) . "\n";
        $context .= "Priority: " . ucfirst($ticketData['priority'] ?? 'medium') . "\n";
        $context .= "Category: " . ucfirst(str_replace('_', ' ', $ticketData['category'] ?? 'other')) . "\n";
        
        if (isset($ticketData['created_at'])) {
            $context .= "Created: " . date('Y-m-d H:i:s', strtotime($ticketData['created_at'])) . "\n\n";
        } else {
            $context .= "Created: N/A\n\n";
        }
        
        $context .= "ORDER INFORMATION:\n";
        $context .= "Order ID: " . ($ticketData['order_id'] ?? 'N/A') . "\n";
        $context .= "Order Number: " . ($ticketData['order_number'] ?? 'N/A') . "\n";
        $context .= "Order Total: $" . number_format($ticketData['total_amount'] ?? 0, 2) . "\n";
        $context .= "Order Status: " . ucfirst($ticketData['order_status'] ?? 'Unknown') . "\n";
        $context .= "Tracking Number: " . ($ticketData['tracking_number'] ?? 'Not available') . "\n\n";
        
        $context .= "CUSTOMER INFORMATION:\n";
        $context .= "Name: " . ($ticketData['customer_name'] ?? 'N/A') . "\n";
        $context .= "Email: " . ($ticketData['customer_email'] ?? 'N/A') . "\n";
        $context .= "Phone: " . ($ticketData['customer_phone'] ?? 'N/A') . "\n\n";
        
        if (isset($ticketData['store_name'])) {
            $context .= "STORE INFORMATION:\n";
            $context .= "Store Name: " . $ticketData['store_name'] . "\n\n";
        }
        
        $context .= "CONVERSATION HISTORY:\n";
        if (isset($ticketData['messages']) && is_array($ticketData['messages']) && count($ticketData['messages']) > 0) {
            // Limit to last 5 messages to reduce token usage
            $recentMessages = array_slice($ticketData['messages'], -5);
            foreach ($recentMessages as $msg) {
                $sender = ($msg['sender_type'] ?? 'system') === 'user' ? 'Customer' : 
                         (($msg['sender_type'] ?? 'system') === 'support' ? 'Support Agent' : 'System');
                $context .= "{$sender}: " . ($msg['message'] ?? '') . "\n";
            }
        } else {
            $context .= "No previous messages.\n";
        }
        $context .= "\nCURRENT CUSTOMER MESSAGE: " . $customerMessage . "\n";
        
        return $context;
    }

    /**
     * Build AI prompt for Gemini
     */
    private function buildPrompt($context, $customerMessage)
    {
        $systemPrompt = "You are a customer support AI. Respond professionally and helpfully in 2-3 sentences.

GUIDELINES:
- Be polite and empathetic
- Only use information from the context
- If info is missing, acknowledge it and suggest next steps
- Don't make promises about delivery times
- Keep response concise

{$context}

Respond to the customer's message:";

        return $systemPrompt;
    }

    /**
     * Call Gemini API using cURL
     */
    private function callGeminiAPI($prompt)
    {
        $url = "{$this->baseUrl}/models/gemini-2.5-flash:generateContent?key={$this->apiKey}";
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.7,
                'maxOutputTokens' => 1024,  // Increased from 500
                'topP' => 0.8,
                'topK' => 10
            ],
            'safetySettings' => [
                [
                    'category' => 'HARM_CATEGORY_HARASSMENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_HATE_SPEECH',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ],
                [
                    'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                    'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
                ]
            ]
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json'
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("CURL Error: " . $error);
        }

        if ($httpCode !== 200) {
            $errorDetails = json_decode($response, true);
            $errorMessage = isset($errorDetails['error']['message']) ? 
                          $errorDetails['error']['message'] : $response;
            throw new Exception("Gemini API returned HTTP " . $httpCode . ": " . $errorMessage);
        }

        return json_decode($response, true);
    }

    /**
     * Parse Gemini API response
     */
    private function parseResponse($response)
    {
        // Check for various error conditions first
        if (!isset($response['candidates']) || empty($response['candidates'])) {
            throw new Exception("No candidates in Gemini API response");
        }

        $candidate = $response['candidates'][0];
        
        // Check finish reason
        $finishReason = $candidate['finishReason'] ?? 'UNKNOWN';
        
        // Handle different finish reasons
        switch ($finishReason) {
            case 'SAFETY':
                return "I apologize, but I cannot generate a response for this query. Please rephrase your question or contact our support team directly.";
                
            case 'MAX_TOKENS':
                // Try to extract partial response if available
                if (isset($candidate['content']['parts'][0]['text'])) {
                    $text = trim($candidate['content']['parts'][0]['text']);
                    if (!empty($text)) {
                        // Return the partial response with an ellipsis
                        return $text . "... Please contact our support team for complete assistance.";
                    }
                }
                // If no partial text, return a helpful message
                return "I apologize for the incomplete response. Your query requires more detailed information than I can provide in this format. Please contact our support team directly for comprehensive assistance.";
                
            case 'RECITATION':
                return "I apologize, but I cannot provide this specific response. Please contact our support team for assistance.";
                
            case 'STOP':
                // Normal completion
                break;
                
            default:
                error_log("Unexpected finish reason: " . $finishReason);
        }
        
        // Extract the text content
        if (!isset($candidate['content']['parts'][0]['text'])) {
            throw new Exception("No text content in Gemini API response. Response: " . json_encode($response));
        }

        $text = $candidate['content']['parts'][0]['text'];
        
        // Clean up the response
        $text = trim($text);
        
        // Remove any markdown formatting
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text); // Bold
        $text = preg_replace('/\*(.*?)\*/', '$1', $text); // Italic
        
        return $text;
    }

    /**
     * Test Gemini connectivity
     */
    public function testConnection()
    {
        try {
            $testPrompt = "Respond with exactly: 'Hello! I am Gemini AI assistant and I'm ready to help customers.'";
            
            $response = $this->callGeminiAPI($testPrompt);
            $message = $this->parseResponse($response);
            
            return [
                'success' => true,
                'message' => 'Gemini AI is working correctly',
                'test_response' => $message
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Gemini AI connection failed: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ];
        }
    }
}
?>