<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use Core\Components\Models\AbstractTemplate;
use Core\Components\Models\Component;
use Core\Utility\Services\PathService;

final class FormTemplate extends AbstractTemplate
{
    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(PathService::fromProjectDirectory('App', 'Components', 'HTML', 'form.html'), true);
    }

    public function getRegisterForm(): Component
    {
        $form = $this
            ->get('form')
            ->fillAll([
                'route' => '/register',
                'method' => 'POST',
                'title' => 'Register',
                'submit' => 'Register',
                'rows' => $this->getAuthFormRows(),
            ])
        ;

        $form
            ->fill('lower_text', $this->get('lower_text')->fill('text', "Or <a href='/login'>Login</a>", raw: true))
        ;

        return $form;
    }
    
    public function getLoginForm(): Component
    {
        $form = $this
            ->get('form')
            ->fillAll([
                'route' => '/login',
                'method' => 'POST',
                'title' => 'Login',
                'submit' => 'Login',
                'rows' => $this->getAuthFormRows(),
            ])
        ;

        $form
            ->fill('lower_text', $this->get('lower_text')->fill('text', "Or <a href='/register'>Register</a>", raw: true))
        ;

        return $form;
    }

    public function getUploadForm(): Component
    {
        $rows = $this->stack('form_row', [
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
            ->get('dual_form_row')
            ->fillAll([
                'label' => 'Duration',
                'field_1' => 'minutes',
                'type_1' => 'number',
                'properties_1' => 'min="0" step="1" required',
                'field_2' => 'seconds',
                'type_2' => 'number',
                'properties_2' => 'min="0" step="1" required',
            ], true)
        ;

        $rows[] = $this
            ->get('form_row')
            ->fillAll([
                'field' => 'file',
                'label' => 'File',
                'type' => 'file',
                'properties' => 'required',
            ])
        ;

        return $this
            ->get('form')
            ->fillAll([
                'route' => '/songs/upload',
                'method' => 'POST',
                'title' => 'Upload',
                'submit' => 'Upload',
                'properties' => 'enctype="multipart/form-data"',
                'rows' => $this->collect($rows),
            ], true)
        ;
    }

    private function getAuthFormRows(): Component
    {
        return $this->quickCollect('form_row', [
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
    }
}
