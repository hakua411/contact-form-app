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

        return view('contact.confirm', compact('validated'));
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
        return view('contact.complete');
    }

    public function index()
    {
        $contacts = Contact::with(['category', 'tags'])->get();

        return view('admin.index', compact('contacts'));
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
