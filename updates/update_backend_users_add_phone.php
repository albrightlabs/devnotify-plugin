<?php namespace AlbrightLabs\DevNotify\Updates;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

class UpdateBackenDUsersAddPhone extends Migration
{
    public function up()
    {
        Schema::table('backend_users', function($table) {
            $table->string('phone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('backend_users', function($table) {
            $table->dropColumn('phone');
        });
    }
}
