<?php

return [
    'hosts' => explode(',', env('LDAP_HOST')),
    'base_dn' => env('LDAP_DN'),
    'suffix' => env('LDAP_SUFFIX'),
    'user' => env('LDAP_USER'),
    'password' => env('LDAP_PASS'),
];
