<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInteractionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interactions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('visitor')->index();

            $table->integer('interactionable_id')->index();
            $table->string('interactionable_type')->index();

            $table->string('category')->index();
            $table->date('date')->index();

            $table->timestamps();
            $table->unique(['visitor', 'interactionable_id', 'interactionable_type', 'date', 'category'], 'interactions_unique');
        });

        if (Schema::hasTable('visits')) {
            DB::statement("INSERT INTO interactions
                (visitor, interactionable_id, interactionable_type, `date`, created_at, updated_at, category)
                SELECT ip, visitable_id, visitable_type, `date`, created_at, updated_at, 'visit' FROM visits");
        }

        Schema::dropIfExists('visits');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hits');
    }
}
