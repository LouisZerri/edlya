<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Select extends Component
{
    public function __construct(
        public string $name,
        public array $options,
        public ?string $label = null,
        public ?string $value = null,
        public ?string $placeholder = null,
        public bool $required = false,
    ) {}

    public function render()
    {
        return view('components.form.select');
    }
}