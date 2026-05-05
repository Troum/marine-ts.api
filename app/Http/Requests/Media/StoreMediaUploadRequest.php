<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            // KB; видео для hero допускает до ~80 MiB (проверьте PHP upload_max_filesize / post_max_size).
            'file' => ['required', 'file', 'max:81920', 'mimes:pdf,jpg,jpeg,png,webp,mp4,webm,mov,quicktime'],
        ];
    }
}
