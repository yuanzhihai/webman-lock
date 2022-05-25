<?php

namespace yzh52521\WebmanLock;

use support\Container;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

/**
 * @method static LockInterface create(?string $orderId = null, ?float $ttl = null, ?bool $autoRelease = null, ?string $prefix = null)
 */
class Locker
{
    protected static $factory = null;

    public static function __callStatic($name, $arguments)
    {
        $key = $arguments[0] ?? '';
        unset($arguments[0]);
        return static::createLock($name . $key, ...$arguments);
    }

    /**
     * 创建锁
     * @param string $key
     * @param float|null $ttl 锁超时时间
     * @param bool|null $autoRelease 是否自动释放锁
     * @param string|null $prefix 锁前缀
     * @return LockInterface
     */
    protected static function createLock(string $key, ?float $ttl = null, ?bool $autoRelease = null, ?string $prefix = null)
    {
        $config      = config('plugin.yzh52521.lock.app.default_config', []);
        $ttl         = $ttl !== null ? $ttl : ($config['ttl'] ?? 300);
        $autoRelease = $autoRelease !== null ? $autoRelease : ($config['auto_release'] ?? true);
        $prefix      = $prefix !== null ? $prefix : ($config['prefix'] ?? 'lock_');
        return static::getLockFactory()->createLock($prefix . $key, $ttl, $autoRelease);
    }


    /**
     * @return LockFactory
     */
    protected static function getLockFactory()
    {
        if (static::$factory === null) {
            $storage       = config('plugin.yzh52521.lock.app.storage');
            $storageConfig = config('plugin.yzh52521.lock.app.storage_configs')[$storage];
            if (is_callable($storageConfig['construct'])) {
                $storageConfig['construct'] = call_user_func($storageConfig['construct']);
            }
            $storageInstance = Container::make($storageConfig['class'], $storageConfig['construct']);
            static::$factory = new LockFactory($storageInstance);
        }

        return static::$factory;
    }
}