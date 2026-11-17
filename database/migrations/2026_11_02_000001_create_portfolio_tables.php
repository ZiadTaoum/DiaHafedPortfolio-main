<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('users', fn (Blueprint $table) => $table->boolean('is_admin')->default(false));
        Schema::create('sections', function (Blueprint $table) {
            $table->id(); $table->string('key')->unique(); $table->string('type');
            $table->string('title'); $table->string('subtitle')->nullable(); $table->text('body')->nullable();
            $table->json('data')->nullable(); $table->unsignedInteger('position')->default(0);
            $table->boolean('is_visible')->default(true); $table->timestamps();
        });
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id(); $table->foreignId('section_id')->constrained()->cascadeOnDelete();
            $table->string('title'); $table->string('subtitle')->nullable(); $table->text('body')->nullable();
            $table->json('data')->nullable(); $table->unsignedInteger('position')->default(0);
            $table->boolean('is_visible')->default(true); $table->timestamps();
        });
        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->string('email'); $table->text('message');
            $table->timestamp('read_at')->nullable(); $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('contact_messages'); Schema::dropIfExists('portfolio_items'); Schema::dropIfExists('sections');
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('is_admin'));
    }
};
