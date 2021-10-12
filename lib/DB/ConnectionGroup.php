<?php

namespace Ridibooks\Platform\Common\DB;

/**
 * @deprecated
 */
abstract class ConnectionGroup
{
    const WRITE = 'write';                      // [bom@141] write
    const READ_SERVICE = 'read';                // [bom@200:33306] read
    const READ_CMS = 'read_cms';                // [bom@200:33307] read
    const LOG = 'log';                          // [log@log-master(AWS)] write for log
    const LOG_TRANSACTION = 'log_transaction';  // [log@52] write for payment transaction log
    const LOG_DOWNLOAD = 'log_download';        // [log@52] write for download log
    const LOG_READ = 'log_read';                // [log@log-slave(AWS)] read for log
    const TRX = 'trx';                          // [trx@141] write for generate unique transaction id
    const CRM_WRITE = 'crm_write';              // [crm@crm-master(AWS)] write for CRM
    const CRM_READ = 'crm_read';                // [crm@crm-slave(AWS)] read for CRM
    const DA_STAT_READ = 'da_stat_read';        // [da_stat@200:33309] read
    const DA_READ = 'da_read';                  // [da@200:33309] read
}
