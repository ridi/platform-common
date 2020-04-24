<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Email;

use PHPUnit\Framework\TestCase;
use Ridibooks\Platform\Common\Email\Service\MailgunHelper;
use Ridibooks\Platform\Common\Tests\Helper\TestHydrator;

abstract class AbstractEmailTest extends TestCase
{
    public function setUp(): void
    {
        MailgunHelper::getInstance()
            ->setApiKey('test')
            ->setTestMode(true, 'http://bin.mailgun.net/0ab44a0c')
            ->setSendDomain('ridibooks.com')
            ->setHydrator(new TestHydrator());
    }
}
