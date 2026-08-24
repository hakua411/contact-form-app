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

    public function store(Request $request)
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
