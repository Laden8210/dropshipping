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
            // Limit to last 10 messages to avoid token limits
            $recentMessages = array_slice($ticketData['messages'], -10);
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
        $systemPrompt = "You are an AI customer support assistant for a dropshipping e-commerce platform. Your role is to provide helpful, empathetic, and accurate responses to customer inquiries.

IMPORTANT GUIDELINES:
1. Always be polite, professional, and empathetic
2. Only provide accurate information based on the context provided
3. If you don't have specific information, acknowledge it and suggest next steps
4. For order-related questions, focus on the actual order status and tracking information
5. For product questions, provide general guidance but recommend contacting supplier for specifics
6. Keep responses concise but comprehensive (2-4 sentences typically)
7. Use a friendly, conversational tone
8. Don't make promises about specific delivery times unless clearly stated in order data

AVOID:
- Making promises you can't keep
- Providing incorrect order or tracking information
- Giving technical support for products beyond basic usage
- Suggesting refunds/returns without proper processes
- Sharing internal business information

RESPONSE STYLE:
- Professional but friendly tone
- Clear and helpful information
- Empathetic understanding of customer concerns
- Actionable next steps when possible

---

{$context}

---

Respond to the customer's message professionally and helpfully. Keep your response concise (2-4 sentences). Only use information from the context provided above.";

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
                'maxOutputTokens' => 500,
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
        if (!isset($response['candidates'][0]['content']['parts'][0]['text'])) {
            // Check if content was blocked
            if (isset($response['candidates'][0]['finishReason']) && 
                $response['candidates'][0]['finishReason'] === 'SAFETY') {
                return "I apologize, but I cannot generate a response for this query. Please rephrase your question or contact our support team directly.";
            }
            
            throw new Exception("Invalid response format from Gemini API: " . json_encode($response));
        }

        $text = $response['candidates'][0]['content']['parts'][0]['text'];
        
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