<?php

return [
    'api_key' => env('ANTHROPIC_API_KEY'),
    'model' => 'claude-sonnet-4-20250514',
    'max_tokens' => 16000,
    'timeout' => 180,
];