<?php

namespace App\Http\Controllers;

use App\Models\ReplyTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReplyTemplateController extends Controller
{
    /**
     * List templates (Inertia page).
     */
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        $templates = $merchant->replyTemplates()
            ->orderByDesc('usage_count')
            ->get();

        return Inertia::render('Templates/Index', [
            'templates' => $templates,
        ]);
    }

    /**
     * JSON endpoint for the chat widget to search templates by shortcut.
     */
    public function search(Request $request): JsonResponse
    {
        $merchant = $request->user()->merchant;
        $query = $request->input('q', '');

        $templates = $merchant->replyTemplates()
            ->when($query, function ($q) use ($query) {
                $q->where('shortcut', 'like', "%{$query}%")
                  ->orWhere('name', 'like', "%{$query}%");
            })
            ->orderByDesc('usage_count')
            ->take(10)
            ->get(['id', 'shortcut', 'name', 'body', 'category']);

        return response()->json($templates);
    }

    /**
     * Create a template.
     */
    public function store(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'shortcut' => 'required|string|max:50|regex:/^\/[a-z0-9_]+$/',
            'name'     => 'required|string|max:100',
            'body'     => 'required|string|max:2000',
            'category' => 'nullable|in:greeting,pricing,shipping,returns,custom',
        ]);

        $merchant->replyTemplates()->create($validated);

        return back()->with('success', "Template \"{$validated['name']}\" created.");
    }

    /**
     * Update a template.
     */
    public function update(Request $request, ReplyTemplate $template): RedirectResponse
    {
        abort_unless($template->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'shortcut' => 'required|string|max:50|regex:/^\/[a-z0-9_]+$/',
            'name'     => 'required|string|max:100',
            'body'     => 'required|string|max:2000',
            'category' => 'nullable|in:greeting,pricing,shipping,returns,custom',
        ]);

        $template->update($validated);

        return back()->with('success', 'Template updated.');
    }

    /**
     * Delete a template.
     */
    public function destroy(Request $request, ReplyTemplate $template): RedirectResponse
    {
        abort_unless($template->merchant_id === $request->user()->merchant?->id, 403);

        $template->delete();

        return back()->with('success', 'Template deleted.');
    }

    /**
     * Increment usage counter (called from chat when template is used).
     */
    public function use(Request $request, ReplyTemplate $template): JsonResponse
    {
        abort_unless($template->merchant_id === $request->user()->merchant?->id, 403);

        $template->increment('usage_count');

        return response()->json(['ok' => true]);
    }
}
