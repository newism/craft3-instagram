<?php

namespace newism\instagram\controllers;

use craft\web\Controller;
use League\OAuth2\Client\Token\AccessToken;
use newism\instagram\Plugin;

class InstagramController extends Controller
{
    public function actionConnect()
    {
        $stateKey = \Craft::$app->request->get('stateKey');
        $provider = Plugin::$plugin->instagram->getProvider();
        $authUrl = $provider->getAuthorizationUrl(
            [
                'scope' => ['user_profile', 'user_media'],
            ]
        );
        $state = $provider->getState();

        \Craft::$app->session->set('instagram_oauth_state_'.$state, $stateKey.':'.$state);
        \Craft::$app->getResponse()->redirect($authUrl);
    }

    public function actionConnectCheck()
    {
        $state = \Craft::$app->request->get('state');
        [$savedStateKey, $savedState] = explode(':', \Craft::$app->session->get('instagram_oauth_state_'.$state));

        if (empty($savedState) || $savedState !== $state) {
            throw new \Exception('Invalid OAuth State');
        }

        $provider = Plugin::$plugin->instagram->getProvider();
        $token = $provider->getAccessToken(
            'authorization_code',
            [
                'code' => \Craft::$app->request->get('code'),
            ]
        );

        $settings = Plugin::$plugin->getSettings();
        $grant = 'ig_exchange_token';
        $tokenUrl = $provider->getGraphHost().'/access_token?'.http_build_query(
                [
                    'grant_type' => $grant,
                    'client_secret' => $settings['clientSecret'],
                    'access_token' => (string) $token,
                ]
            );

        $request = $provider->getAuthenticatedRequest('GET', $tokenUrl, $token);
        $response = $provider->getParsedResponse($request);
        $token = new AccessToken($response);
        $encodedToken = json_encode($token, JSON_THROW_ON_ERROR, 512);

        $siteName = \Craft::$app->config->general->siteName;

        return <<<TAG
            <p>Redirecting back to ${siteName}.</p>
            <script type="application/javascript">
                window.opener.postMessage(${encodedToken});
                window.close();
            </script>
        TAG;
    }

    public function actionRefreshToken()
    {
    }

    public function actionDeauthorize()
    {
    }
}
