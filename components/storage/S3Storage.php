<?php

namespace app\components\storage;

use Yii;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;
use yii\helpers\FileHelper;

/**
 *
 * S3Storage handles resources to upload/uploaded to Amazon AWS
 *
 */
class S3Storage extends Component implements StorageInterface
{
    /**
     * @var string
     */
    public $endpoint;
    /**
     * @var string Amazon Region
     */
    public $region;
    /**
     * @var string Amazon access key
     */
    public $key;
    /**
     * @var string Amazon secret access key
     */
    public $secret;
    /**
     * @var string Amazon Bucket
     */
    public $bucket;
    /**
     * @var string Amazon API Version
     */
    public $version = 'latest';
    /**
     * @var \Aws\S3\S3Client
     */
    private $_client;
    /**
     * @var boolean V4 signature
     */
    public $enableV4 = false;
    /**
     * @var array
     */
    public $cacheServers = [];
    /**
     * @var string
     */
    protected $_cacheServer;

    /**
     * @inheritdoc
     */
    public function init()
    {
        foreach (['key', 'secret', 'bucket', 'region'] as $attribute) {
            if ($this->$attribute === null) {
                throw new InvalidConfigException(strtr('"{class}::{attribute}" cannot be empty.', [
                    '{class}'     => static::className(),
                    '{attribute}' => '$' . $attribute
                ]));
            }
        }
        if (count($this->cacheServers)) {
            $this->_cacheServer = $this->cacheServers[array_rand($this->cacheServers)];
        }
        parent::init();
    }

    /**
     * Saves a file
     * @param string $filePath
     * @param string $name the name of the file
     * @param array $options extra options for the object to save on the bucket.
     * @return \Aws\Result
     */
    public function save($filePath, $name, $options = [])
    {
        $options = ArrayHelper::merge([
            'Bucket'     => $this->bucket,
            'Key'        => $name,
            'SourceFile' => $filePath,
            'ACL'        => 'public-read'
        ], $options);

        return $this->getClient()->putObject($options);
    }

    /**
     * Removes a file
     * @param string $name the name of the file to remove
     * @return boolean
     */
    public function delete($name)
    {
        $result = $this->getClient()->deleteObject([
            'Bucket' => $this->bucket,
            'Key'    => $name
        ]);

        return $result['DeleteMarker'];
    }

    /**
     * Checks whether a file exists or not.
     * @param string $name the name of the file
     * @return boolean
     */
    public function fileExists($name)
    {
        try {
            $head = $this->getClient()->headObject([
                'Bucket' => $this->bucket,
                'Key'    => $name,
            ]);
        } catch (S3Exception $e) {
            return false;
        }
        return $head;
    }

    /**
     * Returns the url of the file or empty string if the file does not exists.
     * @param string $name the key name of the file to access
     * @return string
     */
    public function getUrl($name)
    {
        if (!empty($this->_cacheServer)) {
            return 'http://' . $this->_cacheServer . '/' . $name;
        } else {
            return $this->getClient()->getObjectUrl($this->bucket, $name);
        }
    }

    /**
     * Delete all objects that match a specific key prefix.
     * @param string $prefix delete only objects under this key prefix
     * @return type
     */
    public function deleteMatchingObjects($prefix)
    {
        return $this->getClient()->deleteMatchingObjects($this->bucket, $prefix);
    }

    /**
     * Return the full path a file names only (no directories) within s3 virtual "directory" by treating s3 keys as path names.
     * @param string $directory the prefix of keys to find
     * @return array of ['path' => string, 'name' => string, 'type' => string, 'size' => int]
     */
    public function listFiles($directory)
    {
        $files = [];

        $iterator = $this->getClient()->getIterator('ListObjects', [
            'Bucket' => $this->bucket,
            'Prefix' => $directory,
        ]);

        foreach ($iterator as $object) {
            // don't return directories
            if (substr($object['Key'], -1) != '/') {
                $file = [
                    'path' => $object['Key'],
                    'name' => substr($object['Key'], strrpos($object['Key'], '/') + 1),
                    'type' => $object['StorageClass'],
                    'size' => (int)$object['Size'],
                ];
                $files[] = $file;
            }
        }

        return $files;
    }

