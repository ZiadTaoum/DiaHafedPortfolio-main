<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class PortfolioItem extends Model {
    protected $guarded = ['id'];
    protected function casts(): array { return ['data'=>'array', 'is_visible'=>'boolean']; }
    public function section(): BelongsTo { return $this->belongsTo(Section::class); }
}
