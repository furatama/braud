<?php

//Route untuk authentikasi user 
Route::group([
    'prefix' => 'auth'
], function ($router) {
    Route::post('login', 'AuthController@login');
    Route::group(['middleware' => ['jwt.verify']], function() { 
        Route::post('logout', 'AuthController@logout');
        Route::post('refresh', 'AuthController@refresh');
        Route::post('me', 'AuthController@me');
        Route::post('payload', 'AuthController@payload');
    
        Route::post('update-info', 'AuthController@updateInfo');
        Route::post('update-password', 'AuthController@updatePassword');
    });
});

//Tambah route dibawah sini

//taruh Route di luar sini untuk yang tidak perlu authentikasi
//Route::get('data', 'DataController@data');

//Buat beberapa route:group untuk hak akses yang berbeda2 pisahkan dengan | jika ada 2 atau lebih hak akses yang dapat menggunakannya
//Route::group(['middleware' => ['jwt.verify:<HAK_AKSES>']], function ()) {....

Route::group(['middleware' => ['jwt.verify:admin']], function() {

    Route::get('user/data', 'UserController@index');
    Route::post('user/data', 'UserController@store');
    Route::get('user/data/{id}', 'UserController@show');
    Route::put('user/data/{id}', 'UserController@update');
    Route::delete('user/data/{id}', 'UserController@destroy');

});

Route::group(['middleware' => ['jwt.verify']], function() { 

    Route::get('customer/data', 'CustomerController@index');
    Route::get('customer/aktif', 'CustomerController@indexAktif');
    Route::post('customer/data', 'CustomerController@store');
    Route::get('customer/data/{id}', 'CustomerController@show');
    Route::put('customer/data/{id}', 'CustomerController@update');
    Route::delete('customer/data/{id}', 'CustomerController@destroy');

    Route::get('customer/{id}/harga', 'CustomerController@showHarga');
    Route::post('customer/{id}/harga', 'CustomerController@storeHarga');

    Route::get('produk/data', 'ProdukController@index');
    Route::post('produk/data', 'ProdukController@store');
    Route::get('produk/data/{id}', 'ProdukController@show');
    Route::put('produk/data/{id}', 'ProdukController@update');
    Route::delete('produk/data/{id}', 'ProdukController@destroy');
    Route::get('produk/detail', 'ProdukController@indexWithDetail');
    Route::get('produk/detail/{id}', 'ProdukController@showWithDetail');

    Route::get('produk/aktif', 'ProdukController@indexAktif');
    Route::get('produk/all', 'ProdukController@indexAll');
    Route::get('produk/customer/{id_customer}', 'ProdukController@byCustomer');

    Route::get('harga/data', 'HargaController@index');
    Route::post('harga/data', 'HargaController@store');
    Route::get('harga/data/{id}', 'HargaController@show');
    Route::put('harga/data/{id}', 'HargaController@update');
    Route::delete('harga/data/{id}', 'HargaController@destroy');

    Route::get('satuan/data', 'SatuanController@index');
    Route::post('satuan/data', 'SatuanController@store');
    Route::get('satuan/data/{id}', 'SatuanController@show');
    Route::put('satuan/data/{id}', 'SatuanController@update');
    Route::delete('satuan/data/{id}', 'SatuanController@destroy');

    Route::get('kategori/data', 'KategoriController@index');
    Route::post('kategori/data', 'KategoriController@store');
    Route::get('kategori/data/{id}', 'KategoriController@show');
    Route::put('kategori/data/{id}', 'KategoriController@update');
    Route::delete('kategori/data/{id}', 'KategoriController@destroy');

    Route::get('order/data', 'OrderController@index');
    Route::post('order/data', 'OrderController@store');
    Route::get('order/data/{id}', 'OrderController@show');
    Route::put('order/data/{id}', 'OrderController@update');
    Route::delete('order/data/{id}', 'OrderController@destroy');
    Route::get('order/detail', 'OrderController@indexWithDetail');
    Route::get('order/detail/{id}', 'OrderController@showWithDetail');
    Route::get('order/kredit', 'OrderController@indexCredit');

    Route::get('order/num', 'OrderController@numOrder');

    Route::get('order-detail/data/{id}', 'OrderDetailController@byIDOrder');

    Route::get('kredit/data', 'KreditController@index');
    Route::post('kredit/data', 'KreditController@store');
    Route::get('kredit/data/{id}', 'KreditController@show');
    Route::put('kredit/data/{id}', 'KreditController@update');
    Route::delete('kredit/data/{id}', 'KreditController@destroy');

    Route::get('kredit/order/{id_order}', 'KreditController@showByIDOrder');

    Route::get('report/customer','ReportController@customer');
    Route::get('report/produk','ReportController@produk');
    Route::get('report/order','ReportController@order');

    Route::get('report/customer/excel','ReportController@customerExcel');
    Route::get('report/produk/excel','ReportController@produkExcel');
    Route::get('report/order/excel','ReportController@orderExcel');

});