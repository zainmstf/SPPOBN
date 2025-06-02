<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRuleTrackingToKonsultasiTable extends Migration
{
    public function up()
    {
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->integer('current_rule_index')->default(0)->after('pertanyaan_terakhir');
            $table->json('failed_rules')->nullable()->after('current_rule_index');
        });
    }

    public function down()
    {
        Schema::table('konsultasi', function (Blueprint $table) {
            $table->dropColumn(['current_rule_index', 'failed_rules']);
        });
    }
}