<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Email;

use Ridibooks\Platform\Common\Email\Constant\EmailContentTypeConst;
use Ridibooks\Platform\Common\Email\Service\MailgunHelper;

class MailgunHelperTest extends AbstractEmailTest
{
    public function testSend(): void
    {
        $instance = MailgunHelper::getInstance();

        $result = $instance->send(
            'no-reply@ridibooks.com',
            ['no-reply@ridibooks.com'],
            'test',
            'test',
            EmailContentTypeConst::TEXT_PLAIN,
            ['no-reply-cc@ridibooks.com'],
            ['no-reply-bcc@ridibooks.com'],
            ['test' => __DIR__ . '/../../LICENSE']
        );
        $this->assertTrue($result);
    }

    public function testEmailValidation(): void
    {
        $instance = MailgunHelper::getInstance();

        $this->assertTrue($instance->isVaildEmail('test@ridibooks.com'));
    }
}
