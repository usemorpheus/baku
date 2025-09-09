<?php


namespace App\Actions\Telegram;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateChatInfo
{
    use AsAction;

    public function handle($chat): void
    {
        $info = GetChat::run($chat->id);

        if (empty($info)) {
            return;
        }

        if ($info['type'] == 'private') {
            $chat->title = $info['first_name'] . ' ' . $info['username'];
        } else {
            $chat->title = $info['title'];
        }

        if (!empty($info['photo'])) {
            $photo = GetFile::run($info['photo']['small_file_id']);
            if (!empty($photo)) {
                $chat->photo = $photo;
            }
        }

        $chat->save();
        $chat->setMeta('info', $info);
    }

}
