<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('portfolio:admin {email?} {--name=Developer} {--force-change}', function () {
    $email=$this->argument('email') ?: $this->ask('Administrator email');
    if (!filter_var($email,FILTER_VALIDATE_EMAIL)) { $this->error('Enter a valid email.'); return 1; }
    $user=\App\Models\User::firstOrNew(['email'=>$email]);
    $isNew=!$user->exists;
    if ($isNew || $this->option('force-change')) {
        $password=$this->secret('Password (at least 12 characters)');
        if (strlen($password??'')<12) { $this->error('Password must contain at least 12 characters.'); return 1; }
        $user->password=$password;
        $user->password_changed_at=now();
    }
    $user->name=$this->option('name'); $user->is_admin=true; $user->save();
    $this->info('Administrator ready. Sign in at /admin/login.');
})->purpose('Create or reset a portfolio administrator with a securely prompted password');
