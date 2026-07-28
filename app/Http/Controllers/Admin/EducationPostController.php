<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EducationPostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpsertEducationPostRequest;
use App\Models\EducationPost;
use App\Services\AuditLogService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EducationPostController extends Controller
{
    public function index(Request $request): View
    {
        $status = trim((string) $request->string('status'));
        $search = trim((string) $request->string('search'));

        $posts = EducationPost::query()
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.education.index', [
            'posts' => $posts,
            'filters' => [
                'status' => $status,
                'search' => $search,
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.education.form', [
            'post' => new EducationPost([
                'status' => EducationPostStatus::Draft,
                'is_demo_data' => false,
            ]),
            'formAction' => route('admin.education.store'),
            'formMethod' => 'POST',
            'pageTitle' => 'Buat Artikel Edukasi',
        ]);
    }

    public function store(UpsertEducationPostRequest $request, AuditLogService $auditLogService): RedirectResponse
    {
        $post = new EducationPost;
        $this->fillPost($post, $request);
        $post->save();

        $auditLogService->record(
            $request->user(),
            'admin.education.created',
            $post,
            ['title' => $post->title, 'status' => $post->status->value],
            $request->ip(),
        );

        return redirect()
            ->route('admin.education.edit', $post)
            ->with('status', 'Artikel edukasi berhasil dibuat.');
    }

    public function edit(EducationPost $education): View
    {
        return view('admin.education.form', [
            'post' => $education,
            'formAction' => route('admin.education.update', $education),
            'formMethod' => 'PUT',
            'pageTitle' => 'Edit Artikel Edukasi',
        ]);
    }

    public function update(
        UpsertEducationPostRequest $request,
        EducationPost $education,
        AuditLogService $auditLogService,
    ): RedirectResponse {
        $this->fillPost($education, $request);
        $education->save();

        $auditLogService->record(
            $request->user(),
            'admin.education.updated',
            $education,
            ['title' => $education->title, 'status' => $education->status->value],
            $request->ip(),
        );

        return redirect()
            ->route('admin.education.edit', $education)
            ->with('status', 'Artikel edukasi berhasil diperbarui.');
    }

    protected function fillPost(EducationPost $post, UpsertEducationPostRequest $request): void
    {
        $status = EducationPostStatus::from($request->validated('status'));
        $slugBase = Str::slug($request->validated('title'));
        $slug = $slugBase;
        $suffix = 2;

        while (EducationPost::query()
            ->where('slug', $slug)
            ->when($post->exists, fn ($query) => $query->whereKeyNot($post->getKey()))
            ->exists()) {
            $slug = "{$slugBase}-{$suffix}";
            $suffix++;
        }

        $coverImagePath = $post->cover_image_path;

        if ($request->hasFile('cover_image')) {
            $storedPath = $request->file('cover_image')->store('education-covers', 'public');
            $coverImagePath = Storage::url($storedPath);
        }

        $post->forceFill([
            'title' => $request->validated('title'),
            'slug' => $slug,
            'excerpt' => $request->validated('excerpt'),
            'body' => $request->validated('body'),
            'status' => $status,
            'published_at' => $status === EducationPostStatus::Published
                ? ($post->published_at ?? now())
                : null,
            'cover_image_path' => $coverImagePath,
            'is_demo_data' => false,
        ]);
    }
}
