<?php //-->

use Cradle\Framework\App;
use Cradle\Framework\Flow;

return App::i()
    ->get('/search'         , 'App Search Page')
    ->get('/detail/:app_id' , 'App Detail Page')
    ->get('/create'         , 'App Create Page')
    ->get('/update/:app_id' , 'App Update Page')
    ->get('/remove/:app_id' , 'App Remove Process')
    ->get('/restore/:app_id', 'App Restore Process')
    ->post('/create'        , 'App Create Process')
    ->post('/update/:app_id', 'App Update Process')

    //renders a table display
    ->flow('App Search Page',
        Flow::schema('app')->search->load,
        Flow::schema('app')->search->format,
        Flow::schema('app')->search->render,
        Flow::www()->template->body('display'),
        Flow::www()->template->page
    )

    //renders a detail display
    ->flow('App Detail Page',
        Flow::schema('app')->detail->load,
        [
            Flow::schema('app')->detail->found,
            Flow::schema('app')->detail->format,
            Flow::schema('app')->detail->render,
            Flow::www()->template->body('display'),
            Flow::www()->template->page
        ],
        [
            Flow::schema('app')->detail->notFound,
            Flow::session()->error('Invalid ID'),
            Flow::session()->redirectTo('/app/search')
        ]
    )

    //renders the create form
    ->flow(
        'App Create Page',
        Flow::schema('app')->create->load,
        Flow::schema('app')->create->format,
        Flow::schema('app')->create->render,
        Flow::www()->template->body('form'),
        Flow::www()->template->page
    )

    //renders the update form
    ->flow(
        'App Update Page',
        Flow::schema('app')->update->load,
        [
            Flow::schema('app')->update->found,
            Flow::schema('app')->update->format,
            Flow::schema('app')->update->render,
            Flow::www()->template->body('form'),
            Flow::www()->template->page
        ],
        [
            Flow::schema('app')->update->notFound,
            Flow::session()->error('Invalid ID'),
            Flow::session()->redirectTo('/app/search')
        ]
    )

    //the process to insert into the DB
    ->flow(
        'App Create Process',
        Flow::schema('app')->create->prepare,
        Flow::schema('app')->create->valid,
        [
            Flow::schema('app')->create->yes,
            Flow::schema('app')->create->task,
            Flow::session()->success('App Created'),
            Flow::session()->redirectTo('/app/search')
        ],
        [
            Flow::schema('app')->create->no,
            Flow::session()->error('There were some errors found'),
            'App Create Page'
        ]
    )

    //the process to update the DB
    ->flow(
        'App Update Process',
        Flow::schema('app')->update->prepare,
        Flow::schema('app')->update->valid,
        [
            Flow::schema('app')->update->yes,
            Flow::schema('app')->update->task,
            Flow::session()->success('App Updated'),
            Flow::session()->redirectTo('/app/search')
        ],
        [
            Flow::schema('app')->update->no,
            Flow::session()->error('There were some errors found'),
            'App Update Page'
        ]
    )

    //the process to remove from the DB
    ->flow(
        'App Remove Process',
        Flow::schema('app')->remove->valid,
        [
            Flow::schema('app')->remove->yes,
            Flow::schema('app')->remove->task,
            Flow::session()->success('App Removed'),
        ],
        [
            Flow::schema('app')->remove->no,
            Flow::session()->error('Invalid ID'),
        ],
        Flow::session()->redirectTo('/app/search')
    )

    //the process to restore item in the DB
    ->flow(
        'App Restore Process',
        Flow::schema('app')->restore->valid,
        [
            Flow::schema('app')->restore->yes,
            Flow::schema('app')->restore->task,
            Flow::session()->success('App Restored'),
        ],
        [
            Flow::schema('app')->restore->no,
            Flow::session()->error('Invalid ID'),
        ],
        Flow::session()->redirectTo('/app/search')
    );
