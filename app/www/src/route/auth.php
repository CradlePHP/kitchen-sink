<?php //-->

use Cradle\Framework\App;
use Cradle\Framework\Flow;

return App::i()
    ->get('/search'             , 'Auth Search Page')
    ->get('/create'             , 'Auth Create Page')
    ->get('/update'             , 'Update Account Page')
    ->get('/update/:auth_id'    , 'Auth Update Page')
    ->get('/login'              , 'Auth Login Page')
    ->get('/logout'             , 'Auth Logout Process')
    ->get('/remove/:auth_id'    , 'Auth Remove Process')
    ->get('/restore/:auth_id'   , 'Auth Restore Process')
    ->get('/refresh/:auth_id'   , 'Auth Refresh Process')
    ->post('/update/:auth_id'   , 'Update Account Process')
    ->post('/create'            , 'Auth Create Process')
    ->post('/update'            , 'Auth Update Process')
    ->post('/login'             , 'Auth Login Process')

    ->flow(
        'Auth Search Page',
        Flow::auth()->search->load,
        Flow::auth()->search->format,
        Flow::forward(),
        Flow::auth()->search->render,
        Flow::www()->template->body('display'),
        Flow::www()->template->page
    )
    ->flow(
        'Auth Create Page',
        Flow::auth()->create->load,
        Flow::auth()->create->format,
        Flow::auth()->create->render,
        Flow::www()->template->body('form'),
        Flow::www()->template->page
    )
    ->flow(
        'Update Account Page',
        Flow::auth()->update->load,
        [
            Flow::auth()->update->found,
            Flow::auth()->update->format,
            Flow::auth()->update->render,
            Flow::www()->template->body('form'),
            Flow::www()->template->page
        ],
        [
            Flow::auth()->update->notFound,
            Flow::session()->error('Auth does not exist'),
            Flow::session()->redirectTo('/auth/search')
        ]
    )
    ->flow(
        'Auth Update Page',
        Flow::auth()->required->check,
        [
            Flow::auth()->required->yes,
            Flow::auth()->update->load,
            Flow::auth()->update->format,
            Flow::auth()->update->render,
            Flow::www()->template->body('form'),
            Flow::www()->template->page
        ],
        [
            Flow::auth()->required->no,
            Flow::session()->error('Invalid Permissions'),
            Flow::session()->redirectTo('/auth/search')
        ]
    )
    ->flow(
        'Auth Login Page' ,
        Flow::auth()->login->load,
        Flow::auth()->login->format,
        Flow::auth()->login->render,
        Flow::www()->template->body('form'),
        Flow::www()->template->page
    )
    ->flow(
        'Auth Logout Process',
        Flow::auth()->logout(),
        Flow::session()->success('Logout Successful'),
        Flow::session()->redirectTo('/sink')
    )

    ->flow(
        'Auth Create Process',
        Flow::auth()->create->prepare,
        Flow::auth()->create->valid,
        [
            Flow::auth()->create->yes,
            Flow::auth()->create->task,
            //now create profile
            Flow::schema('profile')->create->prepare,
            Flow::schema('profile')->create->valid,
            [
                Flow::schema('profile')->create->yes,
                Flow::schema('profile')->create->task,
                //now bring all the results to stage
                Flow::reset(),
                Flow::schema('auth')->link->to('profile'),
                Flow::session()->success('Create Successful'),
                Flow::session()->redirectTo('/sink')
            ]
        ],
        Flow::session()->error('Some errors were found'),
        'Auth Create Page'
    )

    ->flow(
        'Auth Update Process',
        Flow::auth()->required->check,
        [
            Flow::auth()->required->yes,
            Flow::auth()->update->prepare,
            Flow::auth()->update->valid,
            [
                Flow::auth()->update->yes,
                Flow::auth()->update->task,
                Flow::session()->success('Update Successful'),
                Flow::session()->redirectTo('/sink')
            ],
            [
                Flow::auth()->update->no,
                Flow::session()->error('Something went wrong'),
                'Auth Update Page'
            ]
        ],
        [
            Flow::auth()->required->no,
            Flow::session()->error('Invalid Permissions'),
            Flow::session()->redirectTo('/auth/search')
        ]
    )

    ->flow(
        'Update Account Process',
        Flow::auth()->update->prepare,
        Flow::auth()->update->valid,
        [
            Flow::auth()->update->yes,
            Flow::auth()->update->task,
            Flow::session()->success('Update Successful'),
            Flow::session()->redirectTo('/auth/search')
        ],
        [
            Flow::auth()->update->no,
            Flow::session()->error('Something went wrong'),
            'Auth Update Me Page'
        ]
    )

    ->flow(
        'Auth Login Process',
        Flow::auth()->login->prepare,
        Flow::auth()->login->valid,
        [
            Flow::auth()->login->yes,
            Flow::auth()->login->task,
            Flow::session()->success('Login Successful'),
            Flow::session()->redirectTo('/sink')
        ],
        Flow::session()->flash(),
        'Auth Login Page'
    )

    ->flow(
        'Auth Remove Process',
        Flow::auth()->remove->valid,
        [
            Flow::auth()->remove->yes,
            Flow::auth()->remove->task,
            Flow::schema('auth')->unlinkAll->from('profile'),
            Flow::session()->success('Auth Removed'),
        ],
        [
            Flow::auth()->remove->no,
            Flow::session()->error('Invalid ID'),
        ],
        Flow::session()->redirectTo('/sink')
    )

    ->flow(
        'Auth Restore Process',
        Flow::auth()->restore->valid,
        [
            Flow::auth()->restore->yes,
            Flow::auth()->restore->task,
            Flow::session()->success('Auth Restored'),
        ],
        [
            Flow::auth()->restore->no,
            Flow::session()->error('Invalid ID'),
        ],
        Flow::session()->redirectTo('/sink')
    )

    ->flow(
        'Auth Refresh Process',
        Flow::auth()->refresh->valid,
        [
            Flow::auth()->refresh->yes,
            Flow::auth()->refresh->task,
            Flow::session()->success('Auth Refreshed'),
        ],
        [
            Flow::auth()->refresh->no,
            Flow::session()->error('Invalid ID'),
        ],
        Flow::session()->redirectTo('/sink')
    );
