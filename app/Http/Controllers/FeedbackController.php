<?php

namespace App\Http\Controllers;

use App\Http\Requests\Feedback\CreateFeedbackRequest;
use App\Services\FeedbackService;

class FeedbackController extends BaseController
{
    public function __construct(
        private FeedbackService $service,
    ) {}

    public function store(CreateFeedbackRequest $request)
    {
        return $this->safeTransaction(function () use ($request) {
            $this->service->create($request);

            return response()->json(['message' => 'Sugestão enviada'], 201);
        }, 'Erro ao enviar sugestão', $request);
    }
}
