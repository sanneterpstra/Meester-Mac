<?php

// routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
    $trail->push('Home', route('home.view'));
});

// Home > Shop
Breadcrumbs::for('shop', function (BreadcrumbTrail $trail) {
    $trail->push('Shop', route('shop.view'));
});

// Home > Shop > [Collection]
Breadcrumbs::for('collection', function (BreadcrumbTrail $trail, $collection) {
    $trail->parent('shop');
    foreach ($collection->ancestors as $ancestor) {
        $trail->push($ancestor->translateAttribute('name'), route('collection.view', $ancestor->defaultUrl->slug));
    }
    $trail->push($collection->translateAttribute('name'), route('collection.view', $collection->defaultUrl->slug));
});

// Home > Shop > Collection > Product
Breadcrumbs::for('product', function (BreadcrumbTrail $trail, $product) {
    $trail->parent('shop');
    $collection = $product->collections->where('collection_group_id', 1)->sortBy('_lft')->last();

    foreach ($collection->ancestors as $ancestor) {
        $trail->push($ancestor->translateAttribute('name'), route('collection.view', $ancestor->defaultUrl->slug));
    }

    $trail->push($collection->translateAttribute('name'), route('collection.view', $collection->defaultUrl->slug));

    $trail->push($product->translateAttribute('name'), route('product.view', [$product, $product->variants->first()->sku]));
});
