<?php



use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTblPbaPrnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbl_pba_prns', function (Blueprint $table) {
            $table->integer('id')->nullable();
            $table->integer('sai')->nullable();
            $table->integer('percentile_ranks')->nullable();
            $table->integer('stanines')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbl_pba_prns');
    }
}
