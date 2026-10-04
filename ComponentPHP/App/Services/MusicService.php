<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Song;
use App\Models\User;
use App\Templates\SQL\MusicTemplate;
use Core\Database\Services\DatabaseService;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;
use Core\Utility\Services\PathService;
use Core\Utility\Validators\Exceptions\ValidationException;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\IntOrStringIntValidator;
use Core\Utility\Validators\Types\StringValidator;

final class MusicService
{
    public function __construct(
        public readonly MusicTemplate $musicTemplate,
        public readonly DatabaseService $databaseService,
        public readonly UserService $userService,
        public readonly AuthService $authService,
    ){
    }

    public function uploadSong(Request $request): ?Song
    {       
        $file = $request->files['file'];
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $temporaryPath = $file['tmp_name'];
        $mimeType = mime_content_type($temporaryPath);

        if (!in_array($extension, ['mp3', 'wav'], true) || !in_array($mimeType, ['audio/mpeg'], true)) {
            unlink($temporaryPath);

            return null;
        }

        $requirements = [
            'title' => new StringValidator('title'),
            'artist' => new StringValidator('artist'),
            'minutes' => new IntOrStringIntValidator('minutes'),
            'seconds' => new IntOrStringIntValidator('seconds'),
        ];

        ValidatorService::validate($requirements, $request->post);

        $title = $requirements['title']->getValueWithDefault();
        if ($title instanceof ValidationException || $title === null) {
            return null;
        }

        $artist = $requirements['artist']->getValueWithDefault();
        if ($artist instanceof ValidationException || $artist === null) {
            return null;
        }

        $minutes = $requirements['minutes']->getValueWithDefault();
        if ($minutes instanceof ValidationException || $minutes === null) {
            return null;
        }

        $seconds = $requirements['seconds']->getValueWithDefault();
        if ($seconds instanceof ValidationException || $seconds === null) {
            return null;
        }

        $existingSong = $this->getSongByTitleAndArtist($title, $artist);
        if ($existingSong !== null) {
            return null;
        }

        $lookupKey = md5("{$title}{$artist}") . ".{$extension}";

        $createSongComponent = $this->musicTemplate
            ->get('add_song')
            ->fillAll([
                'title' => $title,
                'artist' => $artist,
                'added_by' => $this->authService->getUser()->id,
                'added_at' => DateTimeService::stringNow(),
                'duration' => self::durationToString($minutes, $seconds),
                'lookup_key' => $lookupKey,
            ])
        ;
        $this->databaseService->query($createSongComponent);

        move_uploaded_file(
            $file['tmp_name'],
            PathService::fromProjectDirectory('Public', 'Assets', $lookupKey),
        );

        return $this->getSongByTitleAndArtist($title, $artist);
    }

    public function getSongByTitleAndArtist(string $title, string $artist): ?Song
    {
        $findSongComponent = $this->musicTemplate
            ->get('get_song_by_title_artist')
            ->fill('title', $title, true)
            ->fill('artist', $artist, true)
        ;
        $statement = $this->databaseService->query($findSongComponent);

        return $this->databaseService->getOneOrNullResult($statement, Song::class, $this->formatDatabaseRow(...));
    }

    public function getSongById(int $id): ?Song
    {
        $getAllSongsComponent = $this->musicTemplate
            ->get('get_song_by_id')
            ->fill('id', $id)
        ;
        $statement = $this->databaseService->query($getAllSongsComponent);

        return $this->databaseService->getOneOrNullResult($statement, Song::class, $this->formatDatabaseRow(...));
    }

    /**
     * @return list<Song>
     */
    public function getAllSongs(): array
    {
        $getAllSongsComponent = $this->musicTemplate
            ->get('get_all_songs')
        ;
        $statement = $this->databaseService->query($getAllSongsComponent);

        return $this->databaseService->getResult($statement, Song::class, $this->formatDatabaseRow(...));
    }

    public function deleteSongById(int $id): Response
    {
        $song = $this->getSongById($id);
        if ($song === null) {
            return new Response('Failed to delete song as it could not be found', responseCode: 500);
        }

        $currentUser = $this->authService->getUser();
        if ($song->addedBy->id !== $currentUser->id) {
            return new Response('You cannot delete this song', responseCode: 403);
        }

        $deleteComponent = $this->musicTemplate
            ->get('delete_song_by_id')
            ->fill('id', $song->id)
        ;
        $this->databaseService->query($deleteComponent);

        return new Response("Successfully deleted song {$song->title} by {$song->artist}");
    }

    private static function durationToString(int $minutes, int $seconds): string
    {
        return "{$minutes}:{$seconds}";
    }

    private static function durationFromString(string $duration): int
    {
        [$minutes, $seconds] = explode(':', $duration);

        return (((int) $minutes) * 60) + (int) $seconds;
    }

    private function formatDatabaseRow(array $row): array
    {
        $row['addedBy'] = new User(
            $row['user_id'],
            $row['username'],
            DateTimeService::fromString($row['joined']),
            $row['role'],
        );

        $row['addedAt'] = DateTimeService::fromString($row['added_at']);
        $row['duration'] = self::durationFromString($row['duration']);

        return $row;
    }
}
