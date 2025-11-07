<?php

namespace App\Models;

use App\Policies\TaskPolicy;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[UsePolicy(TaskPolicy::class)]
class Task extends Model
{

    use SoftDeletes, HasFactory;
    //
    protected $fillable = ['name', 'description', 'user_id','status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
