<?php

use App\Http\Livewire\AboutPage;
use App\Http\Livewire\CheckoutCanceledPage;
use App\Http\Livewire\CheckoutFailedPage;
use App\Http\Livewire\CheckoutOpenPage;
use App\Http\Livewire\CheckoutPage;
use App\Http\Livewire\CheckoutSuccessPage;
use App\Http\Livewire\CollectionPage;
use App\Http\Livewire\ContactPage;
use App\Http\Livewire\HomePage;
use App\Http\Livewire\HulpPage;
use App\Http\Livewire\PaymentMethods;
use App\Http\Livewire\ProductPage;
use App\Http\Livewire\SearchPage;
use App\Http\Livewire\ShipmentAndReturnsPage;
use App\Http\Livewire\ShopPage;
use App\Http\Livewire\TermsPage;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomePage::class)->name('home.view');

Route::get('/over', AboutPage::class)->name('about.view');

Route::get('/contact', ContactPage::class)->name('contact.view');

Route::get('/apple-hulp', HulpPage::class)->name('hulp.view');

Route::get('/shop', ShopPage::class)->name('shop.view');

Route::get('shop/afrekenen', CheckoutPage::class)->name('checkout.view');

Route::get('shop/afrekenen/succesvol', CheckoutSuccessPage::class)->name('checkout-success.view');

Route::get('shop/afrekenen/geannuleerd', CheckoutCanceledPage::class)->name('checkout-canceled.view');

Route::get('shop/afrekenen/mislukt', CheckoutFailedPage::class)->name('checkout-failure.view');

Route::get('shop/afrekenen/open', CheckoutOpenPage::class)->name('checkout-open.view');

Route::get('shop/algemene-voorwaarden', TermsPage::class)->name('terms.view');

Route::get('shop/betaalmethoden', PaymentMethods::class)->name('payment_methods.view');

Route::get('shop/verzending-en-retouren', ShipmentAndReturnsPage::class)->name('shipments-and-returns.view');

Route::get('shop/{slug}', CollectionPage::class)->name('collection.view');

Route::get('shop/product/{slug}/{sku}', ProductPage::class)->name('product.view');

Route::get('search', SearchPage::class)->name('search.view');

// $products = Product::all();
// $csvFileName = 'also.csv';
// $csvFile = resource_path('csv/' . $csvFileName);
// $file_handle = fopen($csvFile, 'r');
// $head = fgetcsv($file_handle, 4096, ';');
// while ($column = fgetcsv($file_handle, 0, ';')) {
//     $distributorProducts[] = array_combine($head, $column);
// }
// fclose($file_handle);

// foreach($products as $product) {
//     $productVariants = $product->variants()->get();
//     foreach($productVariants as $productVariant) {

//         foreach($distributorProducts as $distributorProduct) {
//             if($distributorProduct['ProductID'] === $productVariant->attribute_data['also_product_id']->getValue()) {
//                 $productVariant->mpn = $distributorProduct['ManufacturerPartNumber'];
//                 $productVariant->stock = intval($distributorProduct['AvailableQuantity']);
//                 $productVariant->save();

//                 $pricing['EUR'] = [
//                     'id' => $productVariant->getPrices()->first()->id,
//                     'price' => (0.05*round(($distributorProduct['NetRetailPrice']*1.21)*20)),
//                     'currency_id' => 1,
//                     'tier' => 1,
//                     'compare_price' => 0,
//                 ];
//                 app(UpdatePrices::class)->execute($productVariant, collect($pricing));
//             }
//         }
//         // dd(Pricing::for($productVariant)->get()->matched->price);
//     }

// }
// dd('done');
