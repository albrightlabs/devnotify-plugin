<?php namespace AlbrightLabs\DevNotify\Classes;

use Mail;
use Config;
use Backend\Models\User;
use Twilio\Rest\Client as TwilioClient;
use AlbrightLabs\DevNotify\Models\Settings;

/**
 * Message Class
 */
class Notification
{
    /**
     * Sends an SMS or email notification
     *
     * arributes: content
     *
     * usage: add use Albrightlabs\DevNotify\Classes\Notification, then to send message,
     * call the Notification::send(MESSAGE_CONTENT).
     */
    public static function send($content, $log = null)
    {

        // get admins froms settings
        $admins = Settings::get('administrators');

        // sent email if enabled
        if(Settings::get('email_notifications_enabled')){
            $subject = 'New error logged!';
            if(Settings::get('prefix_application_name_to_notification')){
                if($app_name = Config::get('app.name')){
                    $subject = $app_name.': '.$subject;
                }
            }
            foreach($admins as $admin_id){
                if($admin = User::find($admin_id)){
                    $admin_name = '';
                    if(null != $admin->first_name){
                        $admin_name .= $admin->first_name;
                    }
                    if(null != $admin->first_name && null != $admin->last_name){
                        $admin_name .= ' ';
                    }
                    if(null != $admin->first_name){
                        $admin_name .= $admin->last_name;
                    }
                    $from = array();
                    $from['name']  = Config::get('mail.from.name');
                    $from['email'] = Config::get('mail.from.address');
                    $vars = [
                        'content' => $content,
                    ];
                    if(Settings::get('append_log_to_email') && null != $log){
                        $vars['log'] = $log ?? null;
                    }
                    Mail::send('albrightlabs.devnotify::mail.notification', $vars, function($message) use ($from, $admin, $admin_name, $subject) {
                        $message->from($from['email'], $from['name']);
                        $message->to($admin->email, $admin_name);
                        $message->subject($subject);
                    });

                }
            }
        }

        // send sms if enabled
        if(Settings::get('enable_sms_notifications')){
            if(
                $sid = Settings::get('twilio_sid') &&
                $token = Settings::get('twilio_token') &&
                $number = Settings::get('twilio_number')
            ){
                $number = str_replace('(','',str_replace(')','',str_replace('-','',str_replace('+1','',$number))));
                $twilio = new TwilioClient($sid = Settings::get('twilio_sid'), $token = Settings::get('twilio_token'));
                if(Settings::get('prefix_application_name_to_notification')){
                    if($app_name = Config::get('app.name')){
                        $content = $app_name.': '.$content;
                    }
                }
                foreach($admins as $admin_id){
                    if($admin = User::find($admin_id)){
                        if(null != $admin->phone){
                            $to = str_replace('(','',str_replace(')','',str_replace('-','',str_replace('+1','',$admin->phone))));
                            $message = $twilio->messages->create(
                                '+1'.$to,
                                [
                                    'from' => '+1'.$number,
                                    'body' => $content,
                                ]
                            );
                        }
                    }
                }
            }
        }

    }
}
