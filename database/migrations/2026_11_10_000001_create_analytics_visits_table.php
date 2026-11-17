<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('analytics_visits', function (Blueprint $table) {
            $table->id();
            $table->char('session_hash', 64);
            $table->string('path', 255);
            $table->string('source', 190);
            $table->string('device', 24);
            $table->string('browser', 32);
            $table->timestamp('visited_at')->index();
            $table->index(['session_hash', 'visited_at']);
            $table->index(['path', 'visited_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_visits');
    }
};
