<?php

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
        Schema::create("refresh_tokens", function (Blueprint $table) {
            $table->id();
            $table->morphs("tokenable");
            $table->string("name");
            $table->string("token", 64)->unique();
            $table->timestamp("expires_at")->nullable();
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
        Schema::dropIfExists("refresh_tokens");
    }
};
