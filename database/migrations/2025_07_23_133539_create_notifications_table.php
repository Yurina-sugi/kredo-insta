<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('recipient_id'); // User who receives the notification
            $table->unsignedBigInteger('sender_id')->nullable(); // User who sends the notification
            $table->string('type'); // Notification type (like, comment, follow, mention, etc.)
            $table->unsignedBigInteger('notifiable_id')->nullable(); // Related model ID
            $table->string('notifiable_type')->nullable(); // Related model type
            $table->text('data')->nullable(); // Additional data (JSON)
            $table->timestamp('read_at')->nullable(); // Read timestamp
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes

            $table->foreign('recipient_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['recipient_id', 'read_at']);
            $table->index(['type', 'notifiable_id', 'notifiable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
