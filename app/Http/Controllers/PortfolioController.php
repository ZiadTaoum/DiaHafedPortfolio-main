<?php
namespace App\Http\Controllers;
use App\Models\{Section, PortfolioItem, ContactMessage};
use App\Support\PortfolioAnalytics;
use Illuminate\Http\Request;
class PortfolioController extends Controller {
    public function index(Request $request, PortfolioAnalytics $analytics) {
        $analytics->record($request);
        $sections = Section::where('is_visible',true)->where('type','!=','settings')->with(['items'=>fn($q)=>$q->where('is_visible',true)])->orderBy('position')->orderBy('id')->get();
        return view('portfolio.home', ['sections'=>$sections,'settings'=>Section::where('key','settings')->first()?->data ?? []]);
    }
    public function project(Request $request, PortfolioItem $project, PortfolioAnalytics $analytics) {
        abort_unless($project->is_visible && $project->section->is_visible && $project->section->type === 'projects',404);
        $analytics->record($request);
        return view('portfolio.project', ['project'=>$project,'settings'=>Section::where('key','settings')->first()?->data ?? []]);
    }
    public function contact(Request $request) {
        abort_unless(Section::where('type','contact')->where('is_visible',true)->exists(),404);
        $data=$request->validate(['name'=>'required|string|max:150','email'=>'required|email|max:255','message'=>'required|string|min:10|max:10000','website'=>'nullable|max:0']);
        ContactMessage::create(collect($data)->only(['name','email','message'])->all());
        return redirect(route('home').'#'.Section::where('type','contact')->first()->key)->with('contact_success',Section::where('type','contact')->first()?->data['success_message'] ?? 'Thank you. Your message has been received.');
    }
}
