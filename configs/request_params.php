<?php
/**
 * @author Serg Nochevny <sergey.nochevny@tstechpro.com>
 *
 * Date: 20.12.2018
 * Time: 18:19
 */
return [
    'trustedIpHeaders' => [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_CF_CONNECTING_IP',
        'HTTP_X_REAL_IP',
        'REMOTE_ADDR',
    ],
];