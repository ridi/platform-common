<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated ObjectsUtils - Use Util\ObjectsUtils');
class_alias(\Ridibooks\Platform\Common\Util\ObjectsUtils::class, 'Ridibooks\Platform\Common\ObjectsUtils');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\ObjectsUtils */
    class ObjectsUtils extends \Ridibooks\Platform\Common\Util\ObjectsUtils
    {
    }
}
