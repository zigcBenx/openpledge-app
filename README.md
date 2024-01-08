## Open pledge app

Open pledge is online marketplace for monetizing opensource. On one side donors can support open source issues with their donations and help with maintaing secure and updated project that they are possibly using. And on the other side developers that can solve issues having certain donation pledged and receive reward for their contribution.


## Stack

Project was initialy generated with [Laravel jetstream](https://jetstream.laravel.com/) starter pack, which provides basic design, authentication and some other perks. As scaffolding Inertia was chosen.

- **[Larave](https://laravel.com/docs/10.x)** -> Modern PHP framework with elegant syntax based on MVC.
- **[Inertia](https://inertiajs.com/)** -> Is for createing SPA(Single Page Applications) without need of separate API project - it is integrated into Laravel. In this project we use **[Vue](https://vuejs.org/)** version of inertia, which means every page can be presented as separate component.

For future developers that may not be that well familiar with Laravel, I can't suggest Laravel official documentation enough, it's an work of art with great examples and explanations.


### Backend
As mentioned, this project is based on Laravel.
Important building bloks:
- [Routes](https://laravel.com/docs/10.x/routing#main-content) -> found in `routes/web.php` file, where all routes are defined.
- Controllers -> found in `app/Http/Controllers/` where route is mapped to certain logic.
- Models -> found in `app/Models/`, where database tables are presented for later quering with ORM ([Eloquent](https://laravel.com/docs/10.x/eloquent))
- Actions -> found in `app/Actions/`, contains separated logic that would usually be in controller, but because of future possibility of APIs, we isolated this in separated files called actions, which are then called inside controllers.
- [Mirations](https://laravel.com/docs/10.x/migrations) -> found in `database/migrations/`, where all database migrations are saved.

#### Relations
Relations in laravel are defined on Models.
E.g.: We have simple example of Repositories and issues.
`One repository has many issues and one issue belongs to one repository.`

If you read previous sentence again you will find two important "batch" of words:
- **belongs to**
- **has many**

And with this relations are defined in Laravel.

```
Issue.php

    public function repository()
    {
        return $this->belongsTo(Repository::class);
    }
```

```
Repository.php
    public function issues()
    {
        return $this->hasMany(Issue::class);
    }
```

This was really simple example, make sure to read more about relations on [Laravel's official documentation](https://laravel.com/docs/10.x/eloquent-relationships).

Almost forgot....

A bit tricky relation to understand for newcomers are **[morph relations]**(https://laravel.com/docs/10.x/eloquent-relationships#polymorphic-relationships). Those are used when single model can be reused on different entities and you don't want to keep adding foreign keys. Good generic example could be addresses, those can be attached to users, partners, companies, customers,.... you get the point.

In our case we use Donations as morph relation, since we want to make this future proof, and there is strong possibility that donations could be related not only to issues (maybe repositories, users, etc.).

For example view `Donation.php` model and `donations` database table.


### Frontend
Frontend can be found in folder `resources`. Since this app is using Inertia, views are not where we render our frontend. Everything lies in `js` folder.

In `js` folder everything is mostly self explenatory. Folder components consists of reusable.. well components, like: buttons, input fields, modals etc. Folder Layouts holds one and only layout, that is used through out the whole application. And lastly we have folder Pages, where views for separate pages are used.

Defining which view should be displayed on which route, make sure to view controller.
For example of "issues":
    - We have route: `Route::resource('issues', IssueController::class)->only('index', 'show', 'store');` which actually creates 3 routes (index, show and store) which are called as functions in controller `Issuecontroller`.
    - Inside controller's index funciton, you can see the return of Inertia facade, called with function render. This just means that it renders `js/Pages/Issues/Index.vue` file.

    ```
        return Inertia::render('Issues/Index', [
            'issues' => $issues
        ]);
    ```

As you will find out, we are not consistent on using Options or Composition API in Vue components. This is because of my habbit of using options API and Jetstream came with Composition API presets...