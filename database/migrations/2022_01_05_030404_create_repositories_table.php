<?php

use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRepositoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Team::class);
            $table->string('name');
            // Slug and url columns
            $table->string('slug')->unique();
            $table->string('url', 2048);
            $table->boolean('is_public');
            $table->boolean('use_v_in_version');

            $table->string('public_key', 4096)->nullable();
            $table->string('private_key', 4096)->nullable();

            $table->dateTime('last_released_at')->nullable();
            $table->string('last_released_version')->nullable();
            
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
        Schema::dropIfExists('repositories');
    }
}
