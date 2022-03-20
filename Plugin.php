<?php namespace Albrightlabs\DevNotify;

use Route;
use Config;
use Backend;
use Redirect;
use System\Classes\PluginBase;
use System\Classes\SettingsManager;
use Albrightlabs\DevNotify\Classes\Notification;
use Albrightlabs\DevNotify\Models\Settings;
use Backend\Controllers\Users;
use Backend\Models\User;

/**
 * DevNotify Plugin Information File
 */
class Plugin extends PluginBase
{
    /**
     * Returns information about this plugin.
     *
     * @return array
     */
    public function pluginDetails()
    {
        return [
            'name'        => 'DevNotify',
            'description' => 'Provides the ability to notify developers of error logs via email or SMS.',
            'author'      => 'Albright Labs LLC',
            'icon'        => 'icon-bell-o',
            'icon-svg'    => '$/albrightlabs/devnotify/assets/images/plugin-icon.svg',
            'homepage'    => 'https://albrightlabs.com/',
        ];
    }

    /**
     * Boot method, called right before the request route.
     *
     * @return void
     */
    public function boot()
    {

        /**
         * Include the Twilio-PHP library
         */
        require_once('assets/vendor/twilio-php/Twilio/autoload.php');

        /**
         * Listen for when system/models/eventlog is created
         */
        \System\Models\EventLog::extend(function($model) {
            $model->bindEvent('model.afterCreate', function() use ($model) {
                // sends notification using the built-in Notification::send feature
                Notification::send('New error logged by the application! '.Config::get('app.url').'/backend/system/eventlogs/preview/'.$model->id, $model->message);
            });
        });

        /**
         * Add phone number to backend user form
         */
        Users::extendFormFields(function($form, $model, $context){
            if (!$model instanceof User)
                return;

            $form->addTabFields([
                'phone' => [
                    'label'   => 'Phone',
                    'type'    => 'text',
                    'span'    => 'auto',
                    'tab'     => 'backend::lang.user.account',
                ],
            ]);
        });

        /**
         * Add phone number to backend user list
         */
        Users::extendListColumns(function($list, $model){
            if (!$model instanceof User)
                return;

            $list->addColumns([
                'phone' => [
                    'label'     => 'Phone',
                    'invisible' => true,
                ],
            ]);
        });

        /**
         * Adds a configuration test
         */
        Route::get('/albrightlabs/devnotify/configuration/test', function () {
            if(Settings::get('append_log_to_email'))
                Notification::send('Test notification!', 'This is an example error log message...');
            else
                Notification::send('Test notification!');

            return Redirect::to(Config::get('app.url').'/backend/system/settings/update/albrightlabs/devnotify/devnotify');
        });

    }

    /**
     * Registers any back-end permissions used by this plugin.
     *
     * @return array
     */
    public function registerPermissions()
    {
        return [
            'albrightlabs.devnotify.manage_settings' => [
                'tab'   => 'Dev Notify',
                'label' => 'Manage the plugin settings'
            ],
        ];
    }

    /**
     * Registers any settings for this plugin.
     *
     * @return array
     */
    public function registerSettings()
    {
        return [
            'devnotify' => [
                'label'       => 'Dev Notify Options',
                'description' => 'Manage error log developer notifications.',
                'category'    => SettingsManager::CATEGORY_LOGS,
                'icon'        => 'icon-exclamation-triangle',
                'class'       => \Albrightlabs\DevNotify\Models\Settings::class,
                'order'       => 910,
                'keywords'    => 'dev notify options email sms',
                'permissions' => ['albrightlabs.devnotify::manage_settings',],
            ],
        ];
    }

}
