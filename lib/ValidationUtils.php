<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated ValidationUtils - Use Util\ValidationUtils');
class_alias(\Ridibooks\Platform\Common\Util\ValidationUtils::class, 'Ridibooks\Platform\Common\ValidationUtils');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\ValidationUtils */
    class ValidationUtils extends \Ridibooks\Platform\Common\Util\ValidationUtils
    {
    }
}
