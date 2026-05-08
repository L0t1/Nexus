<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\AutoTagRule;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TagController extends Controller
{
    /**
     * List all tags for the merchant.
     */
    public function index(Request $request): Response
    {
        $merchant = $request->user()->merchant;

        $tags = $merchant->tags()
            ->withCount('contacts')
            ->latest()
            ->get();

        $autoRules = AutoTagRule::where('merchant_id', $merchant->id)
            ->with('tag:id,name,color')
            ->get();

        return Inertia::render('Tags/Index', [
            'tags'      => $tags,
            'autoRules' => $autoRules,
        ]);
    }

    /**
     * Create a new tag.
     */
    public function store(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $merchant->tags()->create($validated);

        return back()->with('success', "Tag \"{$validated['name']}\" created.");
    }

    /**
     * Update a tag.
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        abort_unless($tag->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'name'  => 'required|string|max:50',
            'color' => 'required|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $tag->update($validated);

        return back()->with('success', 'Tag updated.');
    }

    /**
     * Delete a tag.
     */
    public function destroy(Request $request, Tag $tag): RedirectResponse
    {
        abort_unless($tag->merchant_id === $request->user()->merchant?->id, 403);

        $tag->contacts()->detach(); // Remove from all contacts
        $tag->delete();

        return back()->with('success', 'Tag deleted.');
    }

    /**
     * Toggle a tag on a contact.
     */
    public function toggleContact(Request $request, Contact $contact): RedirectResponse
    {
        abort_unless($contact->merchant_id === $request->user()->merchant?->id, 403);

        $validated = $request->validate([
            'tag_id' => 'required|exists:tags,id',
        ]);

        $tag = Tag::findOrFail($validated['tag_id']);
        abort_unless($tag->merchant_id === $request->user()->merchant?->id, 403);

        $contact->tags()->toggle($validated['tag_id']);

        return back()->with('success', 'Tag updated.');
    }

    /**
     * Create an auto-tag rule.
     */
    public function storeRule(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $validated = $request->validate([
            'tag_id'   => 'required|exists:tags,id',
            'field'    => 'required|in:total_spent,total_orders,city,platform',
            'operator' => 'required|in:>,>=,<,<=,=,!=',
            'value'    => 'required|string|max:255',
        ]);

        // Verify tag belongs to this merchant
        $tag = Tag::findOrFail($validated['tag_id']);
        abort_unless($tag->merchant_id === $merchant->id, 403);

        AutoTagRule::create([...$validated, 'merchant_id' => $merchant->id]);

        return back()->with('success', 'Auto-tag rule created.');
    }

    /**
     * Delete an auto-tag rule.
     */
    public function destroyRule(Request $request, AutoTagRule $rule): RedirectResponse
    {
        abort_unless($rule->merchant_id === $request->user()->merchant?->id, 403);

        $rule->delete();

        return back()->with('success', 'Auto-tag rule deleted.');
    }

    /**
     * Run auto-tag rules now (manual trigger).
     */
    public function runRules(Request $request): RedirectResponse
    {
        $merchant = $request->user()->merchant;

        $rules = AutoTagRule::where('merchant_id', $merchant->id)
            ->where('is_active', true)
            ->with('tag')
            ->get();

        if ($rules->isEmpty()) {
            return back()->with('error', 'No active auto-tag rules found.');
        }

        $tagged = 0;

        $merchant->contacts()->chunk(200, function ($contacts) use ($rules, &$tagged) {
            foreach ($contacts as $contact) {
                foreach ($rules as $rule) {
                    if ($rule->matches($contact)) {
                        if (!$contact->tags()->where('tag_id', $rule->tag_id)->exists()) {
                            $contact->tags()->attach($rule->tag_id);
                            $tagged++;
                        }
                    }
                }
            }
        });

        return back()->with('success', "{$tagged} contact(s) tagged by auto-tag rules.");
    }
}
