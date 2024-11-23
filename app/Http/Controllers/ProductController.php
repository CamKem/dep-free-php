<?php

namespace App\Http\Controllers;

use App\Core\Collecting\ModelCollection;
use App\Core\Controller;
use App\Core\Database\Database;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Models\Product;

class ProductController extends Controller
{

    public function index(Request $request): Template|Response
    {
        $products = (new Product())
            ->query()
            ->with('category')
            ->orderBy('name');

        if ($request->has('search')) {
            $search = trim($request->get('search'));

            if (empty($search)) {
                return redirect()->route('products.index');
            }

            $terms = explode(' ', $search);
            $boolean_search = '+' . implode(' +', $terms);
            $boolean_search = "'{$boolean_search}'";

            // TODO: Implement full-text search using the description field too
            $products = $products
                ->whereRaw("MATCH(products.name) AGAINST({$boolean_search} IN BOOLEAN MODE)")
                ->get();

            if ($products->isEmpty()) {
                // No results found, perform SOUNDEX search on the full string
                $products = (new Product())
                    ->query()
                    ->with('category')
                    ->orderBy('name')
                    ->whereRaw("SOUNDEX(products.name) LIKE SOUNDEX('{$search}')")
                    ->get();

                if ($products->isEmpty()) {
                    // No results found, perform individual word search, using recursive CTE
                    $sql = <<<SQL
                    WITH RECURSIVE seq AS (
                        SELECT 1 AS n
                        UNION ALL
                        SELECT n + 1 FROM seq WHERE n < 10
                    ),
                    product_words AS (
                        SELECT
                            p.id AS product_id,
                            p.name AS product_name,
                            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(p.name, ' ', seq.n), ' ', -1)) AS word,
                            seq.n AS word_position
                        FROM
                            products p
                        JOIN seq ON seq.n <= 1 + (CHAR_LENGTH(p.name) - CHAR_LENGTH(REPLACE(p.name, ' ', '')))
                    ),
                    search_terms AS (
                        /* SELECT ':search' AS search_term */
                        SELECT '{$search}' AS search_term
                    ),
                    search_words AS (
                        SELECT
                            TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(search_term, ' ', seq.n), ' ', -1)) AS word,
                            seq.n AS word_position
                        FROM
                            search_terms
                        JOIN seq ON seq.n <= 1 + (CHAR_LENGTH(search_term) - CHAR_LENGTH(REPLACE(search_term, ' ', '')))
                    ),
                    product_word_counts AS (
                        SELECT
                            product_id,
                            COUNT(*) AS total_words
                        FROM
                            product_words
                        GROUP BY
                            product_id
                    ),
                    matching_words AS (
                        SELECT
                            pw.product_id,
                            COUNT(DISTINCT pw.word) AS matching_word_count
                        FROM
                            product_words pw
                        JOIN search_words sw ON SOUNDEX(pw.word) = SOUNDEX(sw.word)
                        GROUP BY
                            pw.product_id
                    ),
                    product_matches AS (
                        SELECT
                            pwc.product_id,
                            pwc.total_words,
                            COALESCE(mw.matching_word_count, 0) AS matching_word_count,
                            COALESCE(mw.matching_word_count, 0) / pwc.total_words AS match_ratio
                        FROM
                            product_word_counts pwc
                        LEFT JOIN matching_words mw ON pwc.product_id = mw.product_id
                    )
                    
                    SELECT
                        p.*,
                        pm.matching_word_count,
                        pm.total_words,
                        pm.match_ratio
                    FROM
                        products p
                    JOIN product_matches pm ON p.id = pm.product_id
                    WHERE
                        pm.match_ratio >= 0.6
                    ORDER BY
                        pm.match_ratio DESC,
                        p.name;
                SQL;

                    $results = app(Database::class)
                        ->query($sql)
                        ->get();

                    if (!empty($results)) {
                        $products = new ModelCollection;
                        foreach ($results as $result) {
                            $products->add((new Product($result)));
                        }
                    }

                    $products->load('category');
                }
            }
        }

        return view("products.index", [
            'title' => 'Products',
            'products' => $products instanceof ModelCollection ? $products : $products->get(),
        ]);
    }

    public function show(Request $request): Template
    {
        $product = (new Product())
            ->query()
            ->with('category')
            ->where('slug', $request->get('product'))
            ->first();

        if (!$product) {
            return abort();
        }

        return view("products.show", [
            'title' => 'Product',
            'product' => $product,
        ]);
    }

}