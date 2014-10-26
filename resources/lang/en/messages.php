<?php

/*
|--------------------------------------------------------------------------
| Message Language Lines
|--------------------------------------------------------------------------
|
| The following language lines are returned from API calls and inform the user
| if the action was successful or not.
|
*/

return [

    /*
    | ---------------
    | Basic
    | ---------------
    |
    | Language lines for basic resources
    */

    'basic' => [
        'created'             => ':resource Created',
        'updated'             => ':resource Updated',
        'deleted'             => ':resource Deleted',
        'deleteFailed'        => ':resource Could Not Be Deleted',
    ],

    /*
    | ---------------
    | SoftDelete
    | ---------------
    |
    | Language lines for soft deletable resources
    */

    'softDelete' => [
        'restored'            => ':resource Restored',
        'restoreFailed'       => 'Restore Failed',
        'forceDeleted'        => ':resource Deleted Forever',
        'forceDeleteFailed'   => 'Force Deleted Failed',
    ],

    /*
    | ---------------
    | Versionable
    | ---------------
    |
    | Language lines for versionable resources.
    */

    'versionable' => [
        'madeVersion'          => 'A New Version Was Created',
        'makeVersionFailed'    => 'A New Version Could Not Be Created',
        'clearedVersions'      => 'All Old Versions Were Cleared',
        'clearVersionsFailed'  => 'All Old Versions Could Not Be Cleared',
        'alreadyHead'          => 'That :resource Is Already The Head Version',
        'madeHead'             => 'That :resource is Now The Head Version',
        'makeHeadFailed'       => 'Could Not Make That Version the Head'
    ]

];