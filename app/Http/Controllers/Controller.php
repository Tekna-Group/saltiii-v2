<?php

namespace App\Http\Controllers;
/**
 * @OA\Info(
 *     title="Saltiii API",
 *     version="1.0.0",
 *     description="API documentation"
 * )
 */
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
