<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated DateUtils - Use Util\DateUtils');
class_alias(\Ridibooks\Platform\Common\Util\DateUtils::class, 'Ridibooks\Platform\Common\DateUtils');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\DateUtils */
    class DateUtils extends \Ridibooks\Platform\Common\Util\DateUtils
    {
    }
}
