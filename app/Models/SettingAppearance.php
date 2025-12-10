<?php

namespace App\Models;

use App\Scopes\EnterpriseScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingAppearance extends Model
{
    use HasFactory;

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

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class);
    }
}
