<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Tag;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        $tags = Tag::all();

        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(ContactRequest $request)
    {
        $validated = $request->validated();

        $category = Category::find($validated['category_id']);

        return view('contact.confirm', compact('validated', 'category'));
    }

    public function store(ContactRequest $request)
    {
        $contact = Contact::create(
            $request->only([

                'category_id',
                'first_name',
                'last_name',
                'gender',
                'email',
                'tel',
                'address',
                'building',
                'detail',
            ])
        );

        $contact->tags()->attach($request->tag_ids ?? []);

        return redirect()->route('contacts.complete');
    }

    public function complete()
    {
        return view('contact.thanks');
    }

    public function index(Request $request)
    {
        $query = Contact::with(['category', 'tags']);

        $query->keywordSearch($request->keyword);
        $query->genderSearch($request->gender);
        $query->categorySearch($request->category_id);
        $query->dateSearch($request->date);

        $contacts = $query->paginate(7)->withQueryString();

        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.index', compact('contacts', 'categories', 'tags'));
    }

    public function export(Request $request)
    {
        $query = Contact::with('category');

        $query->keywordSearch($request->keyword);
        $query->genderSearch($request->gender);
        $query->categorySearch($request->category_id);
        $query->dateSearch($request->date);

        $contacts = $query
            ->orderBy('created_at', 'desc')
            ->get();

        $csv = fopen('php://temp', 'r+');

        fwrite($csv, "\xEF\xBB\xBF");

        fputcsv($csv, [
            'ID',
            '氏名',
            '性別',
            'メール',
            '電話',
            '住所',
            '建物',
            'カテゴリ',
            '内容',
            '作成日時',
        ]);

        foreach ($contacts as $contact) {
            fputcsv($csv, [
                $contact->id,
                $contact->fist_name.' '.$contact->last_name,
                $contact->gender == 1 ? '男性' : ($contact->gender == 2 ? '女性' : 'その他'),
                $contact->email,
                $contact->tel,
                $contact->address,
                $contact->building,
                $contact->category->content,
                $contact->detail,
                $contact->created_at,
            ]);
        }

        rewind($csv);
        $csvData = stream_get_contents($csv);
        fclose($csv);

        return response($csvData)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="contacts.csv"');
    }

    public function show(Contact $contact)
    {
        $contact->load('category', 'tags');

        return view('admin.show', compact('contact'));
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(Contact $contact)
    {
        $contact->delete();

        return redirect()->route('admin.index');
    }
}
