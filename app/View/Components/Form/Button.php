<?php

namespace App\View\Components\Form;

use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(
        public string $type = 'submit',
        public string $variant = 'primary',
    ) {}

    public function render()
    {
        return view('components.form.button');
    }
}