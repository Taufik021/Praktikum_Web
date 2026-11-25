<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $media = $this->route('media');
        $mediaId = is_object($media) ? $media->getKey() : $media;

        return [
            //
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                'unique:media,title,' . $mediaId,
            ],
            'type' => 'sometimes|required|string',
            'genre' => 'sometimes|required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.string' => 'Judul media harus berupa teks.',
            'title.max' => 'Judul media maksimal 255 karakter.',
            'title.unique' => 'Judul media sudah digunakan.',
            'type.required' => 'Jenis media wajib diisi.',
            'type.string' => 'Jenis media harus berupa teks.',
            'genre.required' => 'Genre media wajib diisi.',
            'genre.string' => 'Genre media harus berupa teks.',
        ];
    }
}
