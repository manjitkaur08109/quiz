<?php

namespace App\Jobs;

use App\Mail\ContactMail;
use App\Models\EmailModel;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Queueable , Dispatchable;
    public $data;
    public $subject;
    public $email;
    public $user_id;
    public $admin;
    
    /**
     * Create a new job instance.
     */
    public function __construct($data,$subject,$email, $user_id)
    {

        Log::info('SendEmailJob is handling');
        Log::info('SendEmailJob data:'.json_encode($data));
        Log::info('SendEmailJob subject:'.$subject);
        Log::info('SendEmailJob email :'.$email);
        Log::info('SendEmailJob userId :'.$user_id);
        
        $this->data = $data;
        $this->subject = $subject;
        $this->email = $email;
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            Mail::to($this->email)->send(new ContactMail( $this->subject , $this->data));
            $status = EmailModel::SUCCESS;
        }catch(\Exception $e){
          $status = EmailModel::FAILED;

        }

        EmailModel::create([
            'user_id' => $this->user_id,
            'email' => $this->email,
            'status' => $status,
            'subject' => $this->subject,
            'message' => $this->data['message']
        ]);

                $admin = User::role('role_id',getRoleName('admin'))->first();
        Log::info('SendEmailJob admin:'.json_encode($admin));

        if ($admin) {
            $adminStatus = EmailModel::PENDING ;
        try{
                Mail::to($admin->email)->send(new ContactMail($this->subject, $this->data));
                $adminStatus = EmailModel::SUCCESS;
            }catch(\Exception $e){
                $adminStatus = EmailModel::FAILED;
            }
              EmailModel::create([
            'admin_id' => $admin->id,
            'email' => $admin->email,
            'status' => $adminStatus,
            'subject' => $this->subject,
            'message' => $this->data['message']
        ]);
        }
       
    }
}
