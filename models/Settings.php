<?php namespace Albrightlabs\DevNotify\Models;

use Model;
use Albrightlabs\DevNotify\Classes\Notification;

class Settings extends Model
{
    /**
     * @var array implement these behaviors
     */
    public $implement = [
        \System\Behaviors\SettingsModel::class
    ];

    /**
     * @var string settingsCode unique to this model
     */
    public $settingsCode = 'albrightlabs_devnotify_settings';

    /**
     * @var string settingsFields configuration
     */
    public $settingsFields = 'fields.yaml';

    /**
     * @var array returns a list of backend users
     */
    public function getAdministratorsOptions($value, $formData)
    {
        $return = array();
        if($admins = \Backend\Models\User::all()){
            foreach($admins as $admin){
                $admin_name = '';
                if(null != $admin->first_name){
                    $admin_name .= $admin->first_name;
                }
                if(null != $admin->first_name && null != $admin->last_name){
                    $admin_name .= ' ';
                }
                if(null != $admin->last_name){
                    $admin_name .= $admin->last_name;
                }
                if(null != $admin->email){
                    $admin_name .= ' - '.$admin->email;
                }
                if(null != $admin->phone){
                    $admin_name .= ' ('.$admin->phone.')';
                }
                $return[$admin->id] = $admin_name;
            }
        }
        return $return;
    }
}
