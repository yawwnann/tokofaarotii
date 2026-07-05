<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('indonesia_provinces', function (Blueprint $table) {
            $table->string('island', 20)->nullable()->after('name');
        });

        DB::table('indonesia_provinces')->whereIn('id', ['11','12','13','14','15','16','17','18','19','21'])->update(['island' => 'Sumatra']);
        DB::table('indonesia_provinces')->whereIn('id', ['31','32','33','34','35','36'])->update(['island' => 'Jawa']);
        DB::table('indonesia_provinces')->whereIn('id', ['51','52','53'])->update(['island' => 'Bali_Nusa']);
        DB::table('indonesia_provinces')->whereIn('id', ['61','62','63','64','65'])->update(['island' => 'Kalimantan']);
        DB::table('indonesia_provinces')->whereIn('id', ['71','72','73','74','75','76'])->update(['island' => 'Sulawesi']);
        DB::table('indonesia_provinces')->whereIn('id', ['81','82'])->update(['island' => 'Maluku']);
        DB::table('indonesia_provinces')->whereIn('id', ['91','94'])->update(['island' => 'Papua']);
    }

    public function down(): void
    {
        Schema::table('indonesia_provinces', function (Blueprint $table) {
            $table->dropColumn('island');
        });
    }
};
