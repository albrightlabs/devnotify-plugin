
# 🔔 Dev Notify Plugin

### 🚨 Requires OctoberCMS 2.0

## ✨ What does this plugin do?
Provides the ability to notify developers of error logs via email or SMS.

## ❓ Why would I use this plugin?
Helpful for notifying administrators of error logs being created by the application without them having to login and go to the event log.
Also helpful if a developer needs to be notified of a specific issue, as it provides a helper for manually sending error notifications.

## 🖥️ How do I install this plugin?
1. Clone this repository into `plugins/albrightlabs/devnotify`
2. Run the console command `php artisan october:migrate`
3. From the admin area, go to Settings > Dev Notify Options and configure the plugin

## ⏫ How do I update this plugin?
Run either of the following commands:
* From the project root, run `php artisan october:util git pull`
* From the plugin root, run `git pull`

## 🚨 Are there any requirements for this plugin?
If you enable SMS notifications, you'll need an active, funded Twilio account and phone number.
You'll also need to add a phone number for each admin you wish to notify via SMS. This can be done via an admin's profile.

## 🔥 How to manually send notifications
1. Add `use Albrightlabs\DevNotify\Classes\NotificationManager` to class where notification is to be sent from
2. Call the function `NotificationManager::send(MESSAGE_CONTENT)`, where MESSAGE_CONTENT is the content of your notification

## ⚙️ Explanation of settings
* Email and SMS notifications can be enabled individually or together
* Notifications can be sent to any backend users selected in the list
* When SMS is enabled, you'll need to provide the SID, Token, and a phone number active in your Twilio account
* You can add the error log message to the email notification by enabling the option
* You can prepend the application name to the email notification subject and SMS content if you manage multiple applications
* Use the "Test Configuration" link to test the configuration. Be sure to save your settings first!

## ✨ Future plans
* Add option to only notify if logs meet or exceed a specific level
