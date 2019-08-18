<?php

namespace App\Presenters\Article;

use App\Organization;
use App\Model\Inventory\Article;


class UrlPresenter
{
    /**
     * @var \App\Organization
     */
    private $organization;
    /**
     * @var \App\Model\Inventory\Article
     */
    private $article;

    /**
     * UrlPresenter constructor.
     *
     * @param \App\Organization $organization
     * @param \App\Model\Inventory\Article $article
     */
    public function __construct(Organization $organization, Article $article)
    {
        $this->organization = $organization;
        $this->article = $article;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if(method_exists($this, $key))
        {
            return $this->$key();
        }
        return $this->$key;
    }

    /**
     * @return string
     */
    public function edit()
    {
        return route('inventory.articles.edit', [
            'organization' => $this->organization,
            'article' => $this->article
        ]);
    }

    /**
     * @return string
     */
    public function update()
    {
        return route('inventory.articles.update',[
            'organization' => $this->organization,
            'article' => $this->article
        ]);
    }

    /**
     * @return string
     */
    public function delete()
    {
        return route('inventory.articles.destroy', [
            'organization' => $this->organization,
            'article' => $this->article
        ]);
    }

    /**
     * @return string
     */
    public function restore()
    {
        return route('inventory1.articles.destroy', [
            'organization' => $this->organization,
            'article' => $this->article
        ]);
    }

}
