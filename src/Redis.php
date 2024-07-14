<?php

namespace Shahmarasy\Leaf;

/**
 * Leaf + Redis [BETA]
 * ----------
 * Redis made crazy simple
 *
 * @since 2.5.1
 * @author Michael Darko <mickdd22@gmail.com>
 * @author Bahman Shahmarasy <shahmarasy@gmail.com>
 * @version 1.0.1-beta
 */
class Redis
{
    /** @var \Redis */
    private static $redis;

    /**
     * Leaf Redis config
     * @var array
     */
    private static $config = [
        'host' => '127.0.0.1',
        'port' => 6379,
        'connection.timeout' => 0.0,
        'connection.reserved' => null,
        'connection.retryInterval' => 0,
        'connection.readTimeout' => 0.0,
        'password' => null,
        'session' => false,
        'session.savePath' => null,
        'session.saveOptions' => [],
    ];

    /**
     * All errors caught in the app
     */
    private static $errors = [];

    /**
     * Initialize redis and connect to redis instance
     *
     * @param array $config Configuration for the redis instance.
     */
    public static function init(array $config = [])
    {
        static::$config = array_merge(static::$config, $config);

        try {
            $redis = new \Redis();
        } catch (\Throwable $th) {
            trigger_error($th);
        }

        try {
            $redis->connect(
                static::$config['host'],
                static::$config['port'],
                static::$config['connection.timeout'],
                static::$config['connection.reserved'],
                static::$config['connection.retryInterval'],
                static::$config['connection.readTimeout']
            );
        } catch (\Throwable $th) {
            trigger_error($th);
        }

        if (static::$config['password']) {
            try {
                $redis->auth(static::$config['password']);
            } catch (\Throwable $th) {
                trigger_error($th);
            }
        }

        if (
            static::$config['session.saveOptions'] &&
            count(static::$config['session.saveOptions']) > 0
        ) {
            static::parseSaveOptions();
        }

        if (static::$config['session'] === true) {
            static::setSessionHandler();
        }

        static::$redis = $redis;

        return $redis;
    }

    protected static function setSessionHandler()
    {
        if (!static::$config['session.savePath']) {
            static::$config['session.savePath'] = 'tcp://' . static::$config['host'] . ':' . static::$config['port'];

            if (
                static::$config['session.saveOptions'] &&
                count(static::$config['session.saveOptions']) > 0
            ) {
                static::$config['session.savePath'] .= static::$config['session.saveOptions'][0];
            }
        } else {
            if (is_array(static::$config['session.savePath'])) {
                $fullPath = '';

                foreach (static::$config['session.savePath'] as $index => $savePath) {
                    $fullPath .= $savePath;

                    if (
                        static::$config['session.saveOptions'] &&
                        isset(static::$config['session.saveOptions'][$index])
                    ) {
                        $fullPath .= static::$config['session.saveOptions'][$index];
                    }

                    if (($index + 1) < count(static::$config['session.savePath'])) {
                        $fullPath .= ', ';
                    }
                }

                static::$config['session.savePath'] = $fullPath;
            }
        }

        ini_set('session.save_handler', 'redis');
        ini_set('session.save_path', static::$config['session.savePath']);
    }

    protected static function parseSaveOptions()
    {
        $parsedOptions = [];

        foreach (static::$config['session.saveOptions'] as $options) {
            $optionKeys = array_keys($options);
            $option = '';

            foreach ($optionKeys as $optionIndex => $optionValue) {
                if ($optionIndex == 0) {
                    $option .= "?{$optionValue}={$options[$optionValue]}";
                } else {
                    $option .= "&{$optionValue}={$options[$optionValue]}";
                }
            }

            $parsedOptions[] = $option;
        }

        static::$config['session.saveOptions'] = $parsedOptions;
    }

