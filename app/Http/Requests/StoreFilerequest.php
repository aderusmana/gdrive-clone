<?php

namespace App\Http\Requests;

use App\Models\File;
use Illuminate\Support\Facades\Auth;

class StoreFilerequest extends ParentIdBaseRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'files.*' => [
                'required',
                'file',
                function ($attribute, $value, $fail) {
                    /** @var $value \Illuminate\Http\UploadedFile */
                    $file = File::query()->where('name', $value->getClientOriginalName())
                        ->where('created_by', Auth::id())
                        ->where('parent_id', $this->parent_id)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($file) {
                        $fail('File "' . $value->getClientOriginalName() . '" not exists.');
                    }
                }
            ]
        ]);
    }
}