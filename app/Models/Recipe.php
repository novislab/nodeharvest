<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\RecipeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    /** @use HasFactory<RecipeFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'user_id',
        'preset_id',
        'location',
        'os_id',
        'software_id',
        'traffic_plan_id',
        'deploy_period',
        'ssh_key',
        'post_install_script',
        'post_install_callback',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'os_id' => 'integer',
            'software_id' => 'integer',
            'traffic_plan_id' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, Recipe>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
