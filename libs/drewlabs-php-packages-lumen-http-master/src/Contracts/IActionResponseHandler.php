<?php

namespace Drewlabs\Packages\Http\Contracts;

interface IActionResponseHandler
{
    /**
     * Controllers Http response formatter
     * @param mixed $data
     * @param int $response_code
     * @param array $headers
     * @return Response
     */
    public function respond($data, $status, $headers = array());
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param mixed $data
     * @param array|null $errors
     * @param bool $success
     * @return Response
     */
    public function respondOk($data, array $errors = null, $success = true);
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param \Exception $e
     * @param array|null $errors
     * @return Response
     */
    public function respondError(\Exception $e, array $errors = null);
    /**
     * Method for converting bad request to a json formatted response
     *
     * @param array $errors
     * @return Response
     */
    public function respondBadRequest(array $errors);

    /**
     * Convert an authorization exception into a response.
     *
     * @param  Request  $request
     * @param  \Exception|null  $exception
     * @return Response
     */
    public function unauthorized($request, \Exception $exception = null);

    // Provides functionnalities for managing file download

    /**
     * A wrapper method arround illuminate response()->download(...params) method
     *
     * @param string $pathToFile
     * @param string $downloadedFileName
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($pathToFile, $downloadedFileName = null, $headers = array(), $deleteAfterSend = false);

    /**
     * Wrapper arround Illuminate streamDownload method of the [\Illuminate\Contracts\Routing\ResponseFactory::class]
     *
     * @param [type] $filename
     * @param \Closure $callback
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streamDownload($filename, \Closure $callback);
    /**
     * This method is a wrapper arround of the [\Illuminate\Contracts\Routing\ResponseFactory::class] [[file]]
     * method that may be used to display a file, such as an image or PDF, directly in the user's browser
     * instead of initiating a download.
     *
     * @param string $pathToFile
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function loadFile($pathToFile, $headers = array());
}
