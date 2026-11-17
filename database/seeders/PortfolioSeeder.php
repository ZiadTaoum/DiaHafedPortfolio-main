<?php
namespace Database\Seeders;
use App\Models\Section;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class PortfolioSeeder extends Seeder {
    public function run(): void {
        DB::transaction(function () {
            foreach (json_decode(file_get_contents(__DIR__.'/data/portfolio.json'),true,512,JSON_THROW_ON_ERROR) as $data) {
                $items=$data['items']; unset($data['items']);
                // Seed only missing sections; never overwrite dashboard edits or deleted items.
                $section=Section::firstOrCreate(['key'=>$data['key']],$data);
                if ($section->wasRecentlyCreated) foreach ($items as $position=>$item) $section->items()->create([...$item,'position'=>$position,'is_visible'=>true]);
                elseif ($data['key'] !== 'projects') {
                    $legacySections=[
                        'settings'=>['title'=>'Site settings','subtitle'=>'','body'=>'','data'=>[
                            'name'=>'Dia Hafed','role'=>'Architecture','description'=>'Dia Hafed — architecture portfolio. Project details and professional profile will be added soon.','footer'=>'Architecture portfolio — work and profile details coming soon.','cv_label'=>'Download CV','portfolio_label'=>'Full portfolio PDF','cv'=>'','portfolio'=>'',
                        ]],
                        'hero'=>['title'=>'Spaces shaped by light, place, and purpose.','subtitle'=>'Architecture / Spatial design','body'=>'I’m Dia Hafed. This portfolio is taking shape—architecture projects, drawings, and details will be added soon.','data'=>[
                            'eyebrow'=>'Dia Hafed / Architecture','image'=>'/images/architecture/hero-study.svg','image_alt'=>"Original architectural facade illustration placeholder, to be replaced with Dia's work",'cta_label'=>'Explore my work','cta_url'=>'#projects','secondary_label'=>'Let’s work together','secondary_url'=>'#contact',
                        ]],
                        'about'=>['title'=>'About Dia','subtitle'=>'Profile details coming soon','body'=>"This section will be updated with Dia's introduction and architecture profile once the CV and background details are provided.",'data'=>[
                            'image'=>'/images/architecture/about-study.svg','image_alt'=>"Illustrative architecture drawing placeholder; replace with Dia's portrait or project image",
                        ]],
                        'services'=>['title'=>'Areas of practice','subtitle'=>"To be confirmed from Dia's materials",'body'=>'Practice areas will be added after the CV and portfolio details are provided.','data'=>[]],
                        'experience'=>['title'=>'Experience','subtitle'=>"To be added from Dia's CV",'body'=>'','data'=>[]],
                        'education'=>['title'=>'Education','subtitle'=>'Education details coming soon','body'=>'','data'=>[]],
                        'skills'=>['title'=>'Skills & tools','subtitle'=>"To be added from Dia's CV",'body'=>'','data'=>[]],
                        'contact'=>['title'=>'Start a conversation','subtitle'=>'Contact details coming soon','body'=>"The contact details will be added when Dia's preferred email and location are confirmed.",'data'=>[
                            'email'=>'','phone'=>'','location'=>'','form_heading'=>'Tell me about your project','submit_label'=>'Send message','success_message'=>'Thank you! Your message has been received.',
                        ]],
                    ];
                    $legacy=$legacySections[$data['key']] ?? null;
                    $legacyDataUnchanged=$legacy && collect($legacy['data'])->every(fn ($value,$key) => ($section->data[$key] ?? null)===$value);
                    $isUntouchedSection=$legacy
                        && !$section->items()->exists()
                        && $section->title===$legacy['title']
                        && $section->subtitle===$legacy['subtitle']
                        && $section->body===$legacy['body']
                        && $legacyDataUnchanged;
                    if ($isUntouchedSection) {
                        $section->update([
                            ...collect($data)->only(['title','subtitle','body'])->all(),
                            'data'=>[...($section->data ?? []),...$data['data']],
                        ]);
                        foreach ($items as $position=>$item) $section->items()->create([...$item,'position'=>$position,'is_visible'=>true]);
                    }
                }
                elseif ($data['key']==='projects') {
                    if ($section->body === "Illustrative placeholders are shown for now. Project titles, descriptions, and images will be replaced when Dia's portfolio materials are provided.") {
                        $section->update(['body'=>$data['body']]);
                    }
                    $legacyTitles=['Courtyard House'=>'Residential study','Community Arts House'=>'Civic space study','Lightwell Apartment'=>'Interior study'];
                    $legacyPlaceholders=[
                        'Residential study'=>['body'=>"Placeholder card only. Replace this description, project data, and illustration with details from Dia's supplied portfolio.",'image'=>'/images/architecture/project-residential.svg'],
                        'Civic space study'=>['body'=>"Placeholder card only. Replace this description, project data, and illustration with details from Dia's supplied portfolio.",'image'=>'/images/architecture/project-cultural.svg'],
                        'Interior study'=>['body'=>"Placeholder card only. Replace this description, project data, and illustration with details from Dia's supplied portfolio.",'image'=>'/images/architecture/project-interior.svg'],
                    ];
                    foreach ($items as $position=>$item) {
                        $existing=$section->items()->where('title',$item['title'])->first();
                        $legacyTitle=$legacyTitles[$item['title']] ?? null;
                        if (!$existing && $legacyTitle) $existing=$section->items()->where('title',$legacyTitle)->first();
                        // A missing legacy record may have been intentionally deleted; do not recreate it.
                        if (!$existing && $legacyTitle) continue;
                        if ($existing) {
                            $legacy=$legacyPlaceholders[$legacyTitle] ?? null;
                            $isUntouchedPlaceholder=$legacyTitle && $existing->title===$legacyTitle && $existing->body===$legacy['body'] && $existing->subtitle==='Project details coming soon' && ($existing->data['category'] ?? null)==='Architecture' && ($existing->data['image'] ?? null)===$legacy['image'];
                            if ($isUntouchedPlaceholder) {
                                $existing->update([...$item,'position'=>$existing->position]);
                                continue;
                            }
                            $updates=[];
                            $gallery=$item['data']['gallery'] ?? null;
                            if (blank($existing->data['gallery'] ?? null) && filled($gallery)) $updates['gallery']=$gallery;
                            foreach (['page_width','page_height','plan_image','plan_rooms','cut_before','cut_after','cut_before_label','cut_after_label'] as $field) {
                                if (!array_key_exists($field,$existing->data ?? []) && array_key_exists($field,$item['data'] ?? [])) $updates[$field]=$item['data'][$field];
                            }
                            if ($updates) $existing->update(['data'=>[...$existing->data,...$updates]]);
                        } elseif ($section->items()->exists()) {
                            $section->items()->create([...$item,'position'=>$position,'is_visible'=>true]);
                        }
                    }
                }
            }
        });
    }
}
