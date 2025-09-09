<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Telegram\UpdateChatInfo;
use App\Models\TelegramChat;
use Merlion\Components\Table\Columns\Actions\Action;
use Merlion\Http\Controllers\CrudController;

class TelegramChatController extends CrudController
{
    protected string $model = TelegramChat::class;
    protected string $route = 'telegram-chats';

    public function updateInfo($id)
    {
        $chat = TelegramChat::findOrFail($id);
        UpdateChatInfo::run($chat);
        return back();
    }

    public function getRowActions(): array
    {
        $actions   = parent::getRowActions();
        $actions[] = Action::make('update-info')->label('Update Info')
            ->rendering(function ($action) {
                $action->withAttributes(['href' => $this->route('update-info', $action->getModelKey())]);
            });;
        return $actions;
    }

    protected function schemas(): array
    {
        return [
            'id',
            'title' => [
                'filterable' => true,
            ],
            'type'  => [
                'type'    => 'select',
                'options' => [
                    'private'    => 'Private',
                    'group'      => 'Group',
                    'supergroup' => 'Supergroup',
                ],
            ],
        ];
    }
}
