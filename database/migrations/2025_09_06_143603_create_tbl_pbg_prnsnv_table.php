<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblPbgPrnsnvTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pbg_prnsnv', function (Blueprint $table) {
            $table->integer('id')->nullable();
            $table->integer('stanines')->nullable();
            $table->integer('percentile_ranks')->nullable();
            $table->integer('scaled_score')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pbg_prnsnv');
    }
}
