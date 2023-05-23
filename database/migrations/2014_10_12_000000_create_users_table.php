<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('privileges');
            $table->boolean('active')->default(0);
            $table->boolean('enable_get_lead');
            $table->rememberToken();
            $table->timestamps();
        });

        User::create([
            'key' => '280469',
            'name' => 'Jovan Lopez',
            'email' => 'everest.studio32@gmail.com',
            'password' => Hash::make('EverestH*me32'),
            'privileges' => 'MANAGER',
            'enable_get_lead' => 1,
            'active' => false,
        ]);

        User::create([
            'key' => '277985',
            'name' => 'Axel Salas',
            'email' => 'axpuma@live.com.mx',
            'password' => Hash::make('Neuuni123'),
            'privileges' => 'AGENT',
            'enable_get_lead' => 1,
            'active' => false,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
