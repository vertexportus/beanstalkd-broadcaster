<?php namespace Vertexportus\BeanstalkdBroadcaster;

use Pheanstalk\Pheanstalk;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class BeanstalkdBroadcasterServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        app('Illuminate\Broadcasting\BroadcastManager')->extend(
            'beanstalkd',
            function ($app) {
                $config = $app['config']['broadcasting.connections.beanstalkd'];
                return new BeanstalkdBroadcaster(
                    new Pheanstalk($config['host'], $config['port']),
                    $config
                );
            }
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}