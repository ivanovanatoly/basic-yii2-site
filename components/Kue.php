<?php
namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\redis\Connection;

class Kue extends Component
{
    /**
     * @var Connection|string|array the Redis [[Connection]] object or the application component ID of the Redis [[Connection]].
     * This can also be an array that is used to create a redis [[Connection]] instance in case you do not want do configure
     * redis connection as an application component.
     * After the Cache object is created, if you want to change this property, you should only assign it
     * with a Redis [[Connection]] object.
     */
    public $redis = 'redis';

    const PRIORITY_LOW = 10;
    const PRIORITY_NORMAL = 0;
    const PRIORITY_MEDIUM = -5;
    const PRIORITY_HIGH = -10;
    const PRIORITY_CRITICAL = -15;

    const ATTEMPTS_DEFAULT = 2;

    /**
     * Initializes the Kue component.
     * This method will initialize the [[redis]] property to make sure it refers to a valid redis connection.
     * @throws InvalidConfigException if [[redis]] is invalid.
     */
    public function init()
    {
        parent::init();
        if (is_string($this->redis)) {
            $this->redis = Yii::$app->get($this->redis);
        } elseif (is_array($this->redis)) {
            if (!isset($this->redis['class'])) {
                $this->redis['class'] = Connection::className();
            }
            $this->redis = Yii::createObject($this->redis);
        }
        if (!$this->redis instanceof Connection) {
            throw new InvalidConfigException("Kue::redis must be either a Redis connection instance or the application component ID of a Redis connection.");
        }
    }

    /**
     * @param string $type
     * @param $data
     * @param int|null $id
     * @param int $priority
     * @param int $maxAttempts
     * @param int $promoteAt expiry time in the number of milliseconds since Unix Epoch (January 1 1970 00:00:00 GMT)
     * @return int|null|string
     * @throws \Exception
     */
    public function create($type, $data, $id = null, $priority = self::PRIORITY_NORMAL, $maxAttempts = self::ATTEMPTS_DEFAULT, $promoteAt = null)
    {
        if (empty($type)) {
            throw new \Exception('Empty job type');
        }

        $maxAttempts = intval($maxAttempts);
        if (!$maxAttempts) {
            throw new \Exception('0 attempts is not allowed');
        }

        if (is_null($id)) {
            $id = $this->redis->executeCommand('INCR', ['q:ids']);
        } else {
            $id = $type . '_' . $id;
        }

        $result = $this->redis->executeCommand('SADD', ['q:job:types', $type]);

        $priority = intval($priority);

        $title = (empty($data['title'])) ? '' : $data['title'];

        $result = $this->redis->executeCommand('HMSET', [
            'q:job:' . $id,

            'id',       $id,
            'state',    isset($promoteAt) ? 'delayed' : 'inactive',
            'title',    $title,
            'type',     $type,
            'created_at', round(microtime(true) * 1000),
            'promote_at', $promoteAt,
            'data',     json_encode($data),
            'priority', $priority
        ]);

        if(isset($promoteAt)){
            $this->redis->executeCommand('ZADD', [
                'q:jobs:delayed',
                $promoteAt,
                $id
            ]);

            $this->redis->executeCommand('ZADD', [
                'q:jobs:' . $type . ':delayed',
                $promoteAt,
                $id
            ]);
        }else{
            $this->redis->executeCommand('ZADD', [
                'q:jobs',
                $priority,
                $id
            ]);

            $this->redis->executeCommand('ZADD', [
                'q:jobs:inactive',
                $priority,
                $id
            ]);

            $this->redis->executeCommand('ZADD', [
                'q:jobs:' . $type . ':inactive',
                $priority,
                $id
            ]);
        }

        $this->redis->executeCommand('LPUSH', [
            'q:' . $type . ':jobs',
            1
        ]);

        return $id;
    }
}
