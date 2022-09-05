<?php

use App\Models\StaffEmail;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_email_user', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(StaffEmail::class, 'staff_email_id')->constrained()->cascadeOnDelete()->uniqid();
            $table->foreignIdFor(User::class, 'user_id')->constrained()->cascadeOnDelete()->uniqid();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_email_user');
    }
};
