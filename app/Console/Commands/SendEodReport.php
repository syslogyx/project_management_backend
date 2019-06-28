<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

class SendEodReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'eod:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send EOD Report of user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(Request $request)
    {
        app('App\Http\Controllers\ClientController')->cronjobEOD($request);
        $this->info('Hourly Update has been send successfully');
        // $email = ['monica.j@syslogyx.com'];
        // $user = User::all();
        // foreach ($user as $a)
        // {
        //     if(!empty($email)){
        //        $mailStatus = MailUtility::sendMail("Milestone Finish", "Milestone is about to finish.", $email);

        //        echo $mailStatus;
        //     }
        // }
        // $this->info('Hourly Update has been send successfully');
    }
}
