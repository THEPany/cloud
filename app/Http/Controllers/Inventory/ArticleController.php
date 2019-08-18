<?php

namespace App\Http\Controllers\Inventory;

use Inertia\Inertia;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\{Organization, Model\Inventory\Article};
use Illuminate\Support\Facades\{Request, Redirect};
use App\Http\Requests\{StoreArticleRequest, UpdateArticleRequest};

class ArticleController extends Controller
{
    /**
     * @param \App\Organization $organization
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(Organization $organization)
    {
        $this->authorize('view', Article::class);

        return Inertia::render('Inventory/Article/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organization' => $organization,
            'articles' => $organization->articles()
                ->orderBy('name')
                ->filter(Request::only('search', 'trashed'))
                ->paginate()
                ->only('id', 'name', 'cost', 'description', 'deleted_at')
                ->transform(function ($item) {
                    return array_merge($item, ['description' => Str::limit($item['description'], 50, '...')]);
                })
        ]);
    }

    /**
     * @param \App\Organization $organization
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create(Organization $organization)
    {
        $this->authorize('create', Article::class);

        return Inertia::render('Inventory/Article/Create', [
            'organization' => $organization,
        ]);
    }

    /**
     * @param \App\Http\Requests\StoreArticleRequest $request
     * @param \App\Organization $organization
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store(StoreArticleRequest $request, Organization $organization)
    {
        $this->authorize('create', Article::class);

        $organization->articles()->create($request->validated());

        return Redirect::route('inventory.articles', $organization)->with('success', __('article.created'));
    }

    /**
     * @param \App\Organization $organization
     * @param \App\Model\Inventory\Article $article
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(Organization $organization, Article $article)
    {
        $this->authorize('update', $article);

        return Inertia::render('Invoice/Article/Edit', [
            'organization' => $organization,
            'article' => [
                'id' => $article->id,
                'name' => $article->name,
                'cost' => $article->cost,
                'description' => $article->description,
                'deleted_at' => $article->deleted_at,
            ],
        ]);
    }

    /**
     * @param \App\Http\Requests\UpdateArticleRequest $request
     * @param \App\Organization $organization
     * @param \App\Model\Inventory\Article $article
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateArticleRequest $request, Organization $organization, Article $article)
    {
        $this->authorize('update', $article);

        $article->update($request->validated());

        return Redirect::to($article->url->edit)->with('success', __('article.updated'));
    }

    /**
     * @param \App\Organization $organization
     * @param \App\Model\Inventory\Article $article
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Organization $organization, Article $article)
    {
        $this->authorize('delete', $article);

        $article->delete();

        return Redirect::to($article->url->edit);
    }

    /**
     * @param \App\Organization $organization
     * @param \App\Model\Inventory\Article $article
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore(Organization $organization, Article $article)
    {
        $this->authorize('delete', $article);

        $article->restore();

        return Redirect::to($article->url->edit);
    }
}
