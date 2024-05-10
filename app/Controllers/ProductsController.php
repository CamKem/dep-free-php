<?php

namespace App\Controllers;

use App\Core\Controller;
use app\Core\Database\Database;
use App\Core\Http\Request;
use App\Core\View;

class ProductsController extends Controller
{

    protected Database $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = app(Database::class);
    }


    public function index(): View
    {
        return view("products.index", [
            'title' => 'Products',
        ]);
    }

    public function show(Request $request): View
    {
        return view("products.show", [
            'title' => 'Product',
            'product' => $this->db->query(
                'select * from products where id = :id',
                ['id' => $request->route('product')]
            )->get(),
        ]);
    }

}