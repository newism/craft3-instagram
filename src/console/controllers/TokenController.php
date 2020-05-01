<?php
/**
 * Instagram plugin for Craft CMS 3.x
 *
 * Instagram plugin to connect users / site to the Instagram Basic API
 *
 * @link      https://newism.com.au
 * @copyright Copyright (c) 2020 Leevi Graham
 */

namespace newism\instagram\console\controllers;

use League\OAuth2\Client\Token\AccessToken;
use newism\instagram\Plugin;
use newism\instagram\records\AccessTokenRecord;
use yii\console\Controller;

/**
 * Access Token Commands
 *
 * @author    Leevi Graham
 * @package   Instagram
 * @since     1.0.0
 */
class TokenController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * Refreshes all instagram access tokens
     *
     * @param string $expiresWithin Find tokens that expire within this time period. This can be any format new \Date() accepts.
     * @return mixed
     * @throws \JsonException
     * @throws \League\OAuth2\Client\Provider\Exception\IdentityProviderException
     */
    public function actionRefresh($expiresWithin = '60 days')
    {
        $accessTokenRecords = AccessTokenRecord::find()
            ->where(['<', 'expires', (new \DateTime($expiresWithin))->format('U')])
            ->all();

        $provider = Plugin::$plugin->instagram->getProvider();
        $refreshTokenUrl = $provider->getGraphHost().'/refresh_access_token?'.http_build_query(
                [
                    'grant_type' => 'ig_refresh_token',
                ]
            );

        $this->stdout(sprintf("Updating %s tokens\n", count($accessTokenRecords)));

        foreach ($accessTokenRecords as $accessTokenRecord) {
            $accessToken = new AccessToken(json_decode($accessTokenRecord->token, true, 512, JSON_THROW_ON_ERROR));
            $request = $provider->getAuthenticatedRequest('GET', $refreshTokenUrl, $accessToken);
            $response = $provider->getParsedResponse($request);
            $accessToken = new AccessToken($response);
            $accessTokenRecord->expires = $accessToken->getExpires();
            $accessTokenRecord->token = json_encode($accessToken, JSON_THROW_ON_ERROR);
            $accessTokenRecord->save();
        }

    }
}
