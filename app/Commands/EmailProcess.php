<?php

namespace AbuseIO\Commands;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Filesystem\Filesystem;
use PhpMimeMailParser\Parser as MimeParser;
use AbuseIO\Parsers\Factory as FindParser;
use Config;
use Log;

class EmailProcess extends Command implements SelfHandling//, ShouldQueue
{
    //use InteractsWithQueue, SerializesModels;

    public $filename;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * Queue command into named tube.
     *
     * @return void
     */
    public function queue($queue, $command)
    {
        $queue->pushOn('emails', $command);
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {

        Log::info(get_class($this).': Queued worker is starting the processing of email file: ' . $this->filename);

        $filesystem = new Filesystem;
        $rawEmail = $filesystem->get($this->filename);

        $parsedMail = new MimeParser();
        $parsedMail->setText($rawEmail);

        // Sanity checks
        if (empty($parsedMail->getHeader('from')) || empty($parsedMail->getMessageBody())) {
            Log::warning(get_class($this).'Validation failed on: ' . $this->filename);
            $this->exception($this->filename, $rawEmail);
        }

        // Ignore email from our own notification address to prevent mail loops
        if (preg_match('/' . Config::get('main.notifications.from_address') . '/', $parsedMail->getHeader('from'))) {
            Log::warning(get_class($this).'Loop prevention: Ignoring email from self ' . Config::get('main.notifications.from_address'));
            $this->exception($this->filename, $rawEmail);
        }

        // Start with detecting valid ARF e-mail
        $attachments = $parsedMail->getAttachments();
        $arfMail = [ ];
        foreach ($attachments as $attachment) {
            if ($attachment->contentType == 'message/feedback-report') {
                $arfMail['report'] = $attachment->getContent();
            }
            if ($attachment->contentType == 'message/rfc822') {
                $arfMail['evidence'] = $attachment->getContent();
            }
            if ($attachment->contentType == 'text/plain') {
                $arfMail['message'] = $attachment->getContent();
            }
        }

        // call parser
        $parserName = FindParser::mapFrom($parsedMail->getHeader('from'));
        if (empty($parserName)) {
            $parserName = FindParser::mapBody($parsedMail->getMessageBody());
        }

        // If we haven't figured out which parser we're going to use, we will never find out so another rage quit
        if (empty($parserName)) {
            Log::error(get_class($this).': Unable to handle message from: ' . $parsedMail->getHeader('from') . ' with subject: ' . $parsedMail->getHeader('subject'));
            $this->exception($this->filename, $rawEmail);
        } else {
            Log::info(get_class($this).': Received message from: '. $parsedMail->getHeader('from') . ' with subject: \'' . $parsedMail->getHeader('subject') . '\' heading to parser: ' . $parserName);
        }

        $parser = new $parserName('config', 'parsed', 'arf');
        $result = $parser->parse();

        Log::info(get_class($this).': ' . $result['errorStatus'] . $result['errorMessage']);

        // call validater
        // call linker
        // call saver

    }

    /**
     * We've hit a snag, so we are gracefully killing ourselves after we contact the admin about it.
     *
     * @return mixed
     */
    protected function exception($filename, $rawEmail)
    {
        Log::error(get_class($this).': Ending with errors. The received e-mail will be deleted from archive and bounced to the admin for investigation');
        // TODO: send the rawEmail back to admin
        dd($filename . $rawEmail);
    }

    protected function getMapping()
    {
        //
    }

}