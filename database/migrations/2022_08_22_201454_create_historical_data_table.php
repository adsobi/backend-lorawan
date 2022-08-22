<?php

use App\Models\EndNode;
use App\Models\Gateway;
use App\Models\HistoricalData;
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
        Schema::create('historical_data', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(EndNode::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Gateway::class)->constrained()->cascadeOnDelete();
            $table->text('data')->nullable();
            $table->float('snr');
            $table->integer('rssi');
            $table->enum('type', HistoricalData::TYPES);
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
        Schema::dropIfExists('historical_data');
    }
};
