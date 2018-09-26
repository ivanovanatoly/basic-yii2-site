<?php

namespace app\components;

use yii\base\Component;

class VkApi extends Component
{
    /* @var string */
    public $v = '5.74';

    /* @var string */
    public $url = 'https://api.vk.com/method/';

    /* @var string */
    public $clientId;

    /* @var string */
    public $ownerId;

    /* @var string */
    public $token;

    /**
     * @param $method string
     * @param $params []
     * @return mixed
     */
    public function api($method, $params = [])
    {
        $params = array_merge($params, [
            'access_token' => $this->token,
            'v'            => $this->v
        ]);
        $url = $this->url . $method . '?' . http_build_query($params);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        return json_decode(curl_exec($curl));
    }

    /**
     * @return mixed
     */
    public function photosGetWallUploadServer()
    {
        return $this->api('photos.getWallUploadServer');
    }

    /**
     * @param $name
     * @param $url
     * @return mixed
     */
    function uploadFile($name, $url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        $curlFile = curl_file_create($name, 'image/jpeg', 'image.jpg');
        $post = [
            'photo' => $curlFile,
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $result = curl_exec($ch) ;

        return json_decode($result);
    }

    /**
     * @param $uploadedFile
     * @return mixed
     */
    function photosSaveWallPhoto($uploadedFile)
    {
        return $this->api('photos.saveWallPhoto', [
            'user_id' => $this->ownerId,
            'server'  => $uploadedFile->server,
            'hash'    => $uploadedFile->hash,
            'photo'   => $uploadedFile->photo
        ]);
    }

    /**
     * @param $photo
     * @param $message string
     * @return mixed
     */
    function wallPost($photo, $message = null)
    {
        $params = [
            'owner_id'    => $this->ownerId,
            'attachments' => "photo{$this->ownerId}_{$photo->response[0]->id}",
        ];
        if ($message) {
            $params['message'] = $message;
        }

        return $this->api('wall.post', $params);
    }

    /**
     * @param $image string
     * @param $message string
     * @return array|boolean
     */
    function postWithPhoto($image, $message = null)
    {
        try {
            $server = $this->photosGetWallUploadServer();
            $file   = $this->uploadFile($image, $server->response->upload_url);
            $photo  = $this->photosSaveWallPhoto($file);
            $post   = $this->wallPost($photo, $message);

            return [
                'postId'  => $post->response->post_id,
                'photoId' => $photo->response[0]->id,
                'photo'   => "photo{$this->ownerId}_{$photo->response[0]->id}"
            ];
        } catch (\Exception $e) {
            return false;
        }
    }
}