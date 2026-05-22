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
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email'); // Lưu tên file ảnh
            // Trạng thái xin đổi email: 'none' (không xin), 'pending' (đang chờ), 'approved' (đã duyệt)
            $table->string('email_change_status')->default('none')->after('avatar');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'email_change_status']);
        });
    }
    };
