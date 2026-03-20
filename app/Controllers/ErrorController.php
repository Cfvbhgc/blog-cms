<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    /**
     * Display an error page.
     */
    public function show(int $code = 404, string $message = 'Page not found'): void
    {
        $this->view('errors.404', [
            'pageTitle' => $code . ' Error',
            'code'      => $code,
            'message'   => $message,
        ]);
    }
}
