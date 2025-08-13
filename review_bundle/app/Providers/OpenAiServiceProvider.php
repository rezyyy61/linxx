<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client as Guzzle;
use OpenAI;
use OpenAI\Client;

class OpenAiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Client::class, function () {


            $guzzle = new Guzzle([
                'timeout' => config('services.openai.timeout', 60),
                'headers' => [
                    'OpenAI-Beta' => 'assistants=v2',
                ],
            ]);

            return OpenAI::factory()
                ->withApiKey(config('services.openai.key'))
                ->withOrganization(config('services.openai.organization'))
                ->withHttpClient($guzzle)
                ->make();
        });
    }
}
