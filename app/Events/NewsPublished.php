<?php

namespace App\Events;

use App\Models\News;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewsPublished
{
    use Dispatchable, SerializesModels;

    public News $news;
    public array $options;

    /**
     * Create a new event instance.
     */
    public function __construct(News $news, array $options = [])
    {
        $this->news = $news;
        $this->options = $options;
    }
}
