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
        'forceDeleted'        => ':resource Deleted Forever',
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
        'clearedVersions'      => 'All Old Versions Were Cleared',
        'alreadyHead'          => 'That :resource Is Already The Head Version',
        'madeHead'             => 'That :resource is Now The Head Version',
    ],

    /*
    | ---------------
    | Publishable
    | ---------------
    |
    | Messages related to publishable entities.
    */

    'publishable' => [
        'published' => ':resource Published',
        'unpublished' => ':resource Unpublished',
        'publishedSoMadeDraft' => 'You are now editing the draft version.',
        'alreadyDraft' => 'The :resource is already a draft.'
    ]

];