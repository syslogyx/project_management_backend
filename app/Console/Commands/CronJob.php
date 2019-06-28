<?php

namespace App\Console\Commands;

use App\MailUtility;
use App\Milestone;
use Illuminate\Console\Command;

class CronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CronJob:cronjob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'User Name Change Successfully';

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
    public function handle()
    {
        //get todays date
        $todayDate = date("Y-m-d");

        //get next 4 days date
        $nextDate = date('Y-m-d', strtotime($todayDate . ' + 4 day'));

        // $milestonesList = \DB::table('milestones')
        //     ->whereBetween('due_date',array('NOW()', 'NOW() + INTERVAL 4 DAY'))
        //     ->get();

        //get milestones list
        $milestones = Milestone::orderBy('updated_at', 'desc')->with('project', 'status', 'project.projectResource.user')
            ->whereBetween('due_date', array($todayDate . ' 00:00:00', $nextDate . ' 00:00:00'))
            ->get();

        // $this->info($milestones);

        //do nothing if milestone list is empty
        if ($milestones != null && !empty($milestones)) {

            //set email address array
            $email = ['kalyani@syslogyx.com'];

            foreach ($milestones as $key => $value) {
                if ($value->project != null && $value->project->project_resource != null && $value->project->project_resource->user != null) {

                    //send an email to the respective manager user
                    if ($value->project->project_resource->role == 'Manager') {
                        array_push($email, $value->project->project_resource->user->email);
                    }
                }
            }

            //check the emai array is empty or not if it is then do nothing
            if (!empty($email)) {
                $mailStatus = MailUtility::sendMail("Milestone Finish", "Milestone is about to finish.", $email);

                echo $mailStatus;
            }
        } else {
            $this->info('Empty email list!');
        }
    }
}
