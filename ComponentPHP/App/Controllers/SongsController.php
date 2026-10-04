<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Middleware\Auth;
use App\Services\MusicService;
use App\Templates\HTML\FormTemplate;
use App\Templates\HTML\MusicTemplate;
use App\Templates\HTML\RootTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\Response;

#[Auth]
final class SongsController extends AbstractController
{
    public function __construct(
        public readonly RootTemplate $rootTemplate,
        public readonly MusicTemplate $musicTemplate,
        public readonly FormTemplate $formTemplate,
        public readonly MusicService $musicService,
    ) {
    }

    #[Route(['/songs'], 'app_viewAllSongs')]
    public function viewSongs(): Response
    {
        $songs = $this->musicService->getAllSongs();

        $songComponents = [];
        foreach ($songs as $song) {
            $songComponents[] = $this->musicTemplate
                ->get('song')
                ->fill('title', $song->title)
                ->fill('artist', $song->artist)
            ;
        }

        $songsComponent = $this->musicTemplate
            ->get('songs')
            ->fill('songs', $this->musicTemplate->collect($songComponents))
        ;

        return new Response($this->rootTemplate->getApp($songsComponent));
    }

    #[Route(['/songs/upload'], 'app_uploadSong')]
    public function uploadSong(Request $request): Response
    {
        if ($request->method === Request::METHOD_POST) {
            $this->musicService->uploadSong($request);
        }

        return new Response($this->rootTemplate->getApp($this->formTemplate->getUploadForm()));
    }

    #[Route(['/songs/{id}/view'], 'app_viewSong')]
    public function viewSong(int $id): Response
    {
        $song = $this->musicService->getSongById($id);

        return new Response($this->musicTemplate->get('song')->fillAll([
            'title' => $song->title,
            'artist' => $song->artist,
        ]));
    }

    #[Route(['/songs/{id}/delete'], 'app_deleteSong')]
    public function deleteSong(int $id): Response
    {
        return $this->musicService->deleteSongById($id);
    }
}
