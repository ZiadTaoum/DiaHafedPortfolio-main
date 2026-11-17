<?php
namespace Tests\Feature;
use App\Models\{User,PortfolioItem};
use App\Support\PlanRooms;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class ProjectToolsTest extends TestCase {
    use RefreshDatabase;
    protected function setUp(): void { parent::setUp(); $this->seed(PortfolioSeeder::class); }
    private function admin(): User { $user=User::factory()->create(); $user->is_admin=true; $user->password_changed_at=now(); $user->save(); return $user; }
    private function values($record,array $overrides=[]): array { return array_replace(['title'=>$record->title,'subtitle'=>$record->subtitle,'body'=>$record->body,'position'=>$record->position,'is_visible'=>1,'data'=>$record->data],$overrides); }
    private function courtyard(): PortfolioItem { return PortfolioItem::where('title','Courtyard House')->firstOrFail(); }

    public function test_sample_project_shows_floor_plan_and_section_cut(): void {
        $this->get('/projects/'.$this->courtyard()->id)->assertOk()
            ->assertSee('data-plan',false)->assertSee('Walk through the plan')->assertSee('Living room')->assertSee('Courtyard')
            ->assertSee('data-cut',false)->assertSee('/images/architecture/section-courtyard.svg')->assertSee('/js/project-tools.js',false);
    }
    public function test_projects_without_the_tools_do_not_render_them(): void {
        $other=PortfolioItem::where('title','Street Commons')->firstOrFail();
        $this->get('/projects/'.$other->id)->assertOk()->assertDontSee('data-plan',false)->assertDontSee('data-cut',false)->assertDontSee('project-tools.js',false);
    }
    public function test_room_text_is_escaped_on_the_page(): void {
        $this->actingAs($this->admin());$item=$this->courtyard();
        $this->put('/admin/items/'.$item->id,$this->values($item,['data'=>[...$item->data,'plan_rooms'=>'5, 5, 20, 20 | <script>alert(1)</script> | 4 m | note']]))->assertSessionHasNoErrors();
        $this->get('/projects/'.$item->id)->assertOk()->assertDontSee('<script>alert(1)</script>',false);
    }
    public function test_admin_can_save_valid_plan_rooms_and_bad_lines_are_rejected(): void {
        $this->actingAs($this->admin());$item=$this->courtyard();
        $this->put('/admin/items/'.$item->id,$this->values($item,['data'=>[...$item->data,'plan_rooms'=>"5, 5, 20, 20 | Studio | 4 m | Light."]]))->assertSessionHasNoErrors();
        $this->assertStringContainsString('Studio',$item->fresh()->data['plan_rooms']);
        foreach (['nonsense line','90, 5, 40, 20 | Overflows the plan','5, 5, 20 | Only three numbers','5, 5, 20, 20 | Name | 4 m | note | //evil.example/x.jpg'] as $bad) {
            $this->put('/admin/items/'.$item->id,$this->values($item,['data'=>[...$item->data,'plan_rooms'=>$bad]]))->assertSessionHasErrors('data.plan_rooms');
        }
    }
    public function test_admin_can_upload_plan_and_section_cut_images_but_not_other_files(): void {
        Storage::fake('local');$this->actingAs($this->admin());$item=$this->courtyard();
        $this->put('/admin/items/'.$item->id,$this->values($item,['upload_plan_image'=>UploadedFile::fake()->image('plan.png'),'upload_cut_before'=>UploadedFile::fake()->image('section.jpg'),'upload_cut_after'=>UploadedFile::fake()->image('render.webp')]))->assertSessionHasNoErrors();
        $data=$item->fresh()->data;
        foreach (['plan_image','cut_before','cut_after'] as $key) $this->assertStringStartsWith('/media/uploads/',$data[$key]);
        $this->put('/admin/items/'.$item->id,$this->values($item,['upload_plan_image'=>UploadedFile::fake()->create('plan.pdf',10,'application/pdf')]))->assertSessionHasErrors('upload_plan_image');
    }
    public function test_plan_room_parser_rules(): void {
        $this->assertCount(8,PlanRooms::parse($this->courtyard()->data['plan_rooms']));
        $this->assertSame([],PlanRooms::errors($this->courtyard()->data['plan_rooms']));
        $this->assertSame([],PlanRooms::errors("\n  \n"));
        $this->assertCount(1,PlanRooms::errors(str_repeat("1, 1, 5, 5 | R\n",41)));
    }
}
