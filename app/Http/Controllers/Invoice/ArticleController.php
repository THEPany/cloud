<?php

namespace App\Http\Controllers\Invoice;

use Inertia\Inertia;
use App\Organization;
use Illuminate\Support\Str;
use App\Model\Invoice\Article;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Redirect;

class ArticleController extends Controller
{
    /**
     * @param $slug
     * @return \Inertia\Response
     */
    public function index($slug)
    {
        return Inertia::render('Invoice/Article/Index', [
            'organization' => $organization = Organization::whereSlug($slug)->firstOrFail(),
            'filters' => Request::all('search', 'trashed'),
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
     */
    public function create($slug)
    {
        return Inertia::render('Invoice/Article/Create', [
            'organization' => Organization::whereSlug($slug)->firstOrFail(),
        ]);
    }

    /**
     * @param $slug
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($slug)
    {
        $organization = Organization::whereSlug($slug)->firstOrFail();

        $organization->articles()->create(
            request()->validate([
                'name' => ['required', 'string', 'min:5', 'max:100'],
                'description' => ['nullable', 'string', 'min:20', 'max:255'],
                'cost' => ['required', 'numeric', 'min:100', 'max:999999']
            ])
        );

        return Redirect::route('invoice.articles.index', $slug)
            ->with('success', 'Articulo creado correctamente.');
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Article $article
     * @return \Inertia\Response
     */
    public function edit($slug, Article $article)
    {
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
     */
    public function update($slug, Article $article)
    {
        $article->update(
            request()->validate([
                'name' => ['required', 'string', 'min:5', 'max:100'],
                'description' => ['nullable', 'string', 'min:20', 'max:255'],
                'cost' => ['required', 'numeric', 'min:100', 'max:999999']
            ])
        );

        return Redirect::route('invoice.articles.edit', [
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
        $article->delete();
        return Redirect::route('invoice.articles.edit', [
            'slug' => $slug,
            'article' => $article
        ]);
    }

    /**
     * @param $slug
     * @param \App\Model\Invoice\Article $article
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($slug, Article $article)
    {
        $article->restore();
        return Redirect::route('invoice.articles.edit', [
            'slug' => $slug,
            'article' => $article
        ]);
    }
}
