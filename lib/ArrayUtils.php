<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated ArrayUtils - Use Util\ArrayUtils');
class_alias(\Ridibooks\Platform\Common\Util\ArrayUtils::class, 'Ridibooks\Platform\Common\ArrayUtils');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\ArrayUtils */
    class ArrayUtils extends \Ridibooks\Platform\Common\Util\ArrayUtils
    {
    }
}
