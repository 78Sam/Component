<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Song;
use App\Models\User;
use App\Templates\SQL\MusicTemplate;
use Core\Database\Services\DatabaseService;
use Core\Routing\Models\Request;
use Core\Utility\Services\PathService;
use Core\Utility\Validators\Exceptions\ValidationException;
use Core\Utility\Validators\Services\ValidatorService;
use Core\Utility\Validators\Types\IntOrStringIntValidator;
use Core\Utility\Validators\Types\StringValidator;
use DateTimeImmutable;

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

        $existingSong = $this->getSong($title, $artist);
        if ($existingSong !== null) {
            return null;
        }

        $lookupKey = md5("{$title}{$artist}") . ".{$extension}";

        $createSongComponent = $this->musicTemplate
            ->get('add_song')
            ->fillAll([
                'title' => $title,
                'artist' => $artist,
                'added_by' => (string) $this->authService->getUser()->id,
                'added_at' => DateTimeService::stringNow(),
                'duration' => "{$minutes}:{$seconds}",
                'lookup_key' => $lookupKey,
            ])
        ;
        $this->databaseService->query($createSongComponent);

        move_uploaded_file(
            $file['tmp_name'],
            PathService::fromProjectDirectory('Public', 'Assets', $lookupKey),
        );

        return $this->getSong($title, $artist);
    }

    public function getSong(string $title, string $artist): ?Song
    {
        $findSongComponent = $this->musicTemplate
            ->get('get_song_by_title_artist')
            ->fill('title', $title, true)
            ->fill('artist', $artist, true)
        ;

        $statement = $this->databaseService->query($findSongComponent);

        $song = $this->databaseService->getOneOrNullResult($statement, Song::class, function(array $row): array {
            $row['user'] = $this->userService->getUserById($row['id']);
            $row['added_at'] = DateTimeService::fromString($row['added_at']);

            [$minutes, $seconds] = explode(':', $row['duration']);
            $row['duration'] = (((int) $minutes) * 60) + (int) $seconds;

            return $row;
        });

        return $song;

        $song = $this->databaseService->getOneOrNullArrayResult($statement);
        if ($song === null) {
            return null;
        }

        [$minutes, $seconds] = explode(':', $song['duration']);
        $duration = (((int) $minutes) * 60) + (int) $seconds;

        return new Song(
            $song['id'],
            $song['title'],
            $song['artist'],
            $this->userService->getUserById($song['userId']), // TODO: Could just build the user but this is easier
            DateTimeService::fromString($song['added_at']),
            $duration,
            $song['lookup_key'],
        );
    }
}
