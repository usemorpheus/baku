<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ArticleController;
use App\Models\AdminUser;
use Merlion\Components\Form\Errors;
use Merlion\Components\Form\Form;

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'middleware' => 'merlion',
], function () {
    admin('admin')->routes();
    Route::group(['middleware' => 'merlion_auth'], function () {
        Route::get('/', function () {
            $card = \Merlion\Components\Container\Card::make();
            $form = Form::make()
                ->flex()->wrap()->gap(2)->alignItems('end')
                ->model(AdminUser::first());
            $form->fields([
                \Merlion\Components\Form\Fields\Text::make()->name('name')->label('Name'),
                \Merlion\Components\Form\Fields\Text::make()->name('password')
                    ->required()
                    ->label('Password')->value('')->password(),
                \Merlion\Components\Form\Fields\File::make()->name('avatar')->label('Avatar'),
                \Merlion\Components\Form\Fields\Space::make(full: true),
                \Merlion\Components\Form\Fields\Button::make(
                    icon: 'ri-save-line me-1',
                    label: __('merlion::base.save')
                ),
            ]);
            $card->header(Errors::make());
            $card->body($form);
            return admin()->content($card)->render();
        })->name('home');

        Route::post('/', function () {
            $user = AdminUser::first();
            if (!\Illuminate\Support\Facades\Hash::check(request('password'), $user->password)) {
                return back()->withErrors([
                    'password' => '密码不对',
                ]);
            }
            admin()->success('更新成功');
            $user->update(request()->only(['name', 'avatar']));
            return back();
        });
        Route::resource('articles', ArticleController::class);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy'])->name('articles.delete');
        Route::resource('agents', AgentController::class);
        Route::delete('agents/{agent}', [AgentController::class, 'destroy'])->name('agents.delete');
    });
});
