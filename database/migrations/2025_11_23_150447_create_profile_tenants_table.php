<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profile_tenants', function (Blueprint $table) {
            $table->id();
             $table->string('first_name');
            $table->string('last_name');
            $table->string('profile_photo');
            $table->date('date_pirth');
            $table->string('identity_photo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_tenants');
    }
};
