<?php

namespace App\Models;

use App\Contracts\HasCacheTags;
use App\Scopes\EnterpriseScope;
use App\Traits\InvalidatesCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingAppearance extends Model implements HasCacheTags
{
    use HasFactory, InvalidatesCache;

    protected $table = 'setting_appearance';

    protected $fillable = [
        'title_page_color_default',
        'navbar_color_default',
        'navbar_icon_color_default',
        'side_menu_color_default_not_selected_item',
        'side_menu_color_default_selected_item',
        'side_menu_color_default_not_selected_icon',
        'side_menu_color_default_selected_icon',
        'title_page_color_code',
        'navbar_color_code',
        'navbar_icon_color_code',
        'side_menu_color_code_not_selected_item',
        'side_menu_color_code_selected_item',
        'side_menu_color_code_not_selected_icon',
        'side_menu_color_code_selected_icon',
        'enterprise_id',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new EnterpriseScope);
    }

    public function getCacheTags(): array
    {
        if (! $this->enterprise_id) {
            return [];
        }

        return [
            "setting_appearance:enterprise:{$this->enterprise_id}",
        ];
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
