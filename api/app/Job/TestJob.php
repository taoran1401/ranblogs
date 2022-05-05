<?php

declare(strict_types=1);

namespace App\Job;

use Hyperf\AsyncQueue\Job;

class TestJob extends Job
{
    public function __construct()
    {
    }

    public function handle()
    {
        \App\Model\Test::insert([
            'data' => '2022-05-05'
        ]);
    }
}
