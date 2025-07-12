<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [

            'text'   => ['nullable', 'string', 'max:5000',
                'required_without_all:images,video,audio,files'],


            'images'     => ['nullable', 'array', 'max:10',
                'required_without_all:video,audio,files'],
            'images.*'   => ['bail', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],


            'video'      => ['nullable', 'file',
                'mimetypes:video/mp4,video/webm',
                'max:51200',          // 50 MB
                'required_without_all:images,audio,files'],


            'audio' => ['nullable', 'file',
                'mimetypes:audio/mpeg,audio/wav,audio/mp4,audio/x-m4a,audio/aac',
                'max:15000',
                'required_without_all:images,video,files'],



            'files'     => ['nullable', 'array', 'max:5',
                'required_without_all:images,video,audio'],
            'files.*'   => ['bail', 'file', 'mimes:pdf,doc,docx,zip,txt', 'max:10240'],
        ];

    }

}
