<?php
namespace App\Services;

use OpenAI\Client;

class FileSummarizer
{
    public function __construct(private Client $client) {}

    public function summarize(string $absolutePdfPath): string
    {
        $file = $this->client->files()->upload([
            'purpose' => 'assistants',
            'file'    => fopen($absolutePdfPath, 'r'),
        ]);
        $fileId = $file->id;

        /* 2) ایجاد Thread تازه */
        $thread   = $this->client->threads()->create();
        $threadId = $thread->id;


        $prompt = <<<TEXT
این فایل شامل محتوای یک نشریه یا مجله است. لطفاً ابتدا اسم حزب یا نشریه‌ای که این فایل را منتشر کرده مشخص کن و سپس:

- یک دیسکریپشن کوتاه و روان بنویس که مناسب نمایش در وب‌سایت باشد.
- توضیح را با جمله‌ای مثل «این شماره از نشریه ...» آغاز کن.
- فقط متن نهایی را بده، بدون تیتر، لینک، شماره صفحه یا ارجاع.
TEXT;

        $this->client->threads()->messages()->create($threadId, [
            'role'       => 'user',
            'content' => $prompt,
            'attachments'=> [
                [
                    'file_id' => $fileId,
                    'tools'   => [
                        ['type' => 'file_search'],
                    ],
                ],
            ],
        ]);

        $run = $this->client->threads()->runs()->create($threadId, [
            'assistant_id' => config('services.openai.assistant_id'),
        ]);

        while ($run->status !== 'completed') {
            sleep(2);
            $run = $this->client->threads()->runs()->retrieve($threadId, $run->id);
        }

        $messages = $this->client->threads()->messages()->list($threadId);
        $answer   = $messages->data[0]->content[0]->text->value ?? '';

        return trim($answer);
    }
}
