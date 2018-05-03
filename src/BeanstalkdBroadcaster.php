<?php namespace Vertexportus\BeanstalkdBroadcaster;

use Pheanstalk\Pheanstalk;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class BeanstalkdBroadcaster extends Broadcaster
{
    protected $queueName;
    protected $pheanstalk;

    /**
     * BeanstalkdBroadcaster constructor.
     * @param Pheanstalk $pheanstalk
     * @param $config
     */
    public function __construct(Pheanstalk $pheanstalk, $config)
    {
        $this->pheanstalk = $pheanstalk;
        $this->queueName = $config['tube'];
    }

    /**
     * @param array $channels
     * @param string $event
     * @param array $payload
     */
    public function broadcast(array $channels, $event, array $payload = array())
    {
        $payload = [
            'event' => $event,
            'data' => $payload,
            'socket' => Arr::pull($payload, 'socket'),
        ];
        foreach ($this->formatChannels($channels) as $channel) {
            $this->pheanstalk
                ->useTube($this->queueName)
                ->put(
                    json_encode(['channel' => $channel, 'payload' => $payload]),
                    Pheanstalk::DEFAULT_PRIORITY,
                    Pheanstalk::DEFAULT_DELAY
                );
        }
    }

    /**
     * Authenticate the incoming request for a given channel.
     *
     * @param  \Illuminate\Http\Request $request
     * @return mixed
     */
    public function auth($request)
    {
        if (Str::startsWith($request->channel_name, ['private-', 'presence-']) &&
            ! $request->user()) {
            throw new AccessDeniedHttpException();
        }

        $channelName = Str::startsWith($request->channel_name, 'private-')
            ? Str::replaceFirst('private-', '', $request->channel_name)
            : Str::replaceFirst('presence-', '', $request->channel_name);

        return parent::verifyUserCanAccessChannel(
            $request, $channelName
        );
    }

    /**
     * Return the valid authentication response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed $result
     * @return mixed
     */
    public function validAuthenticationResponse($request, $result)
    {
        if (is_bool($result)) {
            return json_encode($result);
        }

        return json_encode(['channel_data' => [
            'user_id' => $request->user()->getAuthIdentifier(),
            'user_info' => $result,
        ]]);
    }
}