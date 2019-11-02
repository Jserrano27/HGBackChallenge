<?php

namespace App\Exceptions;

use App\Traits\WebApiResponser;
use Dotenv\Exception\ValidationException;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use WebApiResponser;
    
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof QueryException && $exception->getCode() == 1045) {
            return $this->errorResponse('Cannot establish a database connection. Check your credentials', 503);
        }

        if($exception instanceof MethodNotAllowedHttpException){
            return response()->json(["error" => "The specified method for the request is invalid", "code" => "405"], 405);
        } 
        
        if ($exception instanceof ConnectException) {
            return $this->errorResponse('Service Temporarily Unavailable. Please try again later', 503);
        }

        if ($exception instanceof ThrottleRequestsException) {
            return $this->errorResponse('Too Many Requests', 429);
        }
        if ($exception instanceof IlluminateValidationException){
            return $this->errorResponse($exception->getMessage(), 422);
        }
        // For details of the exception make true the APP_DEBUG value on the .env file
        if(config('app.debug')){
            return parent::render($request, $exception);
        }

        return $this->errorResponse('Unexpected error. Please try again later', 500);
    }
}
