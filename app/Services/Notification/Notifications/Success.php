<?php

namespace app\Services\Notification\Notifications;

use app\Services\Notification\Notification;

class Success implements Notification
{
    private $type = 'success';

    private $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function content()
    {
        return [
            'type' => $this->type,
            'content' => $this->content
        ];
    }
}
