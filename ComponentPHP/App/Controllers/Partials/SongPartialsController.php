<?php

declare(strict_types=1);

namespace App\Controllers\Partials;

use App\Exceptions\Music\UploadException;
use App\Middleware\Auth;
use App\Middleware\Partial;
use App\Models\Song;
use App\Services\AuthService;
use App\Services\MusicService;
use App\Templates\HTML\FormTemplate;
use App\Templates\HTML\MusicTemplate;
use Core\Routing\Attributes\Route;
use Core\Routing\Controllers\AbstractController;
use Core\Routing\Enums\RequestMethods;
use Core\Routing\Models\Requests\HTMXRequest;
use Core\Routing\Models\Requests\Request;
use Core\Routing\Models\Responses\HTMXResponse;
use Core\Routing\Models\Responses\RedirectResponse;
use Core\Routing\Models\Responses\Response;
use Core\Sessions\Services\SessionService;

#[Auth]
final class SongPartialsController extends AbstractController
{
    public function __construct(
        private readonly MusicTemplate $musicTemplate,
        private readonly FormTemplate $formTemplate,
        private readonly MusicService $musicService,
        private readonly AuthService $authService,
    ) {
    }

    #[Partial]
    #[Route(['/songs'], 'app_viewAllSongs', [RequestMethods::Get])]
    public function viewSongs(): HTMXResponse
    {
        return new HTMXResponse($this->musicTemplate->getAllSongs($this->musicService->getAllSongs(), $this->authService->getRequiredUser()));
    }

    #[Route(['/songs/{id}/view'], 'app_viewSong', [RequestMethods::Get])]
    public function viewSong(int $id): HTMXResponse
    {
        $song = $this->musicService->getSongById($id);
        if ($song === null) {
            return new HTMXResponse('');
        }

        return new HTMXResponse($this->musicTemplate->getSong($song, $this->authService->getRequiredUser()));
    }

    #[Route(['/songs/upload'], 'app_uploadSong', [RequestMethods::Get, RequestMethods::Post])]
    public function uploadSong(Request $request): Response
    {
        $error = null;
        if ($request->method === RequestMethods::Post) {
            try {
                if ($this->musicService->uploadSong($request) instanceof Song) {
                    $error = 'Success';
                }
            } catch (UploadException $e) {
                $error = $e->getMessage();
            }
        }

        $form = $this->formTemplate->getUploadForm($error);
        if (!$request instanceof HTMXRequest) {
            SessionService::sessionWrite('htmx', $form->render());

            return new RedirectResponse('/');
        }

        return new Response($form);
    }

    #[Route(['/songs/{id}/delete'], 'app_deleteSong')]
    public function deleteSong(int $id): Response
    {
        return $this->musicService->deleteSongById($id);
    }

    #[Route(['/songs/{id:[0-9]+}/play'], 'app_playSong', [RequestMethods::Get])]
    public function playSong(int $id): HTMXResponse
    {
        $song = $this->musicService->getSongById($id);
        if ($song === null) {
            return new HTMXResponse('');
        }

        return new HTMXResponse($this->musicTemplate->getPlayer($song));
    }

    #[Route(['/songs/test'], 'app_testSongs')]
    public function testSongs(Request $request): HTMXResponse
    {
        if (!$request instanceof HTMXRequest) {
            return new HTMXResponse('Nope');
        }

        $resp = "";
        foreach ($request->server as $key => $value) {
            $resp .= "{$key} => {$value}<br>";
        }

        return new HTMXResponse($resp);
    }

    #[Route(['/songs/test2/{id}/view'], 'app_testSongs2')]
    public function testSongs2(Request $request, int $id): Response
    {
        dump($request);
        if ($id === 12) {
            dump('yeah');
        }

        return new HTMXResponse('surp', hxRetarget: "#newtarget");
    }
}
