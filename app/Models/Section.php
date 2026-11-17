<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Section extends Model {
    protected $guarded = ['id'];
    protected function casts(): array { return ['data'=>'array', 'is_visible'=>'boolean']; }
    public function items(): HasMany { return $this->hasMany(PortfolioItem::class)->orderBy('position')->orderBy('id'); }
}
