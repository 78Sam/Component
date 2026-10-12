<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use App\Models\Song;
use App\Models\User;
use App\Services\MusicService;
use Core\Components\Models\AbstractTemplate;
use Core\Components\Models\Component;
use Core\Components\Services\ComponentService;
use Core\Utility\Services\PathService;

final class MusicTemplate extends AbstractTemplate
{
    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(PathService::fromProjectDirectory('App', 'Components', 'HTML', 'upload.html'), true);
        $this->loadFile(PathService::fromProjectDirectory('App', 'Components', 'HTML', 'songs.html'), true);
    }

    public function getSong(Song $song, User $user): Component
    {
        $songComponent = $this
            ->get('song')
            ->fillAll([
                'id' => $song->id,
                'title' => $song->title,
                'artist' => $song->artist,
                'duration' => MusicService::secondsDurationToString($song->duration),
                'name' => $song->addedBy->username,
            ])
        ;

        if ($song->addedBy->id === $user->id) {
            $songComponent->fill('delete', 'Delete');
        }

        return $songComponent;
    }
}
