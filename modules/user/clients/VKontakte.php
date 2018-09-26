<?php

namespace app\modules\user\clients;

use Yii;
use yii\authclient\clients\VKontakte as BaseVKontakte;
use yii\helpers\Json;

class VKontakte extends BaseVKontakte implements ClientInterface
{
    /** @inheritdoc */
    public $scope = 'email';

    public $apiVersion = '3.0';

    /** @inheritdoc */
    public function getEmail()
    {
        return $this->getAccessToken()->getParam('email');
    }

    /** @inheritdoc */
    public function getUsername()
    {
        return isset($this->getUserAttributes()['screen_name'])
            ? $this->getUserAttributes()['screen_name']
            : null;
    }

    /** @inheritdoc */
    protected function defaultTitle()
    {
        return Yii::t('user', 'VKontakte');
    }

    /**
     * {@inheritdoc}
     */
    protected function initUserAttributes()
    {
        $response = $this->api('users.get.json', 'GET', [
            'fields' => implode(',', $this->attributeNames),
        ]);
        if (empty($response['response'])) {
            throw new \RuntimeException('Unable to init user attributes due to unexpected response: ' . Json::encode($response));
        }
        $attributes = array_shift($response['response']);
        $accessToken = $this->getAccessToken();
        if (is_object($accessToken)) {
            $accessTokenParams = $accessToken->getParams();
            unset($accessTokenParams['access_token']);
            unset($accessTokenParams['expires_in']);
            $attributes = array_merge($accessTokenParams, $attributes);
        }
        return $attributes;
    }

    /**
     * {@inheritdoc}
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $data = $request->getData();
        $data['v'] = $this->apiVersion;
        $data['uids'] = $accessToken->getParam('user_id');
        $data['access_token'] = $accessToken->getToken();
        $request->setData($data);
    }
}
