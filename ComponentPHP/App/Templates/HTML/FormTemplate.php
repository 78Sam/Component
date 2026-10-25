<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Models\AbstractTemplate;
use Core\Components\Models\HTMLComponent;

final class FormTemplate extends AbstractTemplate
{
    public const string FORM_FILE = 'App/Components/HTML/form.html';

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(self::FORM_FILE);
    }

    public function getRegisterForm(): HTMLComponent
    {
        $form = $this
            ->get('form', HTMLComponent::class, self::FORM_FILE)
            ->fillAll([
                'route' => '/register',
                'method' => 'POST',
                'title' => 'Register',
                'submit' => 'Register',
                'rows' => $this->getAuthFormRows(),
            ])
        ;

        $lowerText = $this
            ->get('lower_text', HTMLComponent::class, self::FORM_FILE)
            ->fill('text', 'Or <a href="/login">Login</a>', ['raw' => true])
        ;

        $form
            ->fill('lower_text', $lowerText)
        ;

        return $form;
    }
    
    public function getLoginForm(): HTMLComponent
    {
        $form = $this
            ->get('form', HTMLComponent::class, self::FORM_FILE)
            ->fillAll([
                'route' => '/login',
                'method' => 'POST',
                'title' => 'Login',
                'submit' => 'Login',
                'rows' => $this->getAuthFormRows(),
            ])
        ;

        $lowerText = $this
            ->get('lower_text', HTMLComponent::class, self::FORM_FILE)
            ->fill('text', 'Or <a href="/register">Register</a>', ['raw' => true])
        ;

        $form
            ->fill('lower_text', $lowerText)
        ;

        return $form;
    }

    public function getUploadForm(?string $error = null): HTMLComponent
    {
        $rows = $this->stack('form_row', HTMLComponent::class, self::FORM_FILE, [
            [
                'field' => 'title',
                'label' => 'Title',
                'type' => 'text',
                'properties' => 'required',
            ],
            [
                'field' => 'artist',
                'label' => 'Artist',
                'type' => 'text',
                'properties' => 'required',
            ],
        ]);

        $rows[] = $this
            ->get('dual_form_row', HTMLComponent::class, self::FORM_FILE)
            ->fillAll([
                'label' => 'Duration',
                'field_1' => 'minutes',
                'type_1' => 'number',
                'properties_1' => 'min="0" step="1" required',
                'field_2' => 'seconds',
                'type_2' => 'number',
                'properties_2' => 'min="0" step="1" required',
            ], ['raw' => true])
        ;

        $rows[] = $this
            ->get('form_row', HTMLComponent::class, self::FORM_FILE)
            ->fillAll([
                'field' => 'file',
                'label' => 'File',
                'type' => 'file',
                'properties' => 'required',
            ])
        ;

        $form = $this
            ->get('form', HTMLComponent::class, self::FORM_FILE)
            ->fillAll([
                'route' => '/songs/upload',
                'method' => 'POST',
                'title' => 'Upload',
                'submit' => 'Upload',
                'properties' => 'enctype="multipart/form-data"',
                'rows' => $this->collect($rows, HTMLComponent::class),
            ], ['raw' => true])
        ;
        if ($error !== null) {
            $form->fill('error', $this->get('error', HTMLComponent::class, self::FORM_FILE)->fill('error', $error));
        }

        return $form;
    }

    private function getAuthFormRows(): HTMLComponent
    {
        $rows = $this->stack('form_row', HTMLComponent::class, self::FORM_FILE, [
            [
                'field' => 'username',
                'label' => 'Username',
                'type' => 'text',
                'properties' => 'required'
            ],
            [
                'field' => 'password',
                'label' => 'Password',
                'type' => 'password',
                'properties' => 'required'
            ],
        ]);

        return $this->collect($rows, HTMLComponent::class);
    }
}
