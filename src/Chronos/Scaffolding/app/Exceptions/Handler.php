<?php

namespace Chronos\Scaffolding\App\Exceptions;

use App\Exceptions\Handler as BaseHandler;
use Chronos\Content\Services\ImageStyleService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends BaseHandler
{

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, \Exception $e)
    {
        $e = $this->prepareException($e);

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        } elseif ($e instanceof TokenMismatchException) {
            return $this->tokenMismatch($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }
        return $this->prepareResponse($request, $e);
    }

    /**
     * Render the given HttpException.
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpExceptionInterface $e)
    {
        $status = $e->getStatusCode();
        $request = \Route::getCurrentRequest();

        // check if 404 response
        if ($status == 404 && $request) {

            // loop through available upload paths and determine if current path is one of them
            $paths = \Config::get('content.upload_paths');
            foreach ($paths as $path) {
                $full_path = parse_url($path['asset_path']);
                $check_path = isset($full_path['path']) ? trim($full_path['path'], '/') . '/*' : basename($request->path());
                if ($request->is($check_path) && $request->getHttpHost() == $full_path['host']) {

                    // check if it is a generatable image style
                    $basename = basename($request->path());
                    if (ImageStyleService::checkIfGeneratableImageStyle($basename)) {

                        // set up paths
                        $upload_path = $path['upload_path'];
                        if (!is_dir($upload_path))
                            mkdir($upload_path, 0755, true);
                        $asset_path = $path['asset_path'];

                        // generate and return image style
                        if (ImageStyleService::generate($upload_path, $asset_path, $basename)) {
                            $mimeGuesser = MimeTypeGuesser::getInstance();
                            $mime = $mimeGuesser->guess($upload_path . '/' . $basename);

                            return response(file_get_contents($upload_path . '/' . $basename), 200)->header('Content-Type', $mime);
                        }
                    }

                    break;
                }
            }
        }

        // check if API, Chronos path or app
        if ($request && ($request->is('api') || $request->is('api/*')))
            return response()->json([
                'message' => $e->getMessage(),
                'status' => $status
            ], $status);

        $view = $request && ($request->is('admin') || $request->is('admin/*')) ? "chronos::errors.{$status}" : "errors.{$status}";

        if (view()->exists($view))
            return response()->view($view, ['exception' => $e], $status, $e->getHeaders());
        else
            return $this->convertExceptionToResponse($e);
    }

    /**
     * Renders the token mismatch exception page.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $e
     * @return \Illuminate\Http\Response
     */
    protected function tokenMismatch($request, TokenMismatchException $e)
    {
        // check if Chronos path or app
        if ($request && ($request->is('admin') || $request->is('admin/*')))
            return response()->view("chronos::errors.token_mismatch", ['exception' => $e]);
        else
            return $this->convertExceptionToResponse($e);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $e
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $e)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        // check if Chronos path
        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect()->guest(route('chronos.auth.login'));
	}
        // or app
        else {
	    return redirect($e->redirectTo());
	}
    }
}
