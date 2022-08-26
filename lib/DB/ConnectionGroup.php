<?php

namespace Ridibooks\Platform\Common\DB;

/**
 * @deprecated
 */
abstract class ConnectionGroup
{
    public const WRITE = 'write';                      // [bom@141] write
    public const READ_SERVICE = 'read';                // [bom@200:33306] read
    public const READ_CMS = 'read_cms';                // [bom@200:33307] read
    public const LOG = 'log';                          // [log@log-master(AWS)] write for log
    public const LOG_TRANSACTION = 'log_transaction';  // [log@52] write for payment transaction log
    public const LOG_DOWNLOAD = 'log_download';        // [log@52] write for download log
    public const LOG_READ = 'log_read';                // [log@log-slave(AWS)] read for log
    public const TRX = 'trx';                          // [trx@141] write for generate unique transaction id
    public const CRM_WRITE = 'crm_write';              // [crm@crm-master(AWS)] write for CRM
    public const CRM_READ = 'crm_read';                // [crm@crm-slave(AWS)] read for CRM
    public const DA_STAT_READ = 'da_stat_read';        // [da_stat@200:33309] read
    public const DA_READ = 'da_read';                  // [da@200:33309] read
}
