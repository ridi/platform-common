<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated PagingUtil - Use Util\PagingUtil');
class_alias(\Ridibooks\Platform\Common\Util\PagingUtil::class, 'Ridibooks\Platform\Common\PagingUtil');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\PagingUtil */
    class PagingUtil extends \Ridibooks\Platform\Common\Util\PagingUtil
    {
    }
}
