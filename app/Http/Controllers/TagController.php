<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(TagRequest $request)
    {
        $validated = $request->validated();

        Tag::create($validated);

        return redirect()->route('admin.index');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(Tag $tag)
    {
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(TagRequest $request, Tag $tag)
    {
        $validated = $request->validated();

        $tag->update($validated);

        return redirect()->route('admin.index');
    }

    public function destroy(Tag $tag)
    {
        $tag->delete();

        return redirect()->route('admin.index');
    }
}
