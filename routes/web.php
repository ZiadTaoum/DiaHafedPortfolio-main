<?php
use Illuminate\Support\Facades\{Route,Storage};
use App\Http\Controllers\{PortfolioController,AdminController,AnalyticsController};
use App\Http\Middleware\EnsureAdmin;
Route::get('/',[PortfolioController::class,'index'])->name('home');
Route::get('/cv',function() {
    $path=base_path('Dia Hafed CV.pdf');
    abort_unless(is_file($path),404);
    return response()->download($path,'Dia-Youssef-Hafed-CV.pdf',['Content-Type'=>'application/pdf']);
})->name('cv');
Route::get('/projects/{project}',[PortfolioController::class,'project'])->name('projects.show');
Route::post('/contact',[PortfolioController::class,'contact'])->middleware('throttle:5,1')->name('contact');
Route::get('/media/uploads/{filename}',function(string $filename) {
    abort_unless(preg_match('/^[a-zA-Z0-9]+\.(jpg|jpeg|png|webp|pdf|glb)$/i',$filename),404);
    $path='uploads/'.$filename; abort_unless(Storage::disk('local')->exists($path),404);
    return response()->file(Storage::disk('local')->path($path),['X-Content-Type-Options'=>'nosniff']);
})->name('media');
Route::get('/admin/login',[AdminController::class,'login'])->name('login');
Route::post('/admin/login',[AdminController::class,'authenticate'])->middleware('throttle:5,1')->name('login.submit');
Route::prefix('admin')->name('admin.')->middleware(EnsureAdmin::class)->group(function() {
    Route::get('/',[AdminController::class,'index'])->name('index');
    Route::get('/analytics',AnalyticsController::class)->name('analytics');
    Route::post('/logout',[AdminController::class,'logout'])->name('logout');
    Route::get('/sections/{section}/edit',[AdminController::class,'edit'])->name('sections.edit');
    Route::put('/sections/{section}',[AdminController::class,'update'])->name('sections.update');
    Route::get('/sections/{section}/items/create',[AdminController::class,'createItem'])->name('items.create');
    Route::post('/sections/{section}/items',[AdminController::class,'storeItem'])->name('items.store');
    Route::get('/items/{item}/edit',[AdminController::class,'editItem'])->name('items.edit');
    Route::put('/items/{item}',[AdminController::class,'updateItem'])->name('items.update');
    Route::delete('/items/{item}',[AdminController::class,'deleteItem'])->name('items.destroy');
    Route::get('/messages',[AdminController::class,'messages'])->name('messages');
    Route::patch('/messages/{message}',[AdminController::class,'readMessage'])->name('messages.read');
    Route::delete('/messages/{message}',[AdminController::class,'deleteMessage'])->name('messages.destroy');
    Route::get('/account',[AdminController::class,'account'])->name('account');
    Route::put('/account',[AdminController::class,'updateAccount'])->name('account.update');
});
