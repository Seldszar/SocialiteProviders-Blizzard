<?php

namespace SocialiteProviders\Blizzard;

use SocialiteProviders\Manager\SocialiteWasCalled;

class BlizzardExtendSocialite
{
    /**
     * Execute the provider.
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('blizzard', __NAMESPACE__.'\Provider');
    }
}
