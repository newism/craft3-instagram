<?php
/**
 * Instagram plugin for Craft CMS 3.x
 *
 * Instagram plugin to connect users / site to the Instagram Basic API
 *
 * @link      https://newism.com.au
 * @copyright Copyright (c) 2020 Leevi Graham
 */

namespace newism\instagram\services;

use craft\base\Component;
use craft\helpers\UrlHelper;
use League\OAuth2\Client\Provider\Instagram as InstagramProvider;
use League\OAuth2\Client\Token\AccessToken;
use newism\instagram\Plugin;

/**
 * @author    Leevi Graham
 * @package   Instagram
 * @since     1.0.0
 */
class Instagram extends Component
{
    public function getProvider(): InstagramProvider
    {
        $settings = Plugin::$plugin->getSettings();
        $provider = new InstagramProvider(
            [
                'clientId' => $settings['clientId'],
                'clientSecret' => $settings['clientSecret'],
                'redirectUri' => UrlHelper::cpUrl('instagram/connect/check'),
            ]
        );

        return $provider;
    }

    public function fetchUserMedia(AccessToken $accessToken, $config = []): array {

        $config = array_merge([
            'limit' => 25,
            'fields' => 'caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username,children',
            'cache' => null
        ], $config);

        $cache = \Craft::$app->getCache();
        $provider = $this->getProvider();
        $fetchUserMedia = static function() use ($accessToken, $provider, $config) {
            $mediaUrl = $provider->getGraphHost().'/me/media?' . http_build_query([
                    'fields' => $config['fields'],
                    'limit' => $config['limit']
                ]);
            $request = $provider->getAuthenticatedRequest('GET', $mediaUrl, $accessToken);
            return $provider->getParsedResponse($request);
        };
        if($config['cache'] !== null) {
            $userMedia = $cache->getOrSet('instagramUserMedia_' . $accessToken->getToken(), $fetchUserMedia, $config['cache']);
        } else {
            $userMedia = $fetchUserMedia();
        }
        return $userMedia;
    }
}
