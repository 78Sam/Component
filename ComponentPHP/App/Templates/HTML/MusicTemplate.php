<?php

declare(strict_types=1);

namespace App\Templates\HTML;

use App\Models\Song;
use App\Models\User;
use App\Services\MusicService;
use Core\Components\Models\Templates\AbstractTemplate;
use Core\Components\Models\Components\HTMLComponent;
use Core\Utility\Services\PathService;

final class MusicTemplate extends AbstractTemplate
{
    public const string SONGS_FILE = 'App/Components/HTML/songs.html';

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(self::SONGS_FILE);
    }

    public function getSong(Song $song, User $user): HTMLComponent
    {
        $songComponent = $this
            ->get('song', HTMLComponent::class, self::SONGS_FILE)
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

    /**
     * @param list<Song> $songs
     */
    public function getAllSongs(array $songs, User $user): HTMLComponent
    {
        return $this->collect(array_map(fn(Song $song): HTMLComponent => $this->getSong($song, $user), $songs), HTMLComponent::class);
    }

    public function getPlayer(Song $song): HTMLComponent
    {
        return $this
            ->get('player', HTMLComponent::class, self::SONGS_FILE)
            ->fill('source', PathService::combineSegments('Assets', 'Music', $song->lookupKey))
            ->fill('title', $song->title)
            ->fill('artist', $song->artist)
        ;
    }
}