    public function updateAcl()
    {
        $iterator = $this->getClient()->getIterator('ListObjects', [
            'Bucket' => $this->bucket,
            'Prefix' => '',
        ]);

        foreach ($iterator as $object) {
            if (substr($object['Key'], -1) != '/') {
                $this->getClient()->putObjectAcl([
                    'Bucket'     => $this->bucket,
                    'Key'        => $object['Key'],
                    'ACL'        => 'public-read'
                ]);
            }
        }
    }

    /**
     * Returns a S3Client instance
     * @return \Aws\S3\S3Client
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $settings = [
                'region'  => $this->region,
                'version' => $this->version,
                'credentials' => [
                    'key'       => $this->key,
                    'secret'    => $this->secret
                ]
            ];
            if ($this->enableV4) {
                $settings['signature'] = 'v4';
            }

            if ($this->endpoint) {
                $settings['endpoint'] = $this->endpoint;
            }

            $this->_client = new S3Client($settings);
        }
        return $this->_client;
    }

    public function getS3Details()
    {
        $acl = 'public-read';
        $algorithm = "AWS4-HMAC-SHA256";
        $service = "s3";
        $date = gmdate("Ymd\THis\Z");
        $shortDate = gmdate("Ymd");
        $requestType = "aws4_request";
        $expires = "86400"; // 24 Hours
        $successStatus = "201";
        $url = "http://{$this->bucket}.{$service}-{$this->region}.amazonaws.com";

        $scope = [
            $this->key,
            $shortDate,
            $this->region,
            $service,
            $requestType
        ];
        $credentials = implode('/', $scope);

        $policy = [
            'expiration' => gmdate('Y-m-d\TG:i:s\Z', strtotime('+6 hours')),
            'conditions' => [
                ['bucket' => $this->bucket],
                ['acl' => $acl],
                ['starts-with', '$key', ''],
                ['starts-with', '$Content-Type', ''],
                ['starts-with', '$name', ''],
                ['success_action_status' => $successStatus],
                ['x-amz-credential' => $credentials],
                ['x-amz-algorithm' => $algorithm],
                ['x-amz-date' => $date],
                ['x-amz-expires' => $expires],
            ]
        ];
        $base64Policy = base64_encode(json_encode($policy));

        $dateKey = hash_hmac('sha256', $shortDate, 'AWS4' . $this->secret, true);
        $dateRegionKey = hash_hmac('sha256', $this->region, $dateKey, true);
        $dateRegionServiceKey = hash_hmac('sha256', $service, $dateRegionKey, true);
        $signingKey = hash_hmac('sha256', $requestType, $dateRegionServiceKey, true);

        $signature = hash_hmac('sha256', $base64Policy, $signingKey);

        $inputs = [
            'Content-Type' => '',
            'acl' => $acl,
            'success_action_status' => $successStatus,
            'policy' => $base64Policy,
            'X-amz-credential' => $credentials,
            'X-amz-algorithm' => $algorithm,
            'X-amz-date' => $date,
            'X-amz-expires' => $expires,
            'X-amz-signature' => $signature
        ];

        return compact('url', 'inputs');
    }

    public function getLocalPath($path)
    {
        $info = pathinfo($path);
        $localPath = Yii::$app->getRuntimePath() . '/cropper/';
        if (!is_dir($localPath)) {
            FileHelper::createDirectory($localPath);
        }
        $localPath .= uniqid() . '.' . $info['extension'];
        $this->getClient()->getObject(array(
            'Bucket' => $this->bucket,
            'Key' => $path,
            'SaveAs' => $localPath
        ));
        return $localPath;
    }

    public function copy($from, $to)
    {
        $this->getClient()->copyObject([
            'Bucket' => $this->bucket,
            'CopySource' => $this->bucket . '/' . $from,
            'Key' => $to,
        ]);
    }
}
