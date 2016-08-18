<?php //-->

use Cradle\Framework\App;
use Cradle\Framework\Flow;

return App::i()
    ->get('/search'             , 'Profile Search Page')
    ->get('/detail/:profile_id' , 'Profile Detail Page')
    ->get('/create'             , 'Profile Create Page')
    ->get('/update/:profile_id' , 'Profile Update Page')
    ->get('/remove/:profile_id' , 'Profile Remove Process')
    ->get('/restore/:profile_id', 'Profile Restore Process')
    ->post('/create'            , 'Profile Create Process')
    ->post('/update/:profile_id', 'Profile Update Process')

    //renders a table display
    ->flow('Profile Search Page',
        Flow::schema('profile')->search->load,
        Flow::schema('profile')->search->format,
        Flow::schema('profile')->search->render,
        Flow::www()->template->body('display'),
        Flow::www()->template->page
    )

    //renders a detail display
    ->flow('Profile Detail Page',
        Flow::schema('profile')->detail->load,
        [
            Flow::schema('profile')->detail->found,
            Flow::schema('profile')->detail->format,
            Flow::schema('profile')->detail->render,
            Flow::www()->template->body('display'),
            Flow::www()->template->page
        ],
        [
            Flow::schema('profile')->detail->notFound,
            Flow::session()->error('Invalid ID'),
            Flow::session()->redirectTo('/profile/search')
        ]
    )

    //renders the create form
    ->flow(
        'Profile Create Page',
        Flow::schema('profile')->create->load,
        Flow::schema('profile')->create->format,
        Flow::schema('profile')->create->render,
        Flow::www()->template->body('form'),
        Flow::www()->template->page
    )

    //renders the update form
    ->flow(
        'Profile Update Page',
        Flow::schema('profile')->update->load,
        [
            Flow::schema('profile')->update->found,
            Flow::schema('profile')->update->format,
            Flow::schema('profile')->update->render,
            Flow::www()->template->body('form'),
            Flow::www()->template->page
        ],
        [
            Flow::schema('profile')->update->notFound,
            Flow::session()->error('Invalid ID'),
            Flow::session()->redirectTo('/profile/search')
        ]
    )

    //the process to insert into the DB
    ->flow(
        'Profile Create Process',
        Flow::schema('profile')->create->prepare,
        Flow::schema('profile')->create->valid,
        [
            Flow::schema('profile')->create->yes,
            Flow::file()->fromFileInput,
            Flow::schema('profile')->create->task,
            Flow::session()->success('Profile Created'),
            Flow::session()->redirectTo('/profile/search')
        ],
        [
            Flow::schema('profile')->create->no,
            Flow::session()->error('There were some errors found'),
            'Profile Create Page'
        ]
    )

    //the process to update the DB
    ->flow(
        'Profile Update Process',
        Flow::schema('profile')->update->prepare,
        Flow::schema('profile')->update->valid,
        [
            Flow::schema('profile')->update->yes,
            Flow::file()->fromFileInput,
            Flow::schema('profile')->update->task,
            Flow::session()->success('Profile Updated'),
            Flow::session()->redirectTo('/profile/search')
        ],
        [
            Flow::schema('profile')->update->no,
            Flow::session()->error('There were some errors found'),
            'Profile Update Page'
        ]
    )

    //the process to remove from the DB
    ->flow(
        'Profile Remove Process',
        Flow::schema('profile')->remove->valid,
        [
            Flow::schema('profile')->remove->yes,
            Flow::schema('profile')->remove->task,
            Flow::session()->success('Profile Removed'),
        ],
        [
            Flow::schema('profile')->remove->no,
            Flow::session()->error('Invalid ID'),
        ],
        Flow::session()->redirectTo('/profile/search')
    )

    //the process to restore item in the DB
    ->flow(
        'Profile Restore Process',
        Flow::schema('profile')->restore->valid,
        [
            Flow::schema('profile')->restore->yes,
            Flow::schema('profile')->restore->task,
            Flow::session()->success('Profile Restored'),
        ],
        [
            Flow::schema('profile')->restore->no,
            Flow::session()->error('Invalid ID'),
        ],
        Flow::session()->redirectTo('/profile/search')
    );
