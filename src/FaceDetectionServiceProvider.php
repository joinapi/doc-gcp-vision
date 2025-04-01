<?php

namespace Joinapi\DocGcpVision;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class FaceDetectionServiceProvider  extends ServiceProvider implements DeferrableProvider
{
    public function register()
    {
        $this->app->singleton( 'facedetectionservice', function ($app) {
            return new FaceDetectionService(['keyFile' => getenv('GOOGLE_APPLICATION_CREDENTIALS')] );
        });
    }
}