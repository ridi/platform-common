<?php

namespace Ridibooks\Platform\Common\Util;

class_alias(\Ridibooks\Platform\Common\Sentry\SentryHelper::class, 'Ridibooks\Platform\Common\Util\SentryHelper');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Sentry\SentryHelper */
    class SentryHelper extends \Ridibooks\Platform\Common\Sentry\SentryHelper
    {
    }
}
