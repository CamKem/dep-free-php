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
                        search_word_count AS (
                            SELECT
                                COUNT(*) AS total_search_words
                            FROM
                                search_words
                        ),
                        product_word_counts AS (
                            SELECT
                                product_id,
                                COUNT(*) AS total_product_words
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
                            -- JOIN search_words sw ON SOUNDEX(pw.word) = SOUNDEX(sw.word)
                            -- we are only matching the first 4 characters of the SOUNDEX value
                            JOIN search_words sw ON LEFT(SOUNDEX(pw.word), 4) = LEFT(SOUNDEX(sw.word), 4)
                            GROUP BY
                                pw.product_id
                        ),
                        product_matches AS (
                            SELECT
                                pwc.product_id,
                                pwc.total_product_words,
                                swc.total_search_words,
                                COALESCE(mw.matching_word_count, 0) AS matching_word_count,
                                COALESCE(mw.matching_word_count, 0) / swc.total_search_words AS match_ratio
                            FROM
                                product_word_counts pwc
                            CROSS JOIN search_word_count swc
                            LEFT JOIN matching_words mw ON pwc.product_id = mw.product_id
                        )
                        SELECT
                            p.*,
                            pm.matching_word_count,
                            pm.total_product_words,
                            pm.total_search_words,
                            pm.match_ratio
                        FROM
                            products p
                        JOIN product_matches pm ON p.id = pm.product_id
                        WHERE
                            pm.match_ratio >= CASE
                                WHEN pm.total_search_words = 1 THEN 0.1
                                WHEN pm.total_search_words = 2 THEN 0.4
                                WHEN pm.total_search_words = 3 THEN 0.5
                                ELSE 0.6
                            END
                        ORDER BY
                            pm.match_ratio DESC,
                            p.name ASC;
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