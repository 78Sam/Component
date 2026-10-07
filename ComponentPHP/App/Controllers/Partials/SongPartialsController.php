<?php

declare(strict_types=1);

namespace App\Controllers\Partials;

use App\Exceptions\Music\UploadException;
use App\Middleware\Auth;
use App\Middleware\Partial;
use App\Models\Song;
use App\Services\MusicService;
use App\Templates\HTML\FormTemplate;
use App\Templates\HTML\MusicTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Models\Request;
use Core\Routing\Models\Responses\RedirectResponse;
use Core\Routing\Models\Responses\Response;
use Core\Sessions\Services\SessionService;
use Core\Utility\Services\PathService;

#[Auth]
final class SongPartialsController extends AbstractController
{
    public function __construct(
        public readonly MusicTemplate $musicTemplate,
        public readonly FormTemplate $formTemplate,
        public readonly MusicService $musicService,
    ) {
    }

    #[Partial]
    #[Route(['/songs'], 'app_viewAllSongs')]
    public function viewSongs(): Response
    {
        $songs = $this->musicService->getAllSongs();

        $songComponents = [];
        foreach ($songs as $song) {
            $songComponents[] = $this->musicTemplate->getSong($song);
        }

        $songsComponent = $this->musicTemplate
            ->get('songs')
            ->fill('songs', $this->musicTemplate->collect($songComponents))
        ;

        return new Response($songsComponent);
    }

    #[Route(['/songs/upload'], 'app_uploadSong')]
    public function uploadSong(Request $request): Response
    {
        $error = null;
        if ($request->method === Request::METHOD_POST) {
            try {
                if ($this->musicService->uploadSong($request) instanceof Song) {
                    $error = 'Success';
                }
            } catch (UploadException $e) {
                $error = $e->getMessage();
            }
        }

        $form = $this->formTemplate->getUploadForm($error);
        if (!$request->isHTMX) {
            SessionService::sessionWrite('htmx', $form->render());

            return new RedirectResponse('/');
        }

        return new Response($form);
    }

    #[Route(['/songs/{id}/view'], 'app_viewSong')]
    public function viewSong(int $id): Response
    {
        $song = $this->musicService->getSongById($id);
        if ($song === null) {
            return new Response('');
        }

        return new Response($this->musicTemplate->getSong($song));
    }

    #[Route(['/songs/{id}/delete'], 'app_deleteSong')]
    public function deleteSong(int $id): Response
    {
        return $this->musicService->deleteSongById($id);
    }

    #[Route(['/songs/{id:[0-9]+}/play'], 'app_playSong')]
    public function playSong(int $id): Response
    {
        $song = $this->musicService->getSongById($id);
        $source = '';
        if ($song !== null) {
            $source = PathService::combineSegments('Assets', 'Music', $song->lookupKey);
        }

        $player = $this->musicTemplate
            ->get('player')
            ->fill('source', $source)
        ;

        return new Response($player);
    }

    #[Route(['/songs/test'], 'app_testSongs')]
    public function testSongs(Request $request): Response
    {
        return new Response(var_export($request, true));
    }
}
