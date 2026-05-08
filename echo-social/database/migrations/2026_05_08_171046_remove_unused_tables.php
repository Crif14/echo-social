<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::dropIfExists('password_reset_tokens');
    Schema::dropIfExists('failed_jobs');
    Schema::dropIfExists('job_batches');
    Schema::dropIfExists('jobs');
}

    public function down(): void
    {
        // non le rimettiamo
    }
};