<?php

namespace App\Http\Controllers;

use App\Models\EducationPost;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        $posts = EducationPost::query()
            ->published()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where('title', 'like', '%'.$search.'%');
            })
            ->latest('published_at')
            ->paginate(9)
            ->withQueryString();

        return view('education.index', [
            'posts' => $posts,
            'search' => $search,
        ]);
    }

    public function show(string $slug): View
    {
        $post = EducationPost::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('education.show', [
            'post' => $post,
            'relatedPosts' => EducationPost::query()
                ->published()
                ->whereKeyNot($post->getKey())
                ->latest('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
