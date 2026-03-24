<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('house')->nullable();
            $table->string('caste')->nullable();
            $table->enum('gender', ['Male', 'Female', 'Other'])->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('school_status')->nullable();
            $table->date('date_of_admission')->nullable();
            $table->string('blood_group')->nullable();
            $table->decimal('height', 5, 2)->nullable(); // Example: 150.75 cm
            $table->decimal('weight', 5, 2)->nullable(); // Example: 45.50 kg
            $table->text('family')->nullable();
            $table->string('vision_left')->nullable();
            $table->string('vision_right')->nullable();
            $table->string('dental_hygiene')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'house', 'caste', 'gender', 'address', 'address2', 'city', 'state',
                'school_status', 'date_of_admission', 'blood_group', 'height',
                'weight', 'family', 'vision_left', 'vision_right', 'dental_hygiene'
            ]);
        });
    }
};
