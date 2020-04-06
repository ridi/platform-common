<?php

namespace Ridibooks\Platform\Common;

trigger_error('Deprecated CsvResponse - Use Util\CsvResponse');
class_alias(\Ridibooks\Platform\Common\Util\CsvResponse::class, 'Ridibooks\Platform\Common\CsvResponse');

// code for IDE
if (\false) {
    /** @deprecated Change to \Ridibooks\Platform\Common\Util\CsvResponse */
    class CsvResponse extends \Ridibooks\Platform\Common\Util\CsvResponse
    {
    }
}
