<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | AI Description Service
    |--------------------------------------------------------------------------
    |
    | Provider: "mock" (Standard), "openai" oder "groq"
    |
    | GitHub Copilot wird NICHT unterstützt:
    | Copilot hat keine öffentliche API für Textgenerierung und kein PHP-SDK.
    | Es funktioniert nur als IDE-Plugin (PhpStorm, VS Code etc.).
    |
    | Umstellung auf echte KI:
    |   AI_PROVIDER=openai    (oder "groq")
    |   OPENAI_API_KEY=sk-...
    |   OPENAI_MODEL=gpt-4o-mini
    |   GROQ_API_KEY=gsk_...
    |   GROQ_MODEL=llama-3.3-70b-versatile
    |
    */

    'ai' => [
        'provider' => env('AI_PROVIDER', 'mock'),

        // OpenAI-Konfiguration
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'model'   => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'url'     => 'https://api.openai.com/v1/chat/completions',
        ],

        // Groq-Konfiguration
        'groq' => [
            'api_key' => env('GROQ_API_KEY'),
            'model'   => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
            'url'     => 'https://api.groq.com/openai/v1/chat/completions',
        ],
    ],

];
