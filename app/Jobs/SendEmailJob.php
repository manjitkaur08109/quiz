<?php

namespace App\Jobs;

use App\Mail\ContactMail;
use App\Models\EmailModel;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Bus\Dispatchable;

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
        $this->data = $data;
        $this->subject = $subject;
        $this->email = $email;
        $this->user_id = $user_id;
        $this->admin = User::role('admin')->first();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try{
            Mail::to($this->email)->send(new ContactMail($this->data, $this->subject ,));
            $status = 'success';
        }catch(\Exception $e){
          $status = 'failed';

        }

        EmailModel::create([
            'user_id' => $this->user_id,
            'email' => $this->email,
            'status' => $status,
            'subject' => $this->subject,
            'message' => $this->data['message']
        ]);

        if ($this->admin) {
            $adminStatus = 'pending';
        try{
                Mail::to($this->email)->send(new ContactMail($this->subject, $this->data));
                $adminStatus = 'success';
            }catch(\Exception $e){
                $status = 'failed';
            }
              EmailModel::create([
            'admin_id' => $this->admin->id,
            'email' => $this->email,
            'status' => $adminStatus,
            'subject' => $this->subject,
            'message' => $this->data['message']
        ]);
        }
       
    }
}
