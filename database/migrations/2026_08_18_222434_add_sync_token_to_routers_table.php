<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->string('sync_token', 64)->nullable()->unique()->after('id');
        });

        // Generate sync tokens for existing routers
        $routers = DB::table('routers')->get();
        foreach ($routers as $router) {
            DB::table('routers')->where('id', $router->id)->update([
                'sync_token' => Str::random(32)
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('routers', function (Blueprint $table) {
            $table->dropColumn('sync_token');
        });
    }
};

