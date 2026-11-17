<?php
namespace Tests\Feature;
use App\Models\{User,Section,PortfolioItem,ContactMessage,AnalyticsVisit};
use App\Mail\ContactFormSubmission;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\{Storage,Hash,Mail};
use Tests\TestCase;
class PortfolioTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(PortfolioSeeder::class); }
    private function admin(): User { $user=User::factory()->create(); $user->is_admin=true; $user->password_changed_at=now(); $user->save(); return $user; }
    private function values($record,array $overrides=[]): array { return array_replace(['title'=>$record->title,'subtitle'=>$record->subtitle,'body'=>$record->body,'position'=>$record->position,'is_visible'=>1,'data'=>$record->data],$overrides); }
    public function test_seeded_portfolio_and_all_projects_render(): void {
        $this->get('/')->assertOk()->assertSee('Dia Youssef Hafed')->assertSee('Architecture portfolio')->assertSee('Courtyard House')->assertDontSee('Najat Taoum');
        foreach(PortfolioItem::whereHas('section',fn($q)=>$q->where('type','projects'))->get() as $item) $this->get('/projects/'.$item->id)->assertOk()->assertSee($item->title);
        $project=PortfolioItem::where('title','Courtyard House')->firstOrFail();
        $this->get('/projects/'.$project->id)->assertOk()->assertSee('/images/architecture/project-residential.svg')->assertSee('SAMPLE CONCEPT')->assertSee('data-project-lightbox',false)->assertSee('data-gallery-open',false);
        $this->get('/')->assertSee('Residential')->assertSee('Civic & Culture')->assertSee('Urban Design');
    }
    public function test_cv_details_and_download_are_available(): void {
        $this->get('/')->assertSee('Bachelor of Architecture')->assertSee('Renovation Project Intern')->assertSee('AutoCAD')->assertSee('diahafed423@gmail.com');
        $this->get('/cv')->assertOk()->assertHeader('content-type','application/pdf');
    }
    public function test_seed_is_repeatable_and_preserves_edits_and_deletions(): void {
        $hero=Section::where('key','hero')->first(); $hero->update(['title'=>'Edited title']);
        $item=Section::where('key','projects')->first()->items()->first();$item->delete();
        $count=PortfolioItem::count();$this->seed(PortfolioSeeder::class);
        $this->assertSame(9,Section::count());$this->assertSame($count,PortfolioItem::count());$this->assertSame('Edited title',$hero->fresh()->title);
    }
    public function test_seed_contains_labeled_sample_studies_not_unverified_cv_entries(): void {
        $this->assertSame(['Courtyard House','Community Arts House','Lightwell Apartment','Street Commons','Material / Memory','Garden Room Pavilion'],PortfolioItem::whereHas('section',fn($q)=>$q->where('type','projects'))->orderBy('id')->pluck('title')->all());
        $this->assertSame(['Residential','Civic & Culture','Interior','Urban Design','Research','Landscape'],PortfolioItem::whereHas('section',fn($q)=>$q->where('type','projects'))->orderBy('id')->get()->pluck('data.category')->all());
        $this->assertDatabaseMissing('portfolio_items',['title'=>'Residential study']);
        $this->assertDatabaseMissing('portfolio_items',['title'=>'Dunes Magazine']);
        $this->assertDatabaseMissing('portfolio_items',['title'=>'Kava Shop']);
    }
    public function test_guests_and_non_admins_cannot_edit_content(): void {
        $section=Section::where('key','hero')->first();
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->put('/admin/sections/'.$section->id,$this->values($section))->assertRedirect('/admin/login');
        $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
    }
    public function test_analytics_dashboard_is_admin_only_and_has_date_ranges(): void {
        $this->get('/admin/analytics')->assertRedirect('/admin/login');
        $this->actingAs($this->admin());
        $this->get('/admin/analytics')->assertOk()->assertSee('Analytics.')->assertSee('7 days')->assertSee('30 days')->assertSee('90 days')->assertSee('No visits yet');
        $this->get('/admin/analytics?range=7')->assertOk()->assertViewHas('days',7);
        $this->get('/admin/analytics?range=90')->assertOk()->assertViewHas('days',90);
        $this->get('/admin/analytics?range=999')->assertOk()->assertViewHas('days',30);
    }
    public function test_analytics_tracks_privacy_friendly_visits_and_excludes_admins_and_bots(): void {
        $this->withHeaders([
            'User-Agent'=>'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/131.0.0.0 Safari/537.36',
            'Referer'=>'https://www.google.com/search?q=private+query',
        ])->get('/')->assertOk();
        $visit=AnalyticsVisit::firstOrFail();
        $this->assertSame('/',$visit->path);
        $this->assertSame('google.com',$visit->source);
        $this->assertSame('Desktop',$visit->device);
        $this->assertSame('Chrome',$visit->browser);
        $this->assertSame(64,strlen($visit->session_hash));
        $this->assertDatabaseCount('analytics_visits',1);

        $this->withHeaders(['User-Agent'=>'Googlebot/2.1'])->get('/')->assertOk();
        $this->assertDatabaseCount('analytics_visits',1);
        $this->actingAs($this->admin())->get('/')->assertOk();
        $this->assertDatabaseCount('analytics_visits',1);
    }
    public function test_analytics_dashboard_aggregates_visits_sources_devices_and_inquiries(): void {
        $project=PortfolioItem::whereHas('section',fn($q)=>$q->where('type','projects'))->firstOrFail();
        $record=static function (string $session, string $path, string $source, string $device, string $browser, int $minutesAgo=0): void {
            AnalyticsVisit::create([
                'session_hash'=>hash('sha256',$session),
                'path'=>$path,
                'source'=>$source,
                'device'=>$device,
                'browser'=>$browser,
                'visited_at'=>now()->subMinutes($minutesAgo),
            ]);
        };
        $record('visitor-one','/','Direct / internal','Desktop','Chrome',2);
        $record('visitor-one','/projects/'.$project->id,'google.com','Desktop','Chrome',1);
        $record('visitor-two','/','Direct / internal','Mobile','Safari',3);
        $record('old-visitor','/','example.org','Desktop','Firefox',0);
        AnalyticsVisit::where('session_hash',hash('sha256','old-visitor'))->update(['visited_at'=>now()->subDays(10)]);
        ContactMessage::create(['name'=>'Visitor','email'=>'visitor@example.com','message'=>'I would like to discuss a project.']);

        $this->actingAs($this->admin());
        $this->get('/admin/analytics?range=7')->assertOk()
            ->assertSee('Unique visitors')->assertSee('Page views')
            ->assertSee('Courtyard House')->assertSee('google.com')
            ->assertSee('Mobile')->assertSee('Safari')
            ->assertSee('Visitors now');
        $this->assertDatabaseCount('analytics_visits',4);
    }
    public function test_admin_login_logout_and_invalid_credentials(): void {
        $user=$this->admin();$user->update(['password'=>Hash::make('test-password-123')]);
        $this->post('/admin/login',['email'=>$user->email,'password'=>'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/login',['email'=>$user->email,'password'=>'test-password-123'])->assertRedirect('/admin');$this->assertAuthenticatedAs($user);
        $this->post('/admin/logout')->assertRedirect('/');$this->assertGuest();
    }
    public function test_non_admin_cannot_log_in_to_dashboard(): void {
        $user=User::factory()->create(['password'=>Hash::make('test-password-123')]);
        $this->post('/admin/login',['email'=>$user->email,'password'=>'test-password-123'])->assertSessionHasErrors('email');$this->assertGuest();
    }
    public function test_all_admin_forms_render(): void {
        $this->actingAs($this->admin());$this->get('/admin')->assertOk();$this->get('/admin/account')->assertOk();$this->get('/admin/messages')->assertOk();
        foreach(Section::all() as $section){$this->get('/admin/sections/'.$section->id.'/edit')->assertOk();}
        foreach(PortfolioItem::all() as $item){$this->get('/admin/items/'.$item->id.'/edit')->assertOk();}
    }
    public function test_section_edits_visibility_and_order_affect_website(): void {
        $hero=Section::where('key','hero')->first();$this->actingAs($this->admin());
        $this->put('/admin/sections/'.$hero->id,$this->values($hero,['title'=>'A new story','position'=>1000]))->assertSessionHasNoErrors();
        $this->get('/')->assertSeeInOrder(['Architecture portfolio','A new story']);
        $this->put('/admin/sections/'.$hero->id,$this->values($hero,['is_visible'=>0]))->assertSessionHasNoErrors();
        $this->get('/')->assertDontSee('A new story');
    }
    public function test_project_crud_and_hidden_project_access(): void {
        $section=Section::where('key','projects')->first();$this->actingAs($this->admin());
        $this->post('/admin/sections/'.$section->id.'/items',['title'=>'New project','position'=>0,'is_visible'=>1,'data'=>['category'=>'Branding']])->assertSessionHasNoErrors();
        $item=PortfolioItem::where('title','New project')->firstOrFail();$this->get('/projects/'.$item->id)->assertOk();
        $this->put('/admin/items/'.$item->id,$this->values($item,['is_visible'=>0]))->assertSessionHasNoErrors();$this->get('/projects/'.$item->id)->assertNotFound();
        $this->delete('/admin/items/'.$item->id)->assertRedirect();$this->assertDatabaseMissing('portfolio_items',['id'=>$item->id]);
    }
    public function test_magazine_page_dimensions_only_appear_for_magazine_categories(): void {
        $this->actingAs($this->admin());$item=Section::where('key','projects')->first()->items()->first();
        $this->get('/admin/items/'.$item->id.'/edit')->assertOk()->assertSee('data-magazine-fields  hidden',false);
        $data=[...$item->data,'category'=>'Editorial'];
        $this->put('/admin/items/'.$item->id,$this->values($item,['data'=>$data]))->assertSessionHasNoErrors();
        $this->get('/admin/items/'.$item->id.'/edit')->assertOk()->assertDontSee('data-magazine-fields  hidden',false)->assertSee('Magazine page width (mm)')->assertSee('Magazine page height (mm)');
        $data=[...$item->fresh()->data,'category'=>'Residential','page_width'=>'300','page_height'=>''];
        $this->put('/admin/items/'.$item->id,$this->values($item->fresh(),['data'=>$data]))->assertSessionHasNoErrors();
    }
    public function test_hidden_project_section_hides_detail_pages(): void {
        $section=Section::where('key','projects')->first();$item=$section->items()->first();$section->update(['is_visible'=>false]);$this->get('/projects/'.$item->id)->assertNotFound();
    }
    public function test_uploads_are_stored_and_served_and_unsafe_uploads_rejected(): void {
        Storage::fake('local');$this->actingAs($this->admin());$section=Section::where('key','hero')->first();
        $this->put('/admin/sections/'.$section->id,$this->values($section,['upload_image'=>UploadedFile::fake()->image('cover.jpg')]))->assertSessionHasNoErrors();
        $path=$section->fresh()->data['image'];$this->assertStringStartsWith('/media/uploads/',$path);$this->get($path)->assertOk()->assertHeader('X-Content-Type-Options','nosniff');
        $this->put('/admin/sections/'.$section->id,$this->values($section,['upload_image'=>UploadedFile::fake()->create('script.php',1,'text/x-php')]))->assertSessionHasErrors('upload_image');
        $this->get('/media/uploads/not-found.jpg')->assertNotFound();
    }
    public function test_project_thumbnail_can_be_uploaded_and_used_on_homepage(): void {
        Storage::fake('local');$this->actingAs($this->admin());$item=Section::where('key','projects')->first()->items()->first();
        $this->put('/admin/items/'.$item->id,$this->values($item,['upload_thumbnail'=>UploadedFile::fake()->image('thumbnail.jpg')]))->assertSessionHasNoErrors();
        $thumbnail=$item->fresh()->data['thumbnail'];
        $this->assertStringStartsWith('/media/uploads/',$thumbnail);
        $this->get('/')->assertSee('src="'.$thumbnail.'"',false);
    }
    public function test_gallery_upload_and_unsafe_links(): void {
        Storage::fake('local');$this->actingAs($this->admin());$item=Section::where('key','projects')->first()->items()->first();
        $this->get('/admin/items/'.$item->id.'/edit')->assertOk()->assertSee('gallery_image_list')->assertSee('gallery_uploads')->assertSee('Advanced: edit image paths');
        $data=[...$item->data,'gallery'=>"/images/gallery/second.jpg\n/images/gallery/first.jpg"];
        $this->put('/admin/items/'.$item->id,$this->values($item,['data'=>$data,'gallery_uploads'=>[UploadedFile::fake()->image('art-one.jpg'),UploadedFile::fake()->image('art-two.jpg')]]))->assertSessionHasNoErrors();
        $paths=preg_split('/\R/',$item->fresh()->data['gallery']);
        $this->assertSame(['/images/gallery/second.jpg','/images/gallery/first.jpg'],array_slice($paths,0,2));
        $this->assertCount(4,$paths);
        $this->assertStringStartsWith('/media/uploads/',$paths[2]);
        $this->assertStringStartsWith('/media/uploads/',$paths[3]);
        $data=[...$item->fresh()->data,'gallery'=>$paths[3]."\n".$paths[0]];
        $this->put('/admin/items/'.$item->id,$this->values($item->fresh(),['data'=>$data]))->assertSessionHasNoErrors();
        $this->assertSame([$paths[3],$paths[0]],preg_split('/\R/',$item->fresh()->data['gallery']));
        $hero=Section::where('key','hero')->first();$data=$hero->data;$data['cta_url']='javascript:alert(1)';
        $this->put('/admin/sections/'.$hero->id,$this->values($hero,['data'=>$data]))->assertSessionHasErrors('data.cta_url');
    }
    public function test_contact_validates_saves_and_admin_manages_message(): void {
        Mail::fake();config(['mail.contact_to'=>'owner@example.com']);
        $this->post('/contact',['name'=>'Visitor','email'=>'bad','message'=>'short'])->assertSessionHasErrors(['email','message']);
        $this->post('/contact',['name'=>'Visitor','email'=>'visitor@example.com','message'=>'I would like a brand identity.'])->assertSessionHas('contact_success');
        $message=ContactMessage::firstOrFail();
        Mail::assertSent(ContactFormSubmission::class,fn($mail)=>$mail->hasTo('owner@example.com')&&$mail->hasReplyTo('visitor@example.com')&&$mail->contactMessage->message==='I would like a brand identity.');
        $this->actingAs($this->admin());$this->get('/admin/messages')->assertOk()->assertSee('Visitor');
        $this->patch('/admin/messages/'.$message->id)->assertRedirect();$this->assertNotNull($message->fresh()->read_at);
        $this->delete('/admin/messages/'.$message->id)->assertRedirect();$this->assertDatabaseCount('contact_messages',0);
    }
    public function test_contact_requires_a_valid_recipient_address(): void {
        config(['mail.contact_to'=>null]);
        $section=Section::where('type','contact')->firstOrFail();
        $section->update(['data'=>[...$section->data,'email'=>'']]);
        $this->post('/contact',['name'=>'Visitor','email'=>'visitor@example.com','message'=>'I would like a brand identity.'])->assertServiceUnavailable();
        $this->assertDatabaseCount('contact_messages',0);
    }
    public function test_account_password_change_requires_current_password(): void {
        $user=$this->admin();$this->actingAs($user);
        $this->put('/admin/account',['name'=>'Dia Hafed','email'=>$user->email,'current_password'=>'wrong','password'=>'new-password-123','password_confirmation'=>'new-password-123'])->assertSessionHasErrors('current_password');
        $this->put('/admin/account',['name'=>'Dia Hafed','email'=>$user->email,'current_password'=>'password','password'=>'new-password-123','password_confirmation'=>'new-password-123'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-password-123',$user->fresh()->password));
    }
    public function test_seeded_developer_can_sign_in_without_forced_password_change(): void {
        $this->seed(\Database\Seeders\AdminSeeder::class);
        $user=User::where('email','admin@example.test')->firstOrFail();
        $user->update(['password_changed_at'=>null]); // legacy admin accounts should remain usable
        $this->post('/admin/login',['email'=>'admin@example.test','password'=>'password'])->assertRedirect('/admin');
        $this->assertAuthenticatedAs($user);
        $this->get('/admin')->assertOk()->assertSee('Make it yours.');
        $this->get('/admin/messages')->assertOk();
    }
    public function test_admin_seeder_creates_a_developer_account_with_working_credentials(): void {
        $this->seed(\Database\Seeders\AdminSeeder::class);
        $user=User::where('is_admin',true)->firstOrFail();
        $this->assertSame('admin@example.test',$user->email);
        $this->assertTrue(Hash::check('password',$user->password));
        $this->assertNotNull($user->password_changed_at);
    }
    public function test_admin_colors_persist_and_apply_to_home_and_project_pages(): void {
        $settings=Section::where('key','settings')->first();
        $this->actingAs($this->admin());
        $this->get('/admin/sections/'.$settings->id.'/edit')->assertOk()->assertSee('Website colors');
        $data=[...$settings->data,'color_accent'=>'#2266aa','color_background'=>'#fafafa'];
        $this->put('/admin/sections/'.$settings->id,$this->values($settings,['data'=>$data]))->assertSessionHasNoErrors();
        $this->assertSame('#2266aa',$settings->fresh()->data['color_accent']);
        $this->get('/')->assertOk()->assertSee('--orange:#2266aa;',false)->assertSee('--paper:#fafafa;',false);
        $project=Section::where('key','projects')->first()->items()->first();
        $this->get('/projects/'.$project->id)->assertOk()->assertSee('--orange:#2266aa;',false);
        $this->seed(PortfolioSeeder::class);
        $this->assertSame('#2266aa',$settings->fresh()->data['color_accent']);
    }
    public function test_invalid_colors_are_rejected_and_legacy_settings_use_defaults(): void {
        $settings=Section::where('key','settings')->first();
        $this->actingAs($this->admin());
        $this->put('/admin/sections/'.$settings->id,$this->values($settings,['data'=>[...$settings->data,'color_accent'=>'red;}</style><script>alert(1)</script>']]))->assertSessionHasErrors('data.color_accent');
        $this->assertArrayNotHasKey('color_accent',$settings->fresh()->data);
        $this->get('/')->assertSee('--orange:#b47a52;',false);
        $settings->update(['data'=>[...$settings->data,'color_accent'=>'invalid']]);
        $this->get('/')->assertSee('--orange:#b47a52;',false)->assertDontSee('--orange:invalid',false);
    }
}