    /**
     * Set a redis value
     *
     * @param string|array $key The value(s) to set
     * @param string|mixed $value — string if not used serializer
     * @param int|array $timeout [optional] Calling setex() is preferred if you want a timeout.
     *
     * Since 2.6.12 it also supports different flags inside an array. Example ['NX', 'EX' => 60]
     * - EX seconds -- Set the specified expire time, in seconds.
     * - PX milliseconds -- Set the specified expire time, in milliseconds.
     * - NX -- Only set the key if it does not already exist.
     * - XX -- Only set the key if it already exist.
     * // Simple key -> value set $redis->set('key', 'value');
     * // Will redirect, and actually make an SETEX call $redis->set('key','value', 10);
     * // Will set the key, if it doesn't exist, with a ttl of 10 seconds $redis->set('key', 'value', ['nx', 'ex' => 10]);
     * // Will set a key, if it does exist, with a ttl of 1000 milliseconds $redis->set('key', 'value', ['xx', 'px' => 1000]);
     * @return bool — TRUE if the command is successful
     * @link https://redis.io/commands/set
     * @since If you're using Redis >= 2.6.12, you can pass extended options as explained in example
     */
    public static function set($key, $value = '', $timeout = null)
    {
        if (is_array($key)) {
            foreach ($key as $itemKey => $itemValue) {
                static::set($itemKey, $itemValue, $timeout);
            }
        } else {
            static::$redis->set($key, $value, $timeout);
        }
    }

    /**
     * Get a redis value
     *
     * @param string|array $key The value(s) to get
     * @return string|mixed|false
     * If key didn't exist, FALSE is returned. Otherwise, the value related to this key is returned
     *
     * @link https://redis.io/commands/get
     */
    public static function get($key)
    {
        if (is_string($key)) {
            return static::$redis->get($key);
        }

        $data = [];

        foreach ($key as $item) {
            $data[$item] = static::get($item);
        }

        return $data;
    }

    /**
     * Push an element to the end of the queue.
     *
     * @param string $queue The name of the queue
     * @param mixed $value The value to be added to the queue
     * @param string $direction The direction to push the element to. Either 'left' or 'right'
     * @return int The length of the queue after the push operation
     * @throws \RedisException If there is an issue with the Redis server.
     */
    public static function pushToQueue(string $queue, $value, string $direction = 'left'): int
    {
        if ($direction === 'left') {
            return static::$redis->lPush($queue, $value);
        } else {
            return static::$redis->rPush($queue, $value);
        }
    }

    /**
     * Pop an element from the front of the queue.
     *
     * @param string $queue The name of the queue
     * @param string $direction The direction to pop the element from. Either 'left' or 'right'
     * @return mixed The value of the first element, or false if the queue is empty
     * @throws \RedisException If there is an issue with the Redis server.
     */
    public static function popFromQueue(string $queue, string $direction = 'right'): mixed
    {
        if ($direction === 'right') {
            return static::$redis->rPop($queue);
        } else {
            return static::$redis->lPop($queue);
        }
    }

    /**
     * Get the length of the queue.
     *
     * @param string $queue The name of the queue
     * @return int The length of the queue
     * @throws \RedisException If there is an issue with the Redis server.
     */
    public static function getQueueLength(string $queue): int
    {
        return static::$redis->lLen($queue);
    }

    /**
     * Peek at the first element of the queue without removing it.
     *
     * @param string $queue The name of the queue
     * @return mixed The value of the first element, or false if the queue is empty
     * @throws \RedisException If there is an issue with the Redis server.
     */
    public static function peekQueue(string $queue): mixed
    {
        return static::$redis->lIndex($queue, 0);
    }

    /**
     * Get all elements of the queue.
     *
     * @param string $queue The name of the queue
     * @return array The elements of the queue
     * @throws \RedisException If there is an issue with the Redis server.
     */
    public static function getAllQueueElements(string $queue): array
    {
        return static::$redis->lRange($queue, 0, -1);
    }

    /**
     * Ping redis server.
     *
     * @param string|null $message — [optional]
     * @return bool|string
     * TRUE if the command is successful or returns message Throws a RedisException object on connectivity error, as described above
     * @throws \RedisException
     * @link https://redis.io/commands/ping
     */
    public static function ping(string $message = null)
    {
        return static::$redis->ping($message);
    }

    /**
     * Return all saved errors
     */
    public static function errors(): array
    {
        return static::$errors;
    }

    /**
     * Get all leaf redis console commands
     */
    public static function commands(): array
    {
        require __DIR__ . '/Commands/InstallCommand.php';
        require __DIR__ . '/Commands/ServerCommand.php';

        return [
            Redis\Commands\InstallCommand::class,
            Redis\Commands\ServerCommand::class,
        ];
    }
}
