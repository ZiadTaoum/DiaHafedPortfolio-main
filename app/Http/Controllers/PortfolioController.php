<?php
namespace App\Http\Controllers;
use App\Models\{Section, PortfolioItem, ContactMessage};
use App\Mail\ContactFormSubmission;
use App\Support\PortfolioAnalytics;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
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
        $section=Section::where('type','contact')->where('is_visible',true)->first();
        abort_unless($section,404);
        $data=$request->validate(['name'=>'required|string|max:150','email'=>'required|email|max:255','message'=>'required|string|min:10|max:10000','website'=>'nullable|max:0']);
        $recipient=trim((string) (config('mail.contact_to') ?: ($section->data['email'] ?? '')));
        abort_unless(filter_var($recipient,FILTER_VALIDATE_EMAIL),503,'Contact email delivery is not configured.');
        $message=ContactMessage::create(collect($data)->only(['name','email','message'])->all());
        Mail::to($recipient)->send(new ContactFormSubmission($message));
        return redirect(route('home').'#'.$section->key)->with('contact_success',$section->data['success_message'] ?? 'Thank you. Your message has been received.');
    }
}
