<?php

namespace App\Http\Controllers;

use App\Core\Collecting\ModelCollection;
use App\Core\Controller;
use App\Core\Http\Request;
use App\Core\Http\Response;
use App\Core\Template;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the products, optionally filtered by search term.
     */
    public function index(Request $request): Template|Response
    {
        $search = sanitize($request->get('search'));

        $products = collect();

        if ($request->has('search')) {
            if (!empty($search)) {
                // Strategies in order of priority
                $searchStrategies = [
                    fn() => $this->fullTextSearch($search),
                    fn() => $this->fullTextSearch($search, strict: false),
                    fn() => $this->soundexSearch($search),
                    fn() => $this->fuzzySearch($search),
                ];

                // Execute until results are found
                foreach ($searchStrategies as $strategy) {
                    $products = $strategy();

                    if ($products->isNotEmpty()) {
                        break;
                    }
                }
            } else {
                // If search parameter is present but empty, redirect to index
                return redirect()->route('products.index');
            }
        } else {
            // If no search parameter, retrieve all products ordered by name
            $products = (new Product)
                ->query()->with('category')
                ->orderBy('name')->get();
        }

        // Process matched words for "Did You Mean" feature
        if (!empty($products)) {
            [$matched_product_words, $matched_search_words] = $this->collectMatchedWords($products);
            $shouldShowDidYouMean = !empty($matched_product_words) && !empty($matched_search_words);
        }

        // Return the view with all necessary data
        return view('products.index', [
            'title' => 'Products',
            'products' => $products,
            'matched_product_words' => $matched_product_words ?? [],
            'matched_search_words' => $matched_search_words ?? [],
            'shouldShowDidYouMean' => $shouldShowDidYouMean ?? false,
        ]);
    }

    /**
     * Perform a full-text search on the 'name' and 'description' columns.
     */
    private function fullTextSearch(string $search, bool $strict = true): ModelCollection
    {
        $terms = explode(' ', $search);
        $boolean_search = $strict ? '+' . implode(' +', $terms) : implode(' ', $terms);

        return (new Product)
            ->query()
            ->with('category')
            ->selectRaw("products.*, MATCH(products.name, products.description) AGAINST('{$boolean_search}') AS relevance")
            ->from('products')
            ->whereRaw("MATCH(products.name, products.description) AGAINST('{$boolean_search}' IN BOOLEAN MODE)")
            ->orderBy('relevance', 'DESC')
            ->get();
    }

    /**
     * Perform a SOUNDEX search for the entire search term on the 'name' column.
     */
    private function soundexSearch(string $search): ModelCollection
    {
        return (new Product)
            ->query()
            ->with('category')
            ->selectRaw("products.*, LEFT(SOUNDEX(products.name), 4) AS matched_soundex, LEFT(SOUNDEX('{$search}'), 4) AS search_soundex")
            ->whereRaw("LEFT(SOUNDEX(products.name), 8) = LEFT(SOUNDEX('{$search}'), 8)")
            ->orderBy('products.name')
            ->get()
            ->map(function ($product) use ($search) {
                $product->matched_product_words = $product->name;
                $product->matched_search_words = $search;
                return $product;
            });
    }

    private function fuzzySearch(string $search): ModelCollection
    {
        return (new Product)
            ->query()
            ->addRaw("
                            WITH RECURSIVE seq AS (
                            SELECT 1 AS n
                            UNION ALL
                            SELECT n + 1 FROM seq WHERE n < 10
                        )")
            ->addRaw("
                            product_words AS (
                            SELECT
                                p.id AS product_id,
                                p.name AS product_name,
                                TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(p.name, ' ', seq.n), ' ', -1)) AS word,
                                seq.n AS word_position
                            FROM
                                products p
                            JOIN seq ON seq.n <= 1 + (CHAR_LENGTH(p.name) - CHAR_LENGTH(REPLACE(p.name, ' ', '')))
                        )")
            ->addRaw("
                            search_terms AS (
                            SELECT '{$search}' AS search_term
                        )")
            ->addRaw("
                            search_words AS (
                            SELECT
                                TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(search_term, ' ', seq.n), ' ', -1)) AS word,
                                seq.n AS word_position
                            FROM
                                search_terms
                            JOIN seq ON seq.n <= 1 + (CHAR_LENGTH(search_term) - CHAR_LENGTH(REPLACE(search_term, ' ', '')))
                        )")
            ->addRaw("
                            search_word_count AS (
                            SELECT
                                COUNT(*) AS total_search_words
                            FROM
                                search_words
                        )")
            ->addRaw("
                            product_word_counts AS (
                            SELECT
                                product_id,
                                COUNT(*) AS total_product_words
                            FROM
                                product_words
                            GROUP BY
                                product_id
                        )")
            ->addRaw("
                            matching_words AS (
                            SELECT
                                pw.product_id,
                                COUNT(DISTINCT pw.word) AS matching_word_count,
                                GROUP_CONCAT(DISTINCT pw.word ORDER BY pw.word SEPARATOR ', ') AS matched_product_words,
                                GROUP_CONCAT(DISTINCT sw.word ORDER BY sw.word SEPARATOR ', ') AS matched_search_words
                            FROM
                                product_words pw
                            -- JOIN search_words sw ON SOUNDEX(pw.word) = SOUNDEX(sw.word)
                            JOIN search_words sw ON LEFT(SOUNDEX(pw.word), 4) = LEFT(SOUNDEX(sw.word), 4)
                            GROUP BY
                                pw.product_id
                        )")
            ->addRaw("
                            product_matches AS (
                            SELECT
                                pwc.product_id,
                                pwc.total_product_words,
                                swc.total_search_words,
                                COALESCE(mw.matching_word_count, 0) AS matching_word_count,
                                COALESCE(mw.matched_product_words, '') AS matched_product_words,
                                COALESCE(mw.matched_search_words, '') AS matched_search_words,
                                COALESCE(mw.matching_word_count, 0) / swc.total_search_words AS match_ratio
                            FROM
                                product_word_counts pwc
                            CROSS JOIN search_word_count swc
                            LEFT JOIN matching_words mw ON pwc.product_id = mw.product_id
                        )")
            ->selectRaw("p.*,
                            pm.matching_word_count,
                            pm.total_product_words,
                            pm.total_search_words,
                            pm.match_ratio,
                            pm.matched_product_words,
                            pm.matched_search_words")
            ->from('products p')
            ->join('product_matches pm', 'p.id', '=', 'pm.product_id')
            ->whereRaw("pm.match_ratio >= CASE
                                WHEN pm.total_search_words = 1 THEN 0.1
                                WHEN pm.total_search_words = 2 THEN 0.4
                                WHEN pm.total_search_words = 3 THEN 0.5
                                ELSE 0.6
                            END")
            ->orderBy('pm.match_ratio', 'DESC')
            ->orderBy('p.name')
            ->get()
            ->load('category');
    }

    private function collectMatchedWords(ModelCollection $products): array
    {
        $list = [
            'matched_product_words' => [],
            'matched_search_words' => [],
        ];

        foreach ($products as $product) {
            $list['matched_product_words'][] = $product->matched_product_words;
            $list['matched_search_words'][] = $product->matched_search_words;
        }

        return [
            array_unique(array_filter($list['matched_product_words'])),
            array_unique(array_filter($list['matched_search_words'])),
        ];
    }

    /**
     * Display the specified product.
     */
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