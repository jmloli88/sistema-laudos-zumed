<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Cloud\Firestore\FirestoreClient;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(FirestoreClient::class, function ($app) {
            $config = config('firebase.credentials');

            return new FirestoreClient([
                'projectId' => $config['project_id'],
                'keyFile' => $config
            ]);
        });
    }
}
