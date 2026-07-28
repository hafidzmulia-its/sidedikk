<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questionnaire_versions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('version_number');
            $table->string('title');
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('max_score_snapshot')->default(0);
            $table->boolean('is_demo_data')->default(true);
            $table->boolean('medical_approval_required')->default(true);
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('questionnaire_version_id')->constrained()->cascadeOnDelete();
            $table->text('text');
            $table->text('help_text')->nullable();
            $table->unsignedTinyInteger('score_yes')->default(0);
            $table->unsignedTinyInteger('score_no')->default(0);
            $table->unsignedSmallInteger('display_order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['questionnaire_version_id', 'display_order']);
            $table->index(['questionnaire_version_id', 'is_active']);
        });

        Schema::create('risk_rule_versions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('version_number');
            $table->string('title');
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('max_score_covered')->default(0);
            $table->boolean('is_demo_data')->default(true);
            $table->boolean('medical_approval_required')->default(true);
            $table->timestamps();
        });

        Schema::create('risk_levels', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('risk_rule_version_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->unsignedSmallInteger('min_score');
            $table->unsignedSmallInteger('max_score');
            $table->string('semantic_color', 20);
            $table->text('description');
            $table->text('recommendation');
            $table->unsignedSmallInteger('display_priority')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['risk_rule_version_id', 'display_priority']);
            $table->index(['risk_rule_version_id', 'min_score', 'max_score']);
        });

        Schema::create('screenings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questionnaire_version_id')->constrained()->restrictOnDelete();
            $table->foreignId('risk_rule_version_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('in_progress')->index();
            $table->string('submission_key')->unique();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable()->index();
            $table->unsignedTinyInteger('gestational_age_weeks_snapshot')->default(0);
            $table->unsignedTinyInteger('gestational_age_days_snapshot')->default(0);
            $table->unsignedSmallInteger('total_score')->default(0);
            $table->unsignedSmallInteger('max_score')->default(0);
            $table->string('risk_label_snapshot')->nullable();
            $table->text('risk_description_snapshot')->nullable();
            $table->text('recommendation_snapshot')->nullable();
            $table->string('questionnaire_version_name_snapshot')->nullable();
            $table->string('risk_rule_version_name_snapshot')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status', 'started_at']);
            $table->index(['user_id', 'completed_at']);
        });

        Schema::create('screening_answers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('screening_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('questionnaire_version_id')->constrained()->restrictOnDelete();
            $table->text('question_text_snapshot');
            $table->string('selected_answer', 10);
            $table->unsignedTinyInteger('awarded_score');
            $table->unsignedSmallInteger('display_order_snapshot');
            $table->timestamps();

            $table->index(['screening_id', 'display_order_snapshot']);
        });

        Schema::create('education_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500);
            $table->longText('body');
            $table->string('cover_image_path')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->boolean('is_demo_data')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('safe_metadata')->nullable();
            $table->string('ip_hash', 128)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['actor_id', 'created_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index(['action', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('education_posts');
        Schema::dropIfExists('screening_answers');
        Schema::dropIfExists('screenings');
        Schema::dropIfExists('risk_levels');
        Schema::dropIfExists('risk_rule_versions');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('questionnaire_versions');
    }
};
