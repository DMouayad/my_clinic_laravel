<?php

use App\Models\User;
use Domain\Users\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("staff_members", function (Blueprint $table) {
            $table->id();
            $table->string("email")->unique();
            $table->foreignIdFor(Role::class, "role_id")->constrained();
            $table->foreignIdFor(User::class, "user_id")->nullable();
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
        Schema::dropIfExists("staff_members");
    }
};
