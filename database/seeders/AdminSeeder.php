<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
class AdminSeeder extends Seeder {
    public function run(): void {
        $isDevelopment = app()->environment(['local', 'testing']);
        $email = env('ADMIN_EMAIL') ?: ($isDevelopment ? 'admin@example.test' : null);
        $password = env('ADMIN_PASSWORD') ?: ($isDevelopment ? 'password' : null);
        if (!$email || !$password) {
            throw new \RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD before seeding an administrator outside local/testing.');
        }

        $admin = User::firstOrNew(['email' => $email]);
        if (!$admin->exists) {
            $admin->name = 'Developer';
            $admin->password = $password;
            $admin->is_admin = true;
            $admin->password_changed_at = now();
            $admin->save();
        }
    }
}
