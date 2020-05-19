<?php

namespace App\Listeners;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\Events\AccessTokenCreated;

class PassportAccessTokenCreated
{
    /**
     * Handle the event.
     *
     * @param  AccessTokenCreated $event
     * @return void
     */
    public function handle(AccessTokenCreated $event)
    {
        $provider = \Config::get('auth.guards.api.provider');
        DB::table('oauth_access_token_providers')->insert([
            "oauth_access_token_id" => $event->tokenId,
            "provider" => $provider,
            "created_at" => new Carbon(),
            "updated_at" => new Carbon(),
        ]);
    }
}
