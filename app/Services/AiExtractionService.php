<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiExtractionService
{
    /**
     * Extract order-related data from a chat message using an LLM.
     */
    public function extractOrderData(Message $message): void
    {
        $apiKey = config('services.openai.api_key');

        if (!$apiKey) {
            // Fallback: basic regex extraction when no API key configured
            $this->extractWithRegex($message);
            return;
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(5)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o-mini',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->getSystemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => $message->body,
                        ],
                    ],
                    'response_format' => ['type' => 'json_object'],
                    'temperature' => 0.1,
                    'max_tokens' => 500,
                ]);

            if ($response->successful()) {
                $data = json_decode($response->json('choices.0.message.content'), true);

                if ($data && $this->hasUsefulData($data)) {
                    $message->update([
                        'ai_extraction' => $data,
                        'is_ai_processed' => true,
                    ]);
                }
            } else {
                Log::warning('OpenAI API error', ['status' => $response->status(), 'response' => $response->body()]);
                $this->extractWithRegex($message);
            }
        } catch (\Exception $e) {
            Log::warning('AI extraction failed', ['error' => $e->getMessage(), 'message_id' => $message->id]);
            // Fallback to regex
            $this->extractWithRegex($message);
        }
    }

    /**
     * Regex-based extraction fallback for when OpenAI is unavailable.
     */
    private function extractWithRegex(Message $message): void
    {
        $text = $message->body;
        $data = [];

        // Phone number extraction (Kosovo/Albanian format)
        if (preg_match('/\+?3[89]\d[\s\-]?\d{2}[\s\-]?\d{3}[\s\-]?\d{3}/', $text, $match)) {
            $data['phone_number'] = trim($match[0]);
        }
        if (preg_match('/04[3-9][\s\-]?\d{3}[\s\-]?\d{3}/', $text, $match)) {
            $data['phone_number'] = trim($match[0]);
        }

        // City extraction (common Kosovo cities)
        $cities = ['Prishtina', 'Pristina', 'Prishtinë', 'Prizren', 'Peja', 'Pejë', 'Gjilan', 'Ferizaj', 'Mitrovica', 'Mitrovicë', 'Gjakova', 'Gjakovë', 'Podujeva', 'Podujevë', 'Vushtrri', 'Suhareka', 'Suharekë', 'Rahovec', 'Drenas', 'Gllogovc', 'Lipjan', 'Malisheva', 'Malishevë', 'Kaçanik', 'Skenderaj', 'Viti', 'Deçan', 'Istog', 'Klinë', 'Fushë Kosovë'];
        foreach ($cities as $city) {
            if (stripos($text, $city) !== false) {
                // Normalize spelling
                $data['city'] = (strtolower($city) === 'pristina') ? 'Prishtina' : $city;
                break;
            }
        }

        // Address patterns (Albanian: rr., lagja, nr., ne, në, te, adresa)
        if (preg_match('/(?:rr\.?\s*|rruga\s+|adresa\s+|ne\s+|në\s+|te\s+|të\s+)([a-zA-Z\s]{4,30})(?:,|\.|\d|$)/iu', $text, $match)) {
            $street = trim($match[1]);
            // Ignore if it accidentally grabbed the city name or generic words
            if (!in_array(ucfirst(strtolower($street)), $cities) && !str_starts_with(strtolower($street), 'kete numer')) {
                $data['street_address'] = $street;
            }
        }
        if (preg_match('/(?:lagja?\s+|lagjia\s+)([^\d,;\.]+)/iu', $text, $match)) {
            $data['neighborhood'] = trim($match[1]);
        }

        // Quantity extraction
        if (preg_match('/\b(?:du|dua|porosi|doj|want|buy|order|bon)\s+(\d{1,2})\b/iu', $text, $match)) {
            $data['quantity'] = (int)$match[1];
        }

        // Product intent (keywords including Albanian slang)
        $orderKeywords = ['dua', 'du', 'doj', 'porosi', 'order', 'want', 'buy', 'blerë', 'bli', 'bon'];
        foreach ($orderKeywords as $kw) {
            if (stripos($text, $kw) !== false) {
                // Try to extract 1-5 words after the keyword as the product name
                if (preg_match('/\b' . preg_quote($kw, '/') . '\b\s+(?:\d{1,2}|a|an|një|nje|some|nji)?\s*([a-zA-Z\s]{3,25})(?:\.|\,|my address|$|ne\b|në\b|te\b|të\b|adres|rr\b)/iu', $text, $match)) {
                    $data['product_intent'] = trim($match[1]);
                } else {
                    $data['product_intent'] = 'Unknown Product';
                }
                break;
            }
        }

        if (!empty($data)) {
            $message->update([
                'ai_extraction' => $data,
                'is_ai_processed' => true,
            ]);
        }
    }

    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
You are a backend data extraction engine for a Kosovo-based e-commerce platform.
Analyze the provided chat transcript and extract any relevant order and shipping details.
The conversation may be in Albanian, English, or a mix of both.

Return ONLY a valid JSON object. Do not include markdown formatting (like ```json), conversational text, or explanations. Use null for missing data.

Expected JSON Schema:
{
  "customer_name": "string or null",
  "phone_number": "string or null",
  "city": "string or null",
  "neighborhood": "string or null",
  "street_address": "string or null",
  "product_intent": "string describing what they want to buy, or null",
  "quantity": "number or null",
  "has_order_intent": boolean
}

EXTRACTION RULES & HINTS:
- Albanian Address Patterns: "rr.", "rruga" = street; "lagja", "lagjia" = neighborhood; "nr." = number.
- Common Cities: Prishtina, Prizren, Peja, Gjilan, Ferizaj, Mitrovica, Gjakova, etc.
- Phone Numbers: Look for formats starting with +383, 00383, or local prefixes like 044, 049, 045, 048, 043, 046. Customers may write them with or without spaces (e.g., 044 123 456).
- Order Intent: Set "has_order_intent" to true ONLY if the customer explicitly indicates a desire to purchase or provides delivery details.
PROMPT;
    }

    private function hasUsefulData(array $data): bool
    {
        return !empty(array_filter([
            $data['customer_name'] ?? null,
            $data['phone_number'] ?? null,
            $data['city'] ?? null,
            $data['street_address'] ?? null,
            $data['product_intent'] ?? null,
        ]));
    }
}
