<?php



namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasCreaterAndUpdaterTrait
{
    protected static function bootHasCreaterAndUpdaterTrait()
    {
        static::creating(function ($model) {
            $model->created_by = Auth::id();
            $model->updated_by = Auth::id();
        });

        static::updating(function ($model) {
            $model->updated_by = Auth::id();
        });
    }
}
