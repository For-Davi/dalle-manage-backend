<?php

namespace App\DTO\Setting\Appearance;

use App\DTO\BaseDTO;

class UpdateSettingAppearanceDTO extends BaseDTO
{
    public function __construct(
        public int $title_page_color_default,
        public int $navbar_color_default,
        public int $navbar_icon_color_default,
        public int $side_menu_color_default_not_selected_item,
        public int $side_menu_color_default_selected_item,
        public int $side_menu_color_default_not_selected_icon,
        public int $side_menu_color_default_selected_icon,
        public ?string $title_page_color_code,
        public ?string $navbar_color_code,
        public ?string $navbar_icon_color_code,
        public ?string $side_menu_color_code_not_selected_item,
        public ?string $side_menu_color_code_selected_item,
        public ?string $side_menu_color_code_not_selected_icon,
        public ?string $side_menu_color_code_selected_icon
    ) {}

    public static function fromRequest($data): self
    {
        return new self(
            title_page_color_default: $data['titlePageColorDefault'],
            navbar_color_default: $data['navbarColorDefault'],
            navbar_icon_color_default: $data['navbarIconColorDefault'],
            side_menu_color_default_not_selected_item: $data['sideMenuColorDefaultNotSelectedItem'],
            side_menu_color_default_selected_item: $data['sideMenuColorDefaultSelectedItem'],
            side_menu_color_default_not_selected_icon: $data['sideMenuColorDefaultNotSelectedIcon'],
            side_menu_color_default_selected_icon: $data['sideMenuColorDefaultSelectedIcon'],
            title_page_color_code: $data['titlePageColorCode'],
            navbar_color_code: $data['navbarColorCode'],
            navbar_icon_color_code: $data['navbarIconColorCode'],
            side_menu_color_code_not_selected_item: $data['sideMenuColorCodeNotSelectedItem'],
            side_menu_color_code_selected_item: $data['sideMenuColorCodeSelectedItem'],
            side_menu_color_code_not_selected_icon: $data['sideMenuColorCodeNotSelectedIcon'],
            side_menu_color_code_selected_icon: $data['sideMenuColorCodeSelectedIcon'],
        );
    }
}
