<?php

namespace App\Services;

use App\DTO\Feedback\CreateFeedbackDTO;
use App\DTO\Image\CreateImageDTO;
use App\Repositories\FeedbackRepository;
use App\Repositories\ImageRepository;
use Illuminate\Support\Facades\Storage;

class FeedbackService
{
    public function __construct(
        protected FeedbackRepository $feedbackRepository,
        protected ImageRepository $imageRepository
    ) {}

    public function create($request)
    {
        $savedImage = null;

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $this->savePathImage($image);

                $imageDTO = CreateImageDTO::fromRequest([
                    'url' => $path,
                    'name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                ]);

                $savedImage = $this->imageRepository->create($imageDTO->toArray());
            }
        }

        $feedbackDTO = CreateFeedbackDTO::fromRequest([
            'text' => $request->text,
            'image_id' => $savedImage === null ? null : $savedImage->id,
        ]);

        return $this->feedbackRepository->create($feedbackDTO->toArray());
    }

    private function savePathImage($image)
    {
        if (! Storage::disk('public')->exists('images')) {
            Storage::disk('public')->makeDirectory('images');
        }

        $path = $image->store('images', 'public');

        return Storage::url($path);
    }
}
