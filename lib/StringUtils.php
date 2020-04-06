<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated StringUtils - Use Util\StringUtils');
class_alias(\Ridibooks\Platform\Common\Util\StringUtils::class, 'Ridibooks\Platform\Common\StringUtils');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\StringUtils */
    class StringUtils extends \Ridibooks\Platform\Common\Util\StringUtils
    {
    }
}
