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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('module_name')->nullable();
            $table->longText('xml_content');
            $table->foreignId('parent_template_id')->nullable()->constrained('templates')->onDelete('set null');
            $table->integer('priority')->default(10);
            $table->boolean('active')->default(true);
            $table->timestamps();
            
            $table->unique(['name', 'module_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('templates', function (Blueprint $table) {
            $table->dropForeign('templates_parent_template_id_foreign');
        });
        
        Schema::dropIfExists('templates');
    }
};