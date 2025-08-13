<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class StorePostRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'text' => ['nullable', 'string', 'max:90000'],
            'visibility' => ['required', 'in:public,private,friends'],
            'is_archived'=> ['sometimes', 'boolean'],
            'images'   => ['nullable', 'array', 'max:10'],
            'images.*' => ['bail', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],

            'video' => ['nullable', 'file', 'mimetypes:video/mp4,video/webm', 'max:204800'],

            'audio' => ['nullable', 'file', 'mimetypes:audio/mpeg,audio/wav,audio/mp4,audio/x-m4a,audio/aac', 'max:25600'],

            'files'   => ['nullable', 'array', 'max:5'],
            'files.*' => [
                'file',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/zip,application/octet-stream',
                'max:10240',
            ],
        ];
    }


    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $hasText   = $this->filled('text');
            $hasImages = $this->hasFile('images');
            $hasVideo  = $this->hasFile('video');
            $hasAudio  = $this->hasFile('audio');
            $hasFiles  = $this->hasFile('files');

            if (! ($hasText || $hasImages || $hasVideo || $hasAudio || $hasFiles)) {
                $validator->errors()->add(
                    'content',
                    __('حداقل یکی از فیلدهای متن یا رسانه (عکس، ویدیو، صوت یا فایل) باید پر باشد.')
                );
            }
        });
    }
}
