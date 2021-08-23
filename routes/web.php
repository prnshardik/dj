<?php

use Illuminate\Support\Facades\Route;

Route::get('command/clear', function() {
    Artisan::call('cache:clear');
    Artisan::call('optimize:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "config, cache, and view cleared successfully";
});

Route::get('command/config', function() {
    Artisan::call('config:cache');
    return "config cache successfully";
});

Route::get('command/key', function() {
    Artisan::call('key:generate');
    return "Key generate successfully";
});

Route::get('command/migrate', function() {
    Artisan::call('migrate:refresh');
    return "Database migration generated";
});

Route::get('command/seed', function() {
    Artisan::call('db:seed');
    return "Database seeding generated";
});

Route::group(['middleware' => ['prevent-back-history']], function(){
    Route::group(['middleware' => ['guest']], function () {
        Route::get('/', 'AuthController@login')->name('login');
        Route::post('signin', 'AuthController@signin')->name('signin');

        Route::get('forget-password', 'AuthController@forget_password')->name('forget.password');
        Route::post('password-forget', 'AuthController@password_forget')->name('password.forget');
        Route::get('reset-password/{string}', 'AuthController@reset_password')->name('reset.password');
        Route::post('recover-password', 'AuthController@recover_password')->name('recover.password');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('logout', 'AuthController@logout')->name('logout');

        Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
        Route::get('fix_item_qr', 'TestController@fix_item_qr')->name('fix_item_qr');
        Route::get('fix_subitem_qr', 'TestController@fix_subitem_qr')->name('fix_subitem_qr');
        Route::get('fix_item_inventory_qr', 'TestController@fix_item_inventory_qr')->name('fix_item_inventory_qr');
        Route::get('fix_subitem_inventory_qr', 'TestController@fix_subitem_inventory_qr')->name('fix_subitem_inventory_qr');
        
        /** users */
            Route::any('users', 'UsersController@index')->name('users');
            Route::get('users/create', 'UsersController@create')->name('users.create');
            Route::post('users/insert', 'UsersController@insert')->name('users.insert');
            Route::get('users/view/{id?}', 'UsersController@view')->name('users.view');
            Route::get('users/edit/{id?}', 'UsersController@edit')->name('users.edit');
            Route::patch('users/update', 'UsersController@update')->name('users.update');
            Route::post('users/change-status', 'UsersController@change_status')->name('users.change.status');
            Route::post('users/remove-image', 'UsersController@remove_image')->name('users.remove.image');
        /** users */

        /** items - module */
            /** items-categories */
                Route::any('items-categories', 'ItemsCategoriesController@index')->name('items.categories');
                Route::get('items-categories/create', 'ItemsCategoriesController@create')->name('items.categories.create');
                Route::post('items-categories/insert', 'ItemsCategoriesController@insert')->name('items.categories.insert');
                Route::get('items-categories/view/{id?}', 'ItemsCategoriesController@view')->name('items.categories.view');
                Route::get('items-categories/edit/{id?}', 'ItemsCategoriesController@edit')->name('items.categories.edit');
                Route::patch('items-categories/update', 'ItemsCategoriesController@update')->name('items.categories.update');
                Route::post('items-categories/change-status', 'ItemsCategoriesController@change_status')->name('items.categories.change.status');
            /** items-categories */

            /** items */
                Route::any('items', 'ItemsController@index')->name('items');
                Route::get('items/create', 'ItemsController@create')->name('items.create');
                Route::post('items/insert', 'ItemsController@insert')->name('items.insert');
                Route::get('items/view/{id?}', 'ItemsController@view')->name('items.view');
                Route::get('items/edit/{id?}', 'ItemsController@edit')->name('items.edit');
                Route::patch('items/update', 'ItemsController@update')->name('items.update');
                Route::post('items/change-status', 'ItemsController@change_status')->name('items.change.status');

                Route::post('items/remove-image', 'ItemsController@remove_image')->name('items.remove.image');
                Route::get('items/print/{id?}', 'ItemsController@print')->name('items.print');
            /** items */

            /** items-inventoies */
                Route::any('items-inventories', 'ItemsInventoriesController@index')->name('items.inventories');
                Route::get('items-inventories/create', 'ItemsInventoriesController@create')->name('items.inventories.create');
                Route::post('items-inventories/insert', 'ItemsInventoriesController@insert')->name('items.inventories.insert');
                Route::get('items-inventories/view/{id?}', 'ItemsInventoriesController@view')->name('items.inventories.view');
                Route::get('items-inventories/edit/{id?}', 'ItemsInventoriesController@edit')->name('items.inventories.edit');
                Route::patch('items-inventories/update', 'ItemsInventoriesController@update')->name('items.inventories.update');
                Route::post('items-inventories/change-status', 'ItemsInventoriesController@change_status')->name('items.inventories.change.status');

                Route::get('items-inventories/print/{id?}', 'ItemsInventoriesController@print')->name('items.inventories.print');

                Route::post('items-inventories/remove-image', 'ItemsInventoriesController@remove_image')->name('items.inventories.remove.image');

                Route::get('items-inventories/items', 'ItemsInventoriesController@items')->name('items.inventories.items');
                Route::get('items-inventories/items/delete', 'ItemsInventoriesController@items_delete')->name('items.inventories.items.delete');
            /** items-inventoies */
        /** items - module */

        /** sub-items - module */
            /** sub-items-categories */
                Route::any('sub-items-categories', 'SubItemsCategoriesController@index')->name('sub.items.categories');
                Route::get('sub-items-categories/create', 'SubItemsCategoriesController@create')->name('sub.items.categories.create');
                Route::post('sub-items-categories/insert', 'SubItemsCategoriesController@insert')->name('sub.items.categories.insert');
                Route::get('sub-items-categories/view/{id?}', 'SubItemsCategoriesController@view')->name('sub.items.categories.view');
                Route::get('sub-items-categories/edit/{id?}', 'SubItemsCategoriesController@edit')->name('sub.items.categories.edit');
                Route::patch('sub-items-categories/update', 'SubItemsCategoriesController@update')->name('sub.items.categories.update');
                Route::post('sub-items-categories/change-status', 'SubItemsCategoriesController@change_status')->name('sub.items.categories.change.status');
            /** sub-items-categories */

            /** sub-items */
                Route::any('sub-items', 'SubItemsController@index')->name('sub.items');
                Route::get('sub-items/create', 'SubItemsController@create')->name('sub.items.create');
                Route::post('sub-items/insert', 'SubItemsController@insert')->name('sub.items.insert');
                Route::get('sub-items/view/{id?}', 'SubItemsController@view')->name('sub.items.view');
                Route::get('sub-items/edit/{id?}', 'SubItemsController@edit')->name('sub.items.edit');
                Route::patch('sub-items/update', 'SubItemsController@update')->name('sub.items.update');
                Route::post('sub-items/change-status', 'SubItemsController@change_status')->name('sub.items.change.status');

                Route::post('sub-items/remove-image', 'SubItemsController@remove_image')->name('sub.items.remove.image');
                Route::get('sub-items/print/{id?}', 'SubItemsController@print')->name('sub.items.print');
            /** sub-items */

            /** sub-items-inventories */
                Route::any('sub-items-inventories', 'SubItemsInventoriesController@index')->name('sub.items.inventories');
                Route::get('sub-items-inventories/create', 'SubItemsInventoriesController@create')->name('sub.items.inventories.create');
                Route::post('sub-items-inventories/insert', 'SubItemsInventoriesController@insert')->name('sub.items.inventories.insert');
                Route::get('sub-items-inventories/view/{id?}', 'SubItemsInventoriesController@view')->name('sub.items.inventories.view');
                Route::get('sub-items-inventories/edit/{id?}', 'SubItemsInventoriesController@edit')->name('sub.items.inventories.edit');
                Route::patch('sub-items-inventories/update', 'SubItemsInventoriesController@update')->name('sub.items.inventories.update');
                Route::post('sub-items-inventories/change-status', 'SubItemsInventoriesController@change_status')->name('sub.items.inventories.change.status');

                Route::get('sub-items-inventories/print/{id?}', 'SubItemsInventoriesController@print')->name('sub.items.inventories.print');

                Route::post('sub-items-inventories/remove-image', 'SubItemsInventoriesController@remove_image')->name('sub.items.inventories.remove.image');

                Route::get('sub-items-inventories/sub-items', 'SubItemsInventoriesController@items')->name('sub.items.inventories.items');
                Route::get('sub-items-inventories/sub-items/delete', 'SubItemsInventoriesController@items_delete')->name('sub.items.inventories.items.delete');
            /** sub-items-inventories */
        /** sub-items - module */

        /** cart */
            Route::any('cart', 'CartController@index')->name('cart');
            Route::get('cart/create', 'CartController@create')->name('cart.create');
            Route::post('cart/insert', 'CartController@insert')->name('cart.insert');
            Route::get('cart/view/{id?}', 'CartController@view')->name('cart.view');
            Route::get('cart/edit/{id?}', 'CartController@edit')->name('cart.edit');
            Route::post('cart/update', 'CartController@update')->name('cart.update');
            Route::post('cart/change-status', 'CartController@change_status')->name('cart.change.status');

            Route::get('cart/detail', 'CartController@detail')->name('cart.detail');

            Route::get('cart/users', 'CartController@users')->name('cart.users');
            Route::get('cart/sub_users', 'CartController@sub_users')->name('cart.sub.users');
            Route::get('cart/inventories', 'CartController@inventories')->name('cart.inventories');
            Route::get('cart/delete_inventories', 'CartController@delete_inventories')->name('cart.delete.inventories');
            Route::get('cart/sub_inventories', 'CartController@sub_inventories')->name('cart.sub_inventories');
            Route::get('cart/delete_sub_inventories', 'CartController@delete_sub_inventories')->name('cart.delete.sub_inventories');
        /** cart */

        /** log */
            Route::any('logs', 'LogController@index')->name('logs');
        /** log */

        /** print qrcodes */
            Route::any('prints', 'PrintsController@index')->name('prints');
            Route::any('prints/print', 'PrintsController@print')->name('prints.print');
        /** print qrcodes */
    });

    Route::get("{path}", function(){ return redirect()->route('login'); })->where('path', '.+');
});