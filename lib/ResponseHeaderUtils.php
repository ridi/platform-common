<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated ResponseHeaderUtils - Use Util\ResponseHeaderUtils');
class_alias(\Ridibooks\Platform\Common\Util\ResponseHeaderUtils::class, 'Ridibooks\Platform\Common\ResponseHeaderUtils');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\ResponseHeaderUtils */
    class ResponseHeaderUtils extends \Ridibooks\Platform\Common\Util\ResponseHeaderUtils
    {
    }
}
