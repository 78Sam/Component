<?php

declare(strict_types=1);

namespace App\Templates\SQL;

use Core\Components\Models\Templates\AbstractTemplate;
use Core\Components\Models\Components\SQLComponent;
use Core\Utility\Services\DateTimeService;

final class MusicTemplate extends AbstractTemplate // TODO: SQLAbstractTemplate to add default SQLComponent::class
{
    public const string SONGS_FILE = 'App/Components/SQL/songs.sql';

    #[\Override]
    protected function loadFiles(): void
    {
        $this->loadFile(self::SONGS_FILE);
    }

    public function getAddSongQuery(string $title, string $artist, int $id, string $duration, string $lookupKey): SQLComponent
    {
        return $this
            ->get('add_song', SQLComponent::class, self::SONGS_FILE)
            ->fillAll([
                'title' => $title,
                'artist' => $artist,
                'added_by' => $id,
                'added_at' => DateTimeService::stringNow(),
                'duration' => $duration,
                'lookup_key' => $lookupKey,
            ])
        ;
    }

    public function getSongByTitleAndArtistQuery(string $title, string $artist): SQLComponent
    {
        return $this
            ->get('get_song_by_title_artist', SQLComponent::class, self::SONGS_FILE)
            ->fill('title', $title)
            ->fill('artist', $artist)
        ;
    }

    public function getSongByIdQuery(int $id): SQLComponent
    {
        return $this
            ->get('get_song_by_id', SQLComponent::class, self::SONGS_FILE)
            ->fill('id', $id)
        ;
    }

    public function getAllSongsQuery(): SQLComponent
    {
        return $this
            ->get('get_all_songs', SQLComponent::class, self::SONGS_FILE)
        ;
    }

    public function getDeleteSongByIdQuery(int $id): SQLComponent
    {
        return $this
            ->get('delete_song_by_id', SQLComponent::class, self::SONGS_FILE)
            ->fill('id', $id)
        ;
    }
}
