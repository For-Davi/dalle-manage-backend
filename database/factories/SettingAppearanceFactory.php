<?php

namespace Database\Factories;

use App\Models\SettingAppearance;
use Illuminate\Database\Eloquent\Factories\Factory;

class SettingAppearanceFactory extends Factory
{
    protected $model = SettingAppearance::class;

    public function definition()
    {
        return [
            'title_page_color_default' => 1,
            'navbar_color_default' => 1,
            'navbar_icon_color_default' => 1,
            'side_menu_color_default_not_selected_item' => 1,
            'side_menu_color_default_selected_item' => 1,
            'side_menu_color_default_not_selected_icon' => 1,
            'side_menu_color_default_selected_icon' => 1,
            'title_page_color_code' => null,
            'navbar_color_code' => null,
            'navbar_icon_color_code' => null,
            'side_menu_color_code_not_selected_item' => null,
            'side_menu_color_code_selected_item' => null,
            'side_menu_color_code_not_selected_icon' => null,
            'side_menu_color_code_selected_icon' => null,
        ];
    }
}
