<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="K7 E-Library API",
 *     description="Digital library management system API for educational institutions"
 * )
 * @OA\Server(url="http://localhost:8747/api/v1")
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer"
 * )
 */
abstract class Controller
{
    //
}
