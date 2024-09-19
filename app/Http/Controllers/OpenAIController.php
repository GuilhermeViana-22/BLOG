<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use app\Services\OpenAI\OpenAIService;
use app\Http\Resquests\generateTextRequest;

class OpenAIController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function generateText(generateTextRequest $request)
    {

        $prompt = $request->input('prompt');
        $generatedText = $this->openAIService->generateText($prompt);

        return response()->json([
            'generated_text' => $generatedText,
        ]);
    }
}
