<?php

namespace SocialiteProviders\Blizzard;

use Laravel\Socialite\Two\ProviderInterface;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider implements ProviderInterface
{
    /**
     * Unique Provider Identifier.
     */
    const IDENTIFIER = 'BLIZZARD';

    /**
     * {@inheritdoc}
     */
    protected $scopes = [];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = '+';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://'.$this->getHost().'/oauth/authorize', $state);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenUrl()
    {
        return 'https://'.$this->getHost().'/oauth/token';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://'.$this->getMasheryHost().'/account/user', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        list($username, $discriminator) = explode('#', $user['battletag']);

        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => $user['battletag'],
            'name'     => $username,
            'email'    => null,
            'avatar'   => null,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code'
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function additionalConfigKeys()
    {
        return ['region'];
    }

    /**
     * Gets the region.
     *
     * @return {String}
     */
    protected function getRegion()
    {
        return strtolower($this->getConfig('region', 'us'));
    }

    /**
     * Gets the host.
     *
     * @return {String}
     */
    protected function getHost()
    {
        $region = $this->getRegion();

        if ($region === 'cn') {
            return 'www.battlenet.com.cn';
        }

        return $region.'.battle.net';
    }

    /**
     * Gets the Mashery host.
     *
     * @return {String}
     */
    protected function getMasheryHost()
    {
        $region = $this->getRegion();

        if ($region === 'cn') {
            return 'api.battlenet.com.cn';
        }

        return $region.'.api.battle.net';
    }
}
