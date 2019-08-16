<?php

namespace App\Http\Controllers\Inventory;

use Inertia\Inertia;
use App\Organization;
use Illuminate\Support\Str;
use App\Model\Inventory\Article;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\{Request, Redirect};

class ArticleController extends Controller
{
    /**
     * @param $slug
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index($slug)
    {
        $this->authorize('view', Article::class);

        return Inertia::render('Inventory/Article/Index', [
            'filters' => Request::all('search', 'trashed'),
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
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
     * @param $slug
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function create($slug)
    {
        $this->authorize('create', Article::class);

        return Inertia::render('Inventory/Article/Create', [
            'organization' => Organization::whereSlug($slug)->firstOrFail(),
        ]);
    }

    /**
     * @param $slug
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function store($slug)
    {
        $this->authorize('create', Article::class);

        $organization = Organization::whereSlug($slug)->firstOrFail();

        $organization->articles()->create(
            request()->validate([
                'name' => ['required', 'string', 'min:5', 'max:100'],
                'description' => ['nullable', 'string', 'min:20', 'max:255'],
                'cost' => ['required', 'numeric', 'min:100', 'max:999999']
            ])
        );

        return Redirect::route('inventory.articles', $slug)
            ->with('success', 'Articulo creado correctamente.');
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Article $article
     * @return \Inertia\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit($slug, Article $article)
    {
        $this->authorize('update', $article);

        return Inertia::render('Invoice/Article/Edit', [
            'organization' => Organization::whereSlug($slug)->firstOrFail(),
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
     * @param $slug
     * @param \App\Model\Invoice\Article $article
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update($slug, Article $article)
    {
        $this->authorize('update', $article);

        $article->update(
            request()->validate([
                'name' => ['required', 'string', 'min:5', 'max:100'],
                'description' => ['nullable', 'string', 'min:20', 'max:255'],
                'cost' => ['required', 'numeric', 'min:100', 'max:999999']
            ])
        );

        return Redirect::route('inventory.articles.edit', [
            'slug' => $slug,
            'article' => $article
        ])->with('success', 'Articulo actualizado correctamente.');
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Article $article
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($slug, Article $article)
    {
        $this->authorize('delete', $article);

        $article->delete();
        return Redirect::route('inventory.articles.edit', [
            'slug' => $slug,
            'article' => $article
        ]);
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Article $article
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function restore($slug, Article $article)
    {
        $this->authorize('delete', $article);

        $article->restore();
        return Redirect::route('inventory.articles.edit', [
            'slug' => $slug,
            'article' => $article
        ]);
    }
}
