<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Exception;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Exception\MsgException;

class ExceptionTest extends TestCase
{
    public function testMsgException(): void
    {
        $message = "message";
        $exception = new MsgException($message);

        $this->assertEquals($message, $exception->getMessage());
        $this->assertEquals(false, $exception->shouldBeLogged());
    }
}
