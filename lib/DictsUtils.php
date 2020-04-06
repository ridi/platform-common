<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated DictsUtils - Use Util\DictsUtils');
class_alias(\Ridibooks\Platform\Common\Util\DictsUtils::class, 'Ridibooks\Platform\Common\DictsUtils');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\DictsUtils */
    class DictsUtils extends \Ridibooks\Platform\Common\Util\DictsUtils
    {
    }
}
