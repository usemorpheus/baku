<?php

use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\ArticleController;
use App\Models\AdminUser;
use Merlion\Components\Form\Errors;
use Merlion\Components\Form\Fields\Button;
use Merlion\Components\Form\Fields\Space;
use Merlion\Components\Form\Fields\Text;
use Merlion\Components\Form\Form;

Route::group([
    'prefix'     => 'admin',
    'as'         => 'admin.',
    'middleware' => ['web', 'merlion'],
], function () {
    admin('admin')->routes();
    Route::group(['middleware' => 'merlion_auth'], function () {
        Route::get('/', function () {
            $card = \Merlion\Components\Container\Card::make();
            $form = Form::make()
                ->flex()->wrap()->gap(2)->alignItems('end')
                ->model(AdminUser::first());
            $form->fields([
                Text::make()->name('name')->label('Name')->rules('max:3'),
                Text::make()->name('password')
                    ->required()
                    ->label('Password')->value('')->password(),
                \Merlion\Components\Form\Fields\File::make()->name('avatar')->label('Avatar'),
                Space::make(full: true),
                Button::make(
                    icon: 'ri-save-line me-1',
                    label: __('merlion::base.save')
                ),
            ]);
            $card->body([Errors::make(), $form]);
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
