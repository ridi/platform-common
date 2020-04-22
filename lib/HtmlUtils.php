<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated HtmlUtils - Use Util\HtmlUtils');
class_alias(\Ridibooks\Platform\Common\Util\HtmlUtils::class, 'Ridibooks\Platform\Common\HtmlUtils');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\HtmlUtils */
    class HtmlUtils extends \Ridibooks\Platform\Common\Util\HtmlUtils
    {
    }
}
