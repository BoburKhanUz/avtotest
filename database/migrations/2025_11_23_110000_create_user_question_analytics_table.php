<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foydalanuvchi har bir savol bo'yicha statistika
        Schema::create('user_question_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->integer('correct_count')->default(0); // To'g'ri javoblar soni
            $table->integer('incorrect_count')->default(0); // Noto'g'ri javoblar soni
            $table->integer('total_attempts')->default(0); // Jami urinishlar
            $table->decimal('success_rate', 5, 2)->default(0); // Muvaffaqiyat foizi
            $table->timestamp('last_attempt_at')->nullable();
            $table->boolean('mastered')->default(false); // Savolni o'zlashtirganligi
            $table->timestamps();

            $table->unique(['user_id', 'question_id']);
            $table->index(['user_id', 'success_rate']);
            $table->index(['user_id', 'mastered']);
        });

        // Foydalanuvchi kategoriya bo'yicha statistika
        Schema::create('user_category_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->integer('total_questions')->default(0);
            $table->integer('mastered_questions')->default(0);
            $table->decimal('category_progress', 5, 2)->default(0); // Kategoriya bo'yicha progress %
            $table->decimal('average_success_rate', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'category_id']);
        });

        // Tavsiya etilgan savollar (AI-powered)
        Schema::create('recommended_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('recommendation_type'); // 'weak_area', 'similar_question', 'review'
            $table->integer('priority')->default(0); // Yuqori = muhimroq
            $table->text('reason')->nullable(); // Nima uchun tavsiya qilingan
            $table->boolean('completed')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'completed', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommended_questions');
        Schema::dropIfExists('user_category_analytics');
        Schema::dropIfExists('user_question_analytics');
    }
};
