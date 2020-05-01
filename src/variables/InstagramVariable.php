<?php
/**
 * Instagram plugin for Craft CMS 3.x
 *
 * Instagram plugin to connect users / site to the Instagram Basic API
 *
 * @link      https://newism.com.au
 * @copyright Copyright (c) 2020 Leevi Graham
 */

namespace newism\instagram\variables;

use League\OAuth2\Client\Provider\Instagram;
use League\OAuth2\Client\Token\AccessToken;
use newism\instagram\Plugin;

use Craft;

/**
 * @author    Leevi Graham
 * @package   Instagram
 * @since     1.0.0
 */
class InstagramVariable
{
    public function fetchUserMedia(AccessToken $accessToken, $config = [])
    {
        return Plugin::$plugin->instagram->fetchUserMedia($accessToken, $config);
    }
}
