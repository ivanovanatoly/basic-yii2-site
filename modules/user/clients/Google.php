<?php

namespace app\modules\user\clients;

use yii\authclient\clients\GoogleOAuth as BaseGoogle;

class Google extends BaseGoogle implements ClientInterface
{
    /** @inheritdoc */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['email'])
            ? $this->getUserAttributes()['email']
            : null;
    }

    /** @inheritdoc */
    public function getUsername()
    {
        return;
    }
}
