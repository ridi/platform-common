<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Logger;

use Monolog\Handler\TestHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Logger\ElapsedTime;

class ElapsedTimeTest extends TestCase
{
    /** @dataProvider providerTestElapsedTime */
    public function testElapsedTimeTest(callable $job, callable $expected)
    {
        $group_title = 'test';
        $logger = new Logger($group_title);

        $handler = new TestHandler();
        $logger->pushHandler($handler);

        $elapsed_time = new ElapsedTime($group_title, $logger);

        $job($elapsed_time);
        $expected($handler);
    }

    public function providerTestElapsedTime(): array
    {
        return [
            'case 1. empty ' => [
                function (ElapsedTime $elapsed_time) {
                    // nothing
                },
                function (TestHandler $test_handler) {
                    $records = $test_handler->getRecords();
                    $this->assertCount(1, $records);
                }
            ],
            'case 2. just end' => [
                function (ElapsedTime $elapsed_time) {
                    $elapsed_time->endTotalElapsed();
                },
                function (TestHandler $test_handler) {
                    $records = $test_handler->getRecords();
                    $this->assertCount(2, $records);
                }
            ],
            'case 3. one subjob' => [
                function (ElapsedTime $elapsed_time) {
                    $elapsed_time->endElapsed('test subjob');
                    $elapsed_time->endTotalElapsed();
                },
                function (TestHandler $test_handler) {
                    $records = $test_handler->getRecords();
                    $this->assertCount(3, $records);
                }
            ],
            'case 3-1. one subjob, not end' => [
                function (ElapsedTime $elapsed_time) {
                    $elapsed_time->endElapsed('test subjob');
                },
                function (TestHandler $test_handler) {
                    $records = $test_handler->getRecords();
                    $this->assertCount(2, $records);
                }
            ],
        ];
    }
}
