# webman-lock
webman lock plugin 

# 简介
在 webman 中简化使用业务锁功能，使用 [symfony/lock](https://symfony.com/doc/current/components/lock.html)

# 安装

```
compoer require yzh52521/webman-lock
```
# 使用

```
<?php

namespace app\controller;

use yzh52521\WebmanLock\Locker;

class Cash {
    public function changeCash()
    {
        $lock = Locker::lock($currentUserId);
        if (!$lock->acquire()) {
            throw new \Exception('操作太频繁，请稍后再试');
        }
        try {
            // 修改用户金额
        } finally {
            $lock->release();
        }
        
        return 'ok';
    }
}

```

更多操作参考：[symfony/lock](https://symfony.com/doc/current/components/lock.html) 文档
