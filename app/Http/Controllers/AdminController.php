<?php
namespace App\Http\Controllers;
use App\Models\{Section,PortfolioItem,ContactMessage};
use App\Http\Requests\ContentRequest;
use App\Support\PortfolioFields;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth,Hash};
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\UploadedFile;
class AdminController extends Controller {
    public function login() { return view('admin.login'); }
    public function authenticate(Request $request) {
        $credentials=$request->validate(['email'=>'required|email','password'=>'required|string']);
        if (!Auth::attempt([...$credentials,'is_admin'=>true])) return back()->withErrors(['email'=>'These credentials do not match an administrator account.'])->onlyInput('email');
        $request->session()->regenerate();
        return redirect()->intended(route('admin.index'));
    }
    public function logout(Request $request) { Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken(); return redirect('/'); }
    public function index() { return view('admin.index',['sections'=>Section::withCount('items')->orderBy('position')->get(),'messageCount'=>ContactMessage::whereNull('read_at')->count(),'projectCount'=>PortfolioItem::whereHas('section',fn($q)=>$q->where('type','projects'))->count()]); }
    public function edit(Section $section) { return view('admin.edit',['section'=>$section,'record'=>$section,'fields'=>PortfolioFields::section($section->type),'isItem'=>false]); }
    public function update(ContentRequest $request, Section $section) { $section->update($this->content($request)); return back()->with('status','Section saved. Your website is up to date.'); }
    public function createItem(Section $section) { abort_if($section->type==='settings',404); return view('admin.edit',['section'=>$section,'record'=>new PortfolioItem(['position'=>$section->items()->max('position')+1,'is_visible'=>true]),'fields'=>PortfolioFields::item($section->type),'isItem'=>true]); }
    public function storeItem(ContentRequest $request, Section $section) { abort_if($section->type==='settings',404); $item=$section->items()->create($this->content($request)); return redirect()->route('admin.items.edit',$item)->with('status','Item added.'); }
    public function editItem(PortfolioItem $item) { return view('admin.edit',['section'=>$item->section,'record'=>$item,'fields'=>PortfolioFields::item($item->section->type),'isItem'=>true]); }
    public function updateItem(ContentRequest $request, PortfolioItem $item) {
        $values = $this->content($request);
        if ($item->section->type === 'projects') {
            if (!$request->validated('edit_galleries') && !array_key_exists('gallery', $values['data'])) {
                $values['data'] += array_intersect_key($item->data ?? [], array_flip(['gallery', 'galleries']));
            }
            $values['data'] += array_intersect_key($item->data ?? [], ['visibility' => true]);
        }
        $item->update($values);
        return back()->with('status','Item saved.');
    }
    public function deleteItem(PortfolioItem $item) { $section=$item->section; $item->delete(); return redirect()->route('admin.sections.edit',$section)->with('status','Item deleted.'); }
    private function content(ContentRequest $request): array {
        $values=collect($request->validated())->only(['title','subtitle','body','position','data'])->all();
        $values['is_visible']=$request->boolean('is_visible');
        $values['data']=$values['data']??[];
        foreach (['thumbnail','image','cv','portfolio','model_url','plan_image','cut_before','cut_after'] as $field) {
            if ($request->hasFile('upload_'.$field)) $values['data'][$field]='/media/'.$request->file('upload_'.$field)->store('uploads','local');
        }
        if ($request->hasFile('gallery_uploads')) {
            $paths=array_filter(preg_split('/\R/',$values['data']['gallery']??''));
            foreach ($request->file('gallery_uploads') as $file) $paths[]='/media/'.$file->store('uploads','local');
            $values['data']['gallery']=implode("\n",$paths);
        }
        if ($request->validated('edit_galleries')) {
            unset($values['data']['gallery']);
            $values['data']['galleries'] = [];
            foreach ($request->validated('galleries', []) as $gallery) {
                $images = [];
                $entries = $gallery['images'] ?? [];
                ksort($entries, SORT_NUMERIC);
                foreach ($entries as $image) {
                    $file = $image['upload'] ?? null;
                    $path = $file instanceof UploadedFile
                        ? '/media/'.$file->store('uploads', 'local')
                        : ($image['path'] ?? null);
                    if ($path) $images[] = ['path' => $path];
                }
                $values['data']['galleries'][] = [
                    'title' => $gallery['title'] ?? '',
                    'visible' => (bool) $gallery['visible'],
                    'images' => $images,
                ];
            }
        }
        return $values;
    }
    public function messages() { return view('admin.messages',['messages'=>ContactMessage::latest()->paginate(20)]); }
    public function readMessage(ContactMessage $message) { $message->update(['read_at'=>now()]); return back()->with('status','Message marked as read.'); }
    public function deleteMessage(ContactMessage $message) { $message->delete(); return back()->with('status','Message deleted.'); }
    public function account() { return view('admin.account'); }
    public function updateAccount(Request $request) {
        $data=$request->validate(['name'=>'required|string|max:150','email'=>'required|email|max:255|unique:users,email,'.$request->user()->id,'current_password'=>'required|current_password','password'=>['nullable','confirmed',Password::min(12)]]);
        $request->user()->update(['name'=>$data['name'],'email'=>$data['email'],...(!empty($data['password'])?['password'=>Hash::make($data['password']),'password_changed_at'=>now()]:[])]);
        $request->session()->regenerate(); return back()->with('status','Account updated.');
    }
}
