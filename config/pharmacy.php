<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pharmacy Operational Mode
    |--------------------------------------------------------------------------
    |
    | This value determines whether the pharmacy system operates as a single
    | location or supports multiple branches. Options: 'single' or 'multi'.
    |
    | In 'single' mode, branch management features are hidden and all data
    | is automatically associated with the default Main Branch.
    |
    | In 'multi' mode, full branch management and branch-specific reporting
    | features are available.
    |
    */
    'mode' => env('PHARMACY_MODE', 'single'),
];
