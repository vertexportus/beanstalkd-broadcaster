# beanstalkd-broadcaster

Laravel Beanstalkd Broadcaster.

## How To's

### Install

`composer require vertexportus/beanstalkd-broadcaster`

### Provider

This package automatically loads if using Laravel >=5.5. If not, simply add `Vertexportus\BeanstalkdBroadcaster\BeanstalkdBroadcasterServiceProvider::class` to your `app.php` config.

### Usage

Add the following to `broadcasting.php` config file, and set `BROADCAST_DRIVER` to `beanstalkd`.

```
'beanstalkd' => [
    'driver' => 'beanstalkd',
    'host' => env('BEANSTALK_HOST', 'localhost'),
    'port' => env('BEANSTALK_PORT', '11300'),
    'tube' => 'socketIO'
],
```