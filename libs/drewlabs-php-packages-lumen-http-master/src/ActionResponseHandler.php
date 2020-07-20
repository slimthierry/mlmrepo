<?php

namespace Drewlabs\Packages\Http;

class ActionResponseHandler implements \Drewlabs\Packages\Http\Contracts\IActionResponseHandler
{
    /**
     * Controllers Http response formatter
     * @param mixed $data
     * @param int $response_code
     * @param array $headers
     * @return \Illuminate\Http\JsonResponse
     */
    public function respond($data, $status, $headers = array())
    {
        return response()->json(
            $data,
            $status,
            $headers,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param mixed $data
     * @param array|null $errors
     * @param bool $success
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondOk($data, array $errors = null, $success = true)
    {
        return $this->respond(
            array(
                'success' => $success,
                'body' => array('error_message' => null, 'response_data' => $data, 'errors' => $errors),
                'code' => 200,
            ),
            200
        );
    }
    /**
     * Method for converting thrown exceptions into http response
     *
     * @param \Exception $e
     * @param array|null $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondError(\Exception $e, array $errors = null)
    {
        $response_message = $e->getMessage();
        app()['log']->error('API_ERROR_MESSAGE : ' . $e->getMessage());
        // Checks if running in production or dev for error message to be send
        return $this->respond(
            array(
                'success' => false,
                'body' => array(
                    'error_message' => filter_var(config('app.debug'), FILTER_VALIDATE_BOOLEAN) === false ?
                        'Server Error' : $response_message,
                    'response_data' => null, 'errors' => $errors
                ),
                'code' => 500,
            ),
            500
        );
    }
    /**
     * Method for converting bad request to a json formatted response
     *
     * @param array $errors
     * @return \Illuminate\Http\JsonResponse
     */
    public function respondBadRequest(array $errors)
    {
        $response_message = 'Bad request... Invalid request inputs';
        return $this->respond(
            array(
                'success' => false,
                'body' => array('error_message' => $response_message, 'response_data' => null, 'errors' => $errors),
                'code' => 422,
            ),
            422
        );
    }

    /**
     * Convert an authorization exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception|null  $exception
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorized($request, \Exception $exception = null)
    {
        $message = $request->method() . ' ' . $request->path() . '  Unauthorized access.' . (isset($exception) ? ' [ERROR] : ' . $exception->getMessage() : '');
        return response($message, 401);
    }

    // Provides functionnalities for managing file download

    /**
     * A wrapper method arround illuminate response()->download(...params) method
     *
     * @param string $pathToFile
     * @param string $downloadedFileName
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function download($pathToFile, $downloadedFileName = null, $headers = array(), $deleteAfterSend = false)
    {
        $result = response()->download($pathToFile, $downloadedFileName, $headers);
        return $result->deleteFileAfterSend($deleteAfterSend);
    }

    /**
     * Wrapper arround Illuminate streamDownload method of the [\Illuminate\Contracts\Routing\ResponseFactory::class]
     *
     * @param [type] $filename
     * @param \Closure $callback
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function streamDownload($filename, \Closure $callback)
    {
        return response()->streamDownload($callback, $filename);
    }
    /**
     * This method is a wrapper arround of the [\Illuminate\Contracts\Routing\ResponseFactory::class] [[file]]
     * method that may be used to display a file, such as an image or PDF, directly in the user's browser
     * instead of initiating a download.
     *
     * @param string $pathToFile
     * @param array $headers
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function loadFile($pathToFile, $headers = array())
    {
        return response()->file($pathToFile, $headers);
    }
}
